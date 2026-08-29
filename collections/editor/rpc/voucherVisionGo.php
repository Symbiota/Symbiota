<?php
// Pass-through API endpoint for Vouchervision-Go OCR and Transcription
// This allows the Vouchervision-Go API key to remain private

include_once('../../../config/symbini.php');

// if(empty($VOUCHERVISION_API_URL)){
//     http_response_code(500);
//     header('Content-Type: application/json');
//     echo json_encode(['error' => 'VoucherVision API URL is not configured.']);
//     exit;
// }

$voucherVisionUrl = trim($VOUCHERVISION_API_URL);
if(!preg_match('/\/process-url\/?$/', $voucherVisionUrl)){
    $voucherVisionUrl = preg_replace('/\/process\/?$/', '', rtrim($voucherVisionUrl, '/'));
    $voucherVisionUrl .= '/process-url';
}

// Get JSON data passed to this page
$jsonData = file_get_contents('php://input');
$requestData = json_decode($jsonData, true);
if(!is_array($requestData)){
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid request payload.']);
    exit;
}

$authToken = '';
$geminiApiKey = '';
if(array_key_exists('auth_token', $requestData) && is_string($requestData['auth_token'])){
    $authToken = trim($requestData['auth_token']);
}
if(array_key_exists('gemini_api_key', $requestData) && is_string($requestData['gemini_api_key'])){
    $geminiApiKey = trim($requestData['gemini_api_key']);
}
unset($requestData['auth_token']);
unset($requestData['gemini_api_key']);
$debugEnabled = !empty($requestData['vv_debug']);
unset($requestData['vv_debug']);

if(array_key_exists('image_url', $requestData) && is_string($requestData['image_url'])){
    $imageUrl = trim($requestData['image_url']);
    if($imageUrl && !preg_match('/^https?:\/\//i', $imageUrl)){
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? '';
        if($host){
            if(strpos($imageUrl, '//') === 0){
                $imageUrl = $scheme . ':' . $imageUrl;
            }
            elseif(strpos($imageUrl, '/') === 0){
                $imageUrl = $scheme . '://' . $host . $imageUrl;
            }
        }
    }
    $requestData['image_url'] = $imageUrl;
}

$jsonData = json_encode($requestData);
if($jsonData === false){
    http_response_code(500);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Failed to encode request payload.']);
    exit;
}

$headers = ['Content-Type: application/json'];
if($authToken){
    if(strpos($authToken, '.') !== false && strlen($authToken) > 100){
        $headers[] = 'Authorization: Bearer ' . $authToken;
    }
    else{
        $headers[] = 'X-API-Key: ' . $authToken;
    }
}
elseif(!empty($VOUCHERVISION_API_KEY)){
    $headers[] = 'X-API-Key: ' . $VOUCHERVISION_API_KEY;
}
else{
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Authorization token is required when shared credentials are unavailable.']);
    exit;
}

// Add Gemini API key as header if provided
if($geminiApiKey){
    $headers[] = 'X-Gemini-API-Key: ' . $geminiApiKey;
}

// Set up the API request
$curl_req = curl_init($voucherVisionUrl);
curl_setopt($curl_req, CURLOPT_POST, true);
curl_setopt($curl_req, CURLOPT_RETURNTRANSFER, true);

if($debugEnabled){
    error_log('[VoucherVisionGo RPC] upstream_url=' . $voucherVisionUrl . '; image_url=' . ($requestData['image_url'] ?? ''));
    error_log('[VoucherVisionGo RPC] Request headers: ' . json_encode($headers));
    error_log('[VoucherVisionGo RPC] Request body size: ' . strlen($jsonData) . ' bytes');
}

// Add headers to the request
curl_setopt($curl_req, CURLOPT_HTTPHEADER, $headers);

// Add our JSON data to the POST body
curl_setopt($curl_req, CURLOPT_POSTFIELDS, $jsonData);

// Query the API and collect the response
$response = curl_exec($curl_req);
$statusCode = curl_getinfo($curl_req, CURLINFO_HTTP_CODE);
$contentType = curl_getinfo($curl_req, CURLINFO_CONTENT_TYPE);
$curlError = curl_error($curl_req);
curl_close($curl_req);

if($debugEnabled || $statusCode >= 400){
    error_log('[VoucherVisionGo] Response status: ' . $statusCode);
    error_log('[VoucherVisionGo] Response body: ' . (is_string($response) ? substr($response, 0, 500) : '(non-string)'));
}

if($response === false){
    http_response_code(502);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'VoucherVision request failed.', 'details' => $curlError]);
    exit;
}

if($statusCode === 404 && is_string($response) && stripos($response, 'The requested URL was not found on the server') !== false){
    http_response_code(502);
    header('Content-Type: application/json');
    echo json_encode([
        'error' => 'VoucherVision endpoint returned 404.',
        'details' => 'Check VOUCHERVISION_API_URL and ensure it targets the /process-url route.',
        'voucherVisionUrl' => $voucherVisionUrl,
        'image_url' => $requestData['image_url'] ?? null
    ]);
    exit;
}

// Return the API response, with the status code and the appropriate headers
http_response_code($statusCode);
if($contentType){
    header('Content-Type: ' . $contentType);
}
else{
    header('Content-Type: application/json');
}
echo $response;

?>