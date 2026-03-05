<?php

namespace Capito\CapPhpServer;

/**
 * Brute Force Protection Rate Limiter
 * Tracks request counts with rolling penalty system - blocked requests must wait full penalty duration
 */
class RateLimiter
{
    private $storage; // Can be MysqlStorage, FileStorage, etc.
    private int $bucketExpiry;

    /**
     * Create a new brute force protection rate limiter
     * @param object|null $storage Storage backend with rate limit methods (null for in-memory)
     * @param int $bucketExpiry Bucket expiry time in seconds (default: 1 hour)
     */
    public function __construct($storage = null, int $bucketExpiry = 3600)
    {
        $this->storage = $storage;
        $this->bucketExpiry = $bucketExpiry;
    }

    /**
     * Check if request is allowed with extending window and rolling penalty system
     * @param string $key Identifier for rate limiting (e.g., IP address)
     * @param int $limit Maximum requests allowed in the time window
     * @param int $window Time window in seconds for counting requests (extends with new requests)
     * @param int $penalty Penalty duration in seconds for blocked requests (0 = disabled)
     * @return bool Whether request is allowed
     */
    public function allow(string $key, int $limit, int $window, int $penalty = 60): bool
    {
        if ($limit <= 0 || $penalty <= 0) {
            return true; // Rate limiting disabled
        }
        $now = time();
        $bucket = $this->getBucket($key);
        
        // Check if new, window expired, or penalty expired (unified expiry check)
        if (($bucket === null) || ($now >= $bucket['expires_at'])) {
            // Reset with new activity (fresh window)
            $bucket = [
                'count' => 1,
                'window_start' => $now,
                'expires_at' => $now + $window, // Normal window expiry
            ];
            $this->setBucket($key, $bucket);
            return true;
        }
        
        // Check if we can allow this request (within current window)
        if ($bucket['count'] < $limit) {
            // Add current request and extend window
            $bucket['count']++;
            $bucket['expires_at'] = $now + $window; // Extend window with each new request
            $this->setBucket($key, $bucket);
            return true;
        }
        
        // Rate limit exceeded - impose penalty (longer expiry)
        $bucket['expires_at'] = $now + $penalty; // Penalty period
        $this->setBucket($key, $bucket);
        return false;
    }

    /**
     * Get bucket data from storage or fallback to in-memory
     * @param string $key Rate limit identifier
     * @return array|null Bucket data or null if not found
     */
    private function getBucket(string $key): ?array
    {
        if ($this->storage !== null && method_exists($this->storage, 'getRateLimitBucket')) {
            return $this->storage->getRateLimitBucket($key);
        }
        return null; // No storage, no persistence
    }

    /**
     * Set bucket data in storage
     * @param string $key Rate limit identifier
     * @param array $bucket Bucket data
     */
    private function setBucket(string $key, array $bucket): void
    {
        if ($this->storage !== null && method_exists($this->storage, 'setRateLimitBucket')) {
            $expiresTs = time() + $this->bucketExpiry;
            $this->storage->setRateLimitBucket($key, $bucket, $expiresTs);
        }
    }



    /**
     * Reset rate limit for a specific key
     * @param string $key Identifier to reset
     */
    public function reset(string $key): void
    {
        if ($this->storage !== null && method_exists($this->storage, 'deleteRateLimitBucket')) {
            $this->storage->deleteRateLimitBucket($key);
        }
    }

    /**
     * Get used token count for a key (extending window)
     * @param string $key Identifier
     * @param int $limit Maximum requests allowed in the window (defaults to 5)
     * @param int $window Time window in seconds (defaults to 60)
     * @param int $penalty Penalty duration in seconds (defaults to 60)
     * @return int Number of tokens used in current extending window
     */
    public function getUsedTokens(string $key, int $limit = 5, int $window = 60, int $penalty = 60): int
    {
        $bucket = $this->getBucket($key);
        if ($bucket === null) {
            return 0; // No requests made yet
        }
        
        $now = time();
        
        // Check if bucket has expired (window or penalty)
        if ($now >= $bucket['expires_at']) {
            return 0; // Bucket expired, no tokens used in new window
        }
        
        // Return the count of used tokens in current bucket
        return $bucket['count'];
    }
}
