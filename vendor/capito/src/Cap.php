<?php
namespace Capito\CapPhpServer;

use Exception;
use Capito\CapPhpServer\Interfaces\StorageInterface;
use Capito\CapPhpServer\Exceptions\CapException;

/**
 * Cap PHP Server - A PHP implementation of Cap.
 * A lightweight, modern open-source CAPTCHA alternative using SHA-256 proof-of-work.
 * Enhanced with rate limiting and a unified storage interface.
 */
class Cap
{
    private array $config;
    
    private ?StorageInterface $storage = null;
    private ?RateLimiter $rateLimiter = null;
    
    // --- Default Challenge & Token Configuration (Milliseconds) ---
    private const DEFAULT_CHALLENGE_COUNT = 3;
    private const DEFAULT_CHALLENGE_SIZE = 16;
    private const DEFAULT_CHALLENGE_DIFFICULTY = 2;
    private const DEFAULT_EXPIRES_MS = 600000;    // 10 minutes
    private const DEFAULT_TOKEN_EXPIRES_MS = 1200000; // 20 minutes
    private const DEFAULT_TOKEN_VERIFY_ONCE = true;

    // --- Default Brute Force Protection Configuration ---
    private const DEFAULT_BRUTE_FORCE_LIMIT = 5;
    private const DEFAULT_BRUTE_FORCE_WINDOW = 60; // seconds
    private const DEFAULT_BRUTE_FORCE_PENALTY = 60; // seconds
    // --- Default Dynamic Difficulty Configuration ---
    private const DEFAULT_DYNAMIC_DIFFICULTY_ENABLED = true;
    private const DEFAULT_DIFFICULTY_MODERATE = 3;  // Difficulty when 40-80% of rate limit used
    private const DEFAULT_DIFFICULTY_AGGRESSIVE = 4; // Difficulty when >80% of rate limit used
    
    /**
     * Create a new Cap instance.
     *
     * @param array|null $configObj Configuration options.
     * * Options include:
     * - storage: StorageInterface - Custom storage implementation (mandatory).
     * - challengeCount: int - Number of challenges (default: 3).
     * - challengeSize: int - Challenge size in hex chars (default: 16).
     * - challengeDifficulty: int - Challenge difficulty (default: 2).
     * - challengeExpires: int - Challenge expiration in seconds (default: 600).
     * - tokenExpires: int - Token expiration in seconds (default: 1200).
     * - tokenVerifyOnce: bool - One-time token verification (default: true).

     * - bruteForceLimit: int - Max challenge requests per window (default: 5).
     * - bruteForceWindow: int - Brute force time window in seconds (default: 60).
     * - bruteForcePenalty: int - Penalty duration in seconds when limit exceeded (default: 60, set 0 to disable).
     * - dynamicDifficultyEnabled: bool - Enable dynamic difficulty scaling (default: true).
     * - difficultyModerate: int - Difficulty when moderate rate limiting pressure (default: 3).
     * - difficultyAggressive: int - Difficulty when high rate limiting pressure (default: 4).
     * * @throws CapException if storage is not provided.
     */
    public function __construct(?array $configObj = null)
    {
        $this->config = [
            'storage' => null, // Must be provided
            'challengeCount' => self::DEFAULT_CHALLENGE_COUNT,
            'challengeSize' => self::DEFAULT_CHALLENGE_SIZE,
            'challengeDifficulty' => self::DEFAULT_CHALLENGE_DIFFICULTY,
            'challengeExpires' => self::DEFAULT_EXPIRES_MS / 1000,
            'tokenExpires' => self::DEFAULT_TOKEN_EXPIRES_MS / 1000,
            'tokenVerifyOnce' => self::DEFAULT_TOKEN_VERIFY_ONCE,
            // Brute force protection settings
            'bruteForceLimit' => self::DEFAULT_BRUTE_FORCE_LIMIT,
            'bruteForceWindow' => self::DEFAULT_BRUTE_FORCE_WINDOW,
            'bruteForcePenalty' => self::DEFAULT_BRUTE_FORCE_PENALTY,
            // Dynamic difficulty settings
            'dynamicDifficultyEnabled' => self::DEFAULT_DYNAMIC_DIFFICULTY_ENABLED,
            'difficultyModerate' => self::DEFAULT_DIFFICULTY_MODERATE,
            'difficultyAggressive' => self::DEFAULT_DIFFICULTY_AGGRESSIVE,
        ];
        if ($configObj !== null) {
            $this->config = array_merge($this->config, array_intersect_key($configObj, $this->config));
        }
        if (!$this->config['storage'] instanceof StorageInterface) {
            throw CapException::storageError('StorageInterface implementation must be provided in configuration.');
        }
        $this->storage = $this->config['storage'];

        // Create rate limiter for brute force protection (disabled if penalty = 0)
        if ($this->config['bruteForcePenalty'] > 0) {
            // All StorageInterface implementations are required to support rate limit methods
            $this->rateLimiter = new RateLimiter($this->storage);
        }
    }

    /**
     * Check rate limit for challenge creation with anti-brute force protection.
     * @param string $currentIP Current client IP address.
     * @return bool Whether request is allowed.
     * @throws CapException If rate limited.
     */
    private function checkRateLimit(string $currentIP): bool
    {
        // Check for brute force on challenge creation
        if ($this->rateLimiter !== null) {
            $limit = $this->config['bruteForceLimit'];
            $window = $this->config['bruteForceWindow'];
            $penalty = $this->config['bruteForcePenalty'];
            $isAllowed = $this->rateLimiter->allow($currentIP, $limit, $window, $penalty);    
            if (!$isAllowed) {
                throw CapException::rateLimited("Too many challenge requests. Rate limit exceeded for: {$currentIP}. Please retry in a few minutes.");
            }
        }
        return true;
    }

    /**
     * Calculate dynamic difficulty based on recent challenge requests
     * @param string $currentIP Current client IP address
     * @return int Difficulty level (2-6, where higher is more difficult)
     */
    private function calculateDynamicDifficulty(string $currentIP): int
    {
        if ($this->rateLimiter === null || !$this->config['dynamicDifficultyEnabled']) {
            return $this->config['challengeDifficulty'];
        }
        $limit = $this->config['bruteForceLimit'];
        $window = $this->config['bruteForceWindow'];
        $usedTokens = $this->rateLimiter->getUsedTokens($currentIP, $limit, $window);
        if ($usedTokens > ($limit*0.8)) return $this->config['difficultyAggressive'];
        if ($usedTokens > ($limit*0.4)) return $this->config['difficultyModerate'];      
        return $this->config['challengeDifficulty']; // Normal difficulty
    }

    
    /**
     * Create a new challenge.
     * @param string|null $currentIP Current client IP address.
     * @return array Challenge response.
     * @throws CapException
     */
    public function createChallenge(?string $currentIP = null): array
    {
        if ($currentIP !== null) {
            $this->checkRateLimit($currentIP);
        }
        // Calculate dynamic difficulty AFTER consuming rate limit token
        $difficulty = ($currentIP !== null) 
            ? $this->calculateDynamicDifficulty($currentIP)
            : $this->config['challengeDifficulty'];
            
        $challenges = [];
        for ($i = 0; $i < $this->config['challengeCount']; $i++) {
            $salt = $this->generateRandomHex($this->config['challengeSize']);
            $target = $this->generateRandomHex($difficulty);
            $challenges[] = [$salt, $target];
        }
        $token = $this->generateRandomHex(50);
        $expiresTs = time() + $this->config['challengeExpires'];
        $expiresMs = $expiresTs * 1000;
        $challengeData = [
            'challenge' => $challenges,
            'expires' => $expiresMs,
        ];
        if (!$this->storage->setChallenge($token, $expiresTs, $challengeData)) {
            throw CapException::storageError('Failed to store challenge');
        }
        return [
            'challenge' => $challenges,
            'token' => $token,
            'expires' => $expiresMs,
        ];
    }

    
    /**
     * Redeem a challenge solution.
     * @param array $solution Solution data (must contain 'token' and 'solutions').
     * @param string|null $currentIP Current client IP address.
     * @return array Redeem response.
     * @throws CapException
     */
    public function redeemChallenge(array $solution, ?string $currentIP = null): array
    {
        if (!isset($solution['token']) || $solution['token'] === '' || !isset($solution['solutions'])) {
            throw CapException::invalidChallenge('Invalid solution body: missing token or solutions.');
        }
        $token = $solution['token'];
        $challengeData = $this->storage->getChallenge($token); // Get challenge but don't delete yet
        if ($challengeData === null) {
            throw CapException::challengeExpired('Challenge not found or already used.');
        }
        if (($challengeData['expires'] ?? 0) / 1000 < time()) {
            throw CapException::challengeExpired('Challenge expired.');
        }
        // Validate solutions
        $this->validateSolutions($solution['solutions'], $challengeData['challenge'], $token);       
        // Generate and store verification token
        $vertoken = $this->generateRandomHex(30);
        $tokenExpiresTs = time() + $this->config['tokenExpires'];
        $id = $this->generateRandomHex(16);
        $hash = hash('sha256', $vertoken);
        $key = $id . ':' . $hash;
        // The setToken method delete the challenge *and* storing the verToken
        if (!$this->storage->setToken($key, $tokenExpiresTs, $token)) {
            throw CapException::storageError('Failed to store verification token.');
        }
        return [
            'success' => true,
            'token' => $id . ':' . $vertoken,
            'expires' => $tokenExpiresTs * 1000,
        ];
    }
    

    /**
     * Validates the provided solutions against the challenges.
     * @param array $solutions Received solutions from client.
     * @param array $challenges Challenge data from storage.
     * @param string $token Token for debug logging.
     * @throws CapException if any solution is invalid.
     */
    private function validateSolutions(array $solutions, array $challenges, string $token): void
    {
        $solutionsMap = $this->buildSolutionsMap($solutions);
        foreach ($challenges as $challengeIndex => $challenge) {
            list($salt, $target) = $challenge;  
            // Start with the modern (salt-based) lookup key
            $mapSalt = $salt;
            // Check for legacy key if the salt key is missing.
            if (!isset($solutionsMap[$salt])) {
                $legacyKey = "_legacy_index_{$challengeIndex}";
                // If the legacy solution exists, switch to using that key for the lookup.
                if (isset($solutionsMap[$legacyKey])) {
                    $mapSalt = $legacyKey;
                }
            }
            $solutionValue = $solutionsMap[$mapSalt] ?? null;
            if ($solutionValue === null || !$this->isHashSolutionValid($salt, $target, $solutionValue)) {
                // Remove the challenge to prevent reuse after incorrect solution
                $this->storage->removeChallenge($token);
                $errorMessage = $solutionValue === null 
                    ? "No solution found for challenge {$challengeIndex} with salt {$salt}."
                    : "Invalid solution for challenge {$challengeIndex}.";
                sleep(15); // 15 seconds delay to slow down brute force attempts
                $this->throwFailure($solutions, $challenges, $token, $errorMessage);
            }
        }
    }

    /**
     * Creates a map of solutions keyed by their salt for fast lookups.
     * The target is only necessary for the final hash check, not the mapping key.
     * @param array $solutions Array of solutions from the client.
     * @return array Map of solutions keyed by salt.
     */
    private function buildSolutionsMap(array $solutions): array
    {
        $map = [];
        foreach ($solutions as $index => $sol) {
            // Handle (old) cap.js 0.1.25 number array format: [1, 27, 7]
            // This logic is fragile but preserved for backwards compatibility.
            if (is_numeric($sol)) {
                $map["_legacy_index_{$index}"] = $sol;
                continue;
            }
            if (!is_array($sol)) {
                continue; // Skip invalid formats
            }
            $count = count($sol);          
            if ($count === 2) { // old format [salt, solution]
                list($salt, $solutionValue) = $sol;
                $map[$salt] = $solutionValue;
            } elseif ($count === 3) { // modern format [salt, target, solution]
                // target is ignored for map construction but is used in isHashSolutionValid
                list($salt, /* target */, $solutionValue) = $sol; 
                $map[$salt] = $solutionValue;
            }
        }
        return $map;
    }

    /**
     * Performs the core SHA256 hash check for a given solution.
     * @param string $salt The salt from the challenge.
     * @param string $target The hash prefix the solution must match.
     * @param string|int $solutionValue The numerical solution found by the client.
     * @return bool True if the hash starts with the target prefix.
     */
    private function isHashSolutionValid(string $salt, string $target, $solutionValue): bool
    {
        $hashInput = $salt . (string)$solutionValue;
        $hash = hash('sha256', $hashInput);
        return str_starts_with($hash, $target);          // str_starts_with (PHP 8+)
    }

    /**
     * Compile debug information and throws a CapException.
     * @param array $solutions Received solutions from client.
     * @param array $challenges Challenge data from storage.
     * @param string $token Token for debug logging.
     * @param string $errorMessage Simple error message.
     * @throws CapException
     */
    private function throwFailure(array $solutions, array $challenges, string $token, string $errorMessage): void
    {
        $debugInfo = [
            'timestamp' => date('Y-m-d H:i:s'),
            'token' => $token,
            'received_solutions' => $solutions,
            'challenge_data' => $challenges,
        ];
        $detailedLogMessage = $errorMessage . "\n" .
                              json_encode($debugInfo, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE); 
        throw CapException::invalidSolutions($detailedLogMessage);
    }
    
    /**
     * Validate a verification token.
     * @param string $token Token to validate (format: id:vertoken).
     * @param array|null $conf Token configuration (e.g., ['keepToken' => bool]).
     * @param string|null $currentIP Current client IP address.
     * @return array Validation response.
     * @throws CapException
     */
    public function validateToken(string $token, ?array $conf = null, ?string $currentIP = null): array
    {
        $parts = explode(':', $token);
        if (count($parts) !== 2) {
            return ['success' => false, 'message' => 'Invalid token format.'];
        }     
        list($id, $vertoken) = $parts;
        $hash = hash('sha256', $vertoken);
        $key = $id . ':' . $hash;  
        $conf = $conf ?? [];
        $keepToken = $conf['keepToken'] ?? !$this->config['tokenVerifyOnce'];
        // getToken expects $deleteOnRead as a boolean, which is the inverse of $keepToken
        $expiresTs = $this->storage->getToken($key, !$keepToken, true); 
        if ($expiresTs === null) {
            return ['success' => false, 'message' => 'Token not found.'];
        }
        if ($expiresTs < time()) {
            return ['success' => false, 'message' => 'Token expired.'];
        }
        return ['success' => true];
    }
    

    /**
     * Clean up expired tokens and challenges.
     * As cleanup is handled automatically during storage reads/writes, should never be used
     * @return bool Whether cleanup was successful.
     */
    public function cleanup(): bool
    {
        try {
            return $this->storage->cleanup();
        } catch (Exception $e) {
            error_log("Warning: cleanup failed: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get storage and rate limiter statistics.
     * @return array<string, mixed> Storage statistics.
     */
    public function getStats(): array
    {
        $stats = [
            'storage_type' => get_class($this->storage),
            'rate_limiter_enabled' => $this->rateLimiter !== null,
            'config' => $this->config,
        ];
        if (method_exists($this->storage, 'getStats')) {
            $stats['storage_stats'] = $this->storage->getStats();
        }
        return $stats;
    }

    
    /**
     * Generate random hex string.
     * @param int $length Length of hex string.
     * @return string Random hex string.
     * @throws CapException If generation fails.
     */
    private function generateRandomHex(int $length): string
    {
        if ($length <= 0) {
            return '';
        }
        try {
            $bytes = random_bytes((int)ceil($length / 2));
            $hex = bin2hex($bytes);
            return substr($hex, 0, $length);
        } catch (Exception $e) {
            throw CapException::generateFailed('Failed to generate random hex: ' . $e->getMessage());
        }
    }
}
