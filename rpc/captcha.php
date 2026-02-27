<?php
include_once('../config/symbini.php');

if(empty($CAPTCHA_ENDPOINT)){
	header('HTTP/1.0 403 Forbidden');
    exit();
}

require_once $SERVER_ROOT . '/vendor/capito/src/Cap.php';
require_once $SERVER_ROOT . '/vendor/capito/src/Interfaces/StorageInterface.php';
//require_once $SERVER_ROOT . '/vendor/capito/src/Storage/MysqlStorage.php';
//require_once $SERVER_ROOT . '/vendor/capito/src/Storage/SqliteStorage.php';
require_once $SERVER_ROOT . '/vendor/capito/src/Storage/FileStorage.php';
//require_once $SERVER_ROOT . '/vendor/capito/src/Storage/RedisStorage.php';
require_once $SERVER_ROOT . '/vendor/capito/src/RateLimiter.php';
require_once $SERVER_ROOT . '/vendor/capito/src/Exceptions/CapException.php';

use Capito\CapPhpServer\Cap;
//use Capito\CapPhpServer\Storage\MysqlStorage;
//use Capito\CapPhpServer\Storage\SqliteStorage;
use Capito\CapPhpServer\Storage\FileStorage;
//use Capito\CapPhpServer\Storage\RedisStorage;
use Capito\CapPhpServer\Exceptions\CapException;

$capServer = new Cap([
    'challengeCount' => 3,          // 3 challenges (1–3 seconds to solve)   [== 5 higher sec]
    'challengeSize' => 16,          // 16-byte salt    
    'bruteForceLimit' => 3,         // 3 requests max per window              [==5 default limit]
    'bruteForceWindow' => 60,       // 60 second time window                  [==30 shorter window]
    'bruteForcePenalty' => 60,      // 60 second penalty when blocked         [==120 longer penalty]
    'challengeDifficulty' => 2,     // Difficulty 2 (balanced optimization)  [==3 hard]                     
    'difficultyModerate'=>3,      	// Difficulty level when moderate rate limiting pressure detected
    'difficultyAggressive'=>5,      // Difficulty level when high limiting pressure detected
    'tokenVerifyOnce' => true,      // One-time validation
    'challengeExpires' => 300,      // Expires in 5 minutes
    'tokenExpires' => 600,          // Token expires in 10 minutes  
    'storage' => new FileStorage(['path' => $TEMP_DIR_ROOT . '/cap_storage.json']) 
    //'storage' => new SqliteStorage(['path' => __DIR__ . '/../.data/cap_data.sqlite'])
    //'storage' => new MysqlStorage([
    //    'host'     => 'localhost',
    //    'dbname'   => 'your_database_name',
    //    'username' => 'your_username',
    //    'password' => 'your_password',
        // Optional: table name, defaults to 'cap_tokens'
    //    'table'    => 'cap_tokens'
    //])
]);
// Get request path and client IP
$requestPath = $_SERVER['PATH_INFO'];    
$clientIP = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'] ?? '')[0]
         ?: $_SERVER['HTTP_X_REAL_IP'] 
         ?? $_SERVER['REMOTE_ADDR'] 
         ?? null;

// Set CORS headers (applies to all responses)
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle OPTIONS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// HTTP routing - with modern error handling
try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === '/challenge') {
        handleChallenge($capServer, $clientIP);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === '/redeem') {
        handleRedeem($capServer, $clientIP);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && $requestPath === ADMINCapPath.'/validate') {
        handleValidate($capServer, $clientIP);
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET' && $requestPath === ADMINCapPath.'/stats') {
        handleStats($capServer);
    } else {
        http_response_code(404);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Not Found', 'path' => $requestPath]);
    }
} catch (CapException $e) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'code' => $e->getCode(),
        'type' => 'CapException'
    ]);
} catch (Exception $e) {
    error_log("Server error: " . $e->getMessage());
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode([
        'success' => false,
        'error' => 'Internal server error',
        'type' => 'ServerException'
    ]);
}

/**
 * Handle challenge creation request - using new architecture
 * @param Cap $capServer Cap server instance
 * @param string $clientIP Client IP address
 */
function handleChallenge(Cap $capServer, ?string $clientIP=null)
{
    header('Content-Type: application/json');
    
    try {
        // Use new method signature; supports rate limiting and client IP
        $challenge = $capServer->createChallenge($clientIP);
        echo json_encode($challenge);
    } catch (CapException $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'type' => 'CapException'
        ]);
    } catch (Exception $e) {
        error_log("Challenge creation failed: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to create challenge',
            'type' => 'ServerException'
        ]);
    }
}

/**
 * Handle solution verification request - using new architecture
 * @param Cap $capServer Cap server instance
 * @param string $clientIP Client IP address
 */
function handleRedeem(Cap $capServer, ?string $clientIP=null)
{
    header('Content-Type: application/json');
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        return;
    }
    // Validate required parameters
    if (!isset($input['token']) || $input['token'] === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Token is required']);
        return;
    }
    if (!isset($input['solutions']) || !is_array($input['solutions']) || count($input['solutions']) === 0) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Valid solutions array is required']);
        return;
    }
    try {
        // Use new method signature; supports rate limiting and client IP
        $result = $capServer->redeemChallenge($input, $clientIP);
        echo json_encode($result);
    } catch (CapException $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'type' => 'CapException'
        ]);
    } catch (Exception $e) {
        error_log("Solution verification failed: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to redeem challenge',
            'type' => 'ServerException'
        ]);
    }
}

/**
 * Handle token validation request - using new architecture
 * @param Cap $capServer Cap server instance
 * @param string $clientIP Client IP address
 */
function handleValidate(Cap $capServer, ?string $clientIP=null)
{
    header('Content-Type: application/json');
    // Get JSON input
    $input = json_decode(file_get_contents('php://input'), true);
    if ($input === null) {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Invalid JSON']);
        return;
    }
    // Validate required parameters
    if (!isset($input['token']) || $input['token'] === '') {
        http_response_code(400);
        echo json_encode(['success' => false, 'error' => 'Token is required']);
        return;
    }
    try {
        // Use new method signature; supports rate limiting and client IP
        $result = $capServer->validateToken($input['token'], null, $clientIP);
        echo json_encode($result);
    } catch (CapException $e) {
        http_response_code(400);
        echo json_encode([
            'success' => false,
            'error' => $e->getMessage(),
            'code' => $e->getCode(),
            'type' => 'CapException'
        ]);
    } catch (Exception $e) {
        error_log("Token validation failed: " . $e->getMessage());
        http_response_code(500);
        echo json_encode([
            'success' => false,
            'error' => 'Failed to validate token',
            'type' => 'ServerException'
        ]);
    }
}

/**
 * Handle statistics request - using new architecture
 * @param Cap $capServer Cap server instance
 */
function handleStats(Cap $capServer)
{
    header('Content-Type: application/json');
    
    try {
        // Use the new statistics interface
        $stats = $capServer->getStats();
        echo json_encode($stats, JSON_PRETTY_PRINT);
    } catch (Exception $e) {
        http_response_code(500);
        echo json_encode([
            'error' => 'Failed to get stats: ' . $e->getMessage(),
            'timestamp' => time()
        ]);
    }
}

?>


