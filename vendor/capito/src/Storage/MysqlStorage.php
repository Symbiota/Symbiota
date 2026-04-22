<?php

namespace Capito\CapPhpServer\Storage;

use Exception;
use PDO;
use Capito\CapPhpServer\Interfaces\StorageInterface;

/**
 * MySQL-based Storage Adapter for Cap Server
 * Implements StorageInterface using a single MySQL table with an UPDATE logic.
 */
class MysqlStorage implements StorageInterface
{
    private PDO $pdo;
    private string $table;

    public function __construct(array $config)
    {
        $this->table = $config['table'] ?? 'cap_tokens';
        $dsn = sprintf(
            "mysql:host=%s;dbname=%s;charset=%s",
            $config['host'] ?? '127.0.0.1',
            $config['dbname'],
            $config['charset'] ?? 'utf8mb4'
        );
        $options = $config['options'] ?? [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $config['username'], $config['password'], $options);
        } catch (\PDOException $e) {
            throw new Exception("MySQL connection failed: " . $e->getMessage());
        }
    }

    /**
     * @inheritDoc
     */
    public function setChallenge(string $token, int $expiresTs, array $data): bool
    {
        try {
            $sql = "INSERT INTO {$this->table} (`key`, key_type, data, expires_at) VALUES (?, 'challenge', ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$token, json_encode($data), $expiresTs]);
        } catch (\PDOException $e) {
            // Check for "Base table not found" error code (42S02) to attempt table creation.
            if ($e->getCode() === '42S02' || strpos($e->getMessage(), "Base table or view not found") !== false) {
                $this->ensureTableExists();
                // Retry the insert operation after table creation.
                return $this->setChallenge($token, $expiresTs, $data);
            }
            error_log("MySQLStorage: Failed to set challenge: " . $e->getMessage());
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function getChallenge(string $token): ?array
    {
        try {
            $sql = "SELECT data FROM {$this->table} WHERE `key` = ? AND key_type = 'challenge' AND expires_at > ? LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$token, time()]);
            $data = $stmt->fetchColumn();
            return $data === false ? null : json_decode($data, true);
        } catch (\PDOException $e) {
            error_log("MySQLStorage: Failed to get challenge: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * @inheritDoc
     */
    public function removeChallenge(string $token): bool
    {
        try {
            $sql = "DELETE FROM {$this->table} WHERE `key` = ? AND key_type = 'challenge'";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$token]);
        } catch (\PDOException $e) {
            error_log("MySQLStorage: Failed to remove challenge: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Updates the challenge's primary key to the new token key.
     * NOTE: This is an anti-pattern in database design. Use with caution.
     * @param string $token The new primary key for the row.
     * @param int $expiresTs Expiration timestamp for the new token.
     * @param string $challengeToken The old primary key to be updated.
     * @return bool Success status.
     */
    public function setToken(string $token, int $expiresTs, string $challengeToken): bool
    {
        try {
            $sql = "UPDATE {$this->table} SET `key` = ?, key_type = 'token', data = '{}', expires_at = ? WHERE `key` = ?";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$token, $expiresTs, $challengeToken]);
        } catch (\PDOException $e) {
            error_log("MySQLStorage: Failed to update primary key: " . $e->getMessage());
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function getToken(string $token, bool $delete = false, bool $cleanup = false): ?int
    {
        try {
            if ($cleanup) {
                $this->cleanup();
            }

            // Fetch the expiration time once
            $sql = "SELECT expires_at FROM {$this->table} WHERE `key` = ? AND key_type = 'token' AND expires_at > ? LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$token, time()]);
            $expires = $stmt->fetchColumn();

            if ($delete && $expires !== false) {
                // Perform the DELETE if requested and if the token was found
                $deleteSql = "DELETE FROM {$this->table} WHERE `key` = ?";
                $deleteStmt = $this->pdo->prepare($deleteSql);
                $deleteStmt->execute([$token]);
            }
            
            return $expires === false ? null : $expires;
            
        } catch (\PDOException $e) {
            error_log("MySQLStorage: Failed to get token: " . $e->getMessage());
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function cleanup(): bool
    {
        $sql = "DELETE FROM {$this->table} WHERE expires_at < ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([time()]);
    }

    /**
     * @inheritDoc
     */
    public function isAvailable(): bool
    {
        try {
            $this->pdo->query("SELECT 1 FROM {$this->table} LIMIT 1");
            return true;
        } catch (\PDOException $e) {
            return false;
        }
    }

    /**
     * Set rate limit bucket data for a key
     * @param string $key Rate limit identifier (e.g., IP address)
     * @param array $bucketData Bucket data ['tokens' => float, 'last_refill' => float]
     * @param int $expiresTs Expiration timestamp
     * @return bool Success status
     */
    public function setRateLimitBucket(string $key, array $bucketData, int $expiresTs): bool
    {
        try {
            $rateLimitKey = "rate_limit:" . $key;
            $sql = "INSERT INTO {$this->table} (`key`, key_type, data, expires_at) VALUES (?, 'rate_limit', ?, ?) 
                    ON DUPLICATE KEY UPDATE data = VALUES(data), expires_at = VALUES(expires_at)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$rateLimitKey, json_encode($bucketData), $expiresTs]);
        } catch (\PDOException $e) {
            error_log("MySQLStorage: Failed to set rate limit bucket: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Get rate limit bucket data for a key
     * @param string $key Rate limit identifier (e.g., IP address)
     * @return array|null Bucket data or null if not found
     */
    public function getRateLimitBucket(string $key): ?array
    {
        try {
            $rateLimitKey = "rate_limit:" . $key;
            $sql = "SELECT data FROM {$this->table} WHERE `key` = ? AND key_type = 'rate_limit' AND expires_at > ? LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$rateLimitKey, time()]);
            $data = $stmt->fetchColumn();
            return $data === false ? null : json_decode($data, true);
        } catch (\PDOException $e) {
            error_log("MySQLStorage: Failed to get rate limit bucket: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete rate limit bucket for a key
     * @param string $key Rate limit identifier
     * @return bool Success status
     */
    public function deleteRateLimitBucket(string $key): bool
    {
        try {
            $rateLimitKey = "rate_limit:" . $key;
            $sql = "DELETE FROM {$this->table} WHERE `key` = ? AND key_type = 'rate_limit'";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$rateLimitKey]);
        } catch (\PDOException $e) {
            error_log("MySQLStorage: Failed to delete rate limit bucket: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Creates the storage table if it does not exist.
     *
     * @return void
     */
    private function ensureTableExists(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS `{$this->table}` (
            `key` varchar(255) NOT NULL,
            `key_type` ENUM('challenge', 'token', 'rate_limit') NOT NULL,
            `data` json NOT NULL,
            `expires_at` int(11) NOT NULL,
            PRIMARY KEY (`key`),
            KEY `idx_expires` (`expires_at`),
            KEY `idx_key_type` (`key_type`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;";
        
        $this->pdo->exec($sql);
    }
}
