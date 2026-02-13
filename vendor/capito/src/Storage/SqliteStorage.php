<?php
// CAUTION : this code has not been fully tested
// use it, debug it, and please commit update !
namespace Capito\CapPhpServer\Storage;

use Exception;
use PDO;
use Capito\CapPhpServer\Interfaces\StorageInterface;

/**
 * SQLite-based Storage Adapter for Cap Server
 * Implements StorageInterface for file-based database persistence.
 */
class SqliteStorage implements StorageInterface
{
    private PDO $pdo;
    private string $filePath;
    private string $table;

    public function __construct(array $config)
    {
        $this->table = $config['table'] ?? 'cap_tokens';
        $this->filePath = $config['path'] ?? '.data/cap_storage.sqlite';

        // Set the DSN for SQLite
        $dsn = "sqlite:{$this->filePath}";
        
        $options = $config['options'] ?? [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ];

        try {
            $this->pdo = new PDO($dsn, null, null, $options);
        } catch (\PDOException $e) {
            // If the initial connection fails, try to create the directory and retry.
            $dir = dirname($this->filePath);
            if (!is_dir($dir)) {
                if (!mkdir($dir, 0755, true) && !is_dir($dir)) {
                    throw new Exception("Failed to create storage directory: {$dir}");
                }
                // Retry the PDO connection after creating the directory
                $this->pdo = new PDO($dsn, null, null, $options);
            } else {
                 // Re-throw the original exception if the directory already existed,
                 // as the error must be something else (e.g., permissions).
                 throw $e;
            }
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
            // A more generic catch block for portability.
            error_log("SQLiteStorage: Initial insert failed. Attempting to create table. Error: " . $e->getMessage());
            $this->ensureTableExists();
            
            // Retry the insert operation
            return $this->setChallenge($token, $expiresTs, $data);
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
            error_log("SQLiteStorage: Failed to get challenge: " . $e->getMessage());
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
            error_log("SQLiteStorage: Failed to remove challenge: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Updates the challenge's primary key to the new token key.
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
            error_log("SQLiteStorage: Failed to update primary key: " . $e->getMessage());
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

            $sql = "SELECT expires_at FROM {$this->table} WHERE `key` = ? AND key_type = 'token' AND expires_at > ? LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$token, time()]);
            $expires = $stmt->fetchColumn();

            if ($delete && $expires !== false) {
                $deleteSql = "DELETE FROM {$this->table} WHERE `key` = ?";
                $deleteStmt = $this->pdo->prepare($deleteSql);
                $deleteStmt->execute([$token]);
            }
            
            return $expires === false ? null : $expires;
            
        } catch (\PDOException $e) {
            error_log("SQLiteStorage: Failed to get token: " . $e->getMessage());
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
            if (!is_dir(dirname($this->filePath))) {
                return false;
            }
            if (!file_exists($this->filePath)) {
                return is_writable(dirname($this->filePath));
            }
            return is_writable($this->filePath);
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function setRateLimitBucket(string $key, array $bucketData, int $expiresTs): bool
    {
        try {
            $rateLimitKey = "rate_limit:" . $key;
            $sql = "INSERT OR REPLACE INTO {$this->table} (`key`, key_type, data, expires_at) VALUES (?, 'rate_limit', ?, ?)";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$rateLimitKey, json_encode($bucketData), $expiresTs]);
        } catch (\PDOException $e) {
            error_log("SQLiteStorage: Failed to set rate limit bucket: " . $e->getMessage());
            return false;
        }
    }

    /**
     * @inheritDoc
     */
    public function getRateLimitBucket(string $key): ?array
    {
        try {
            $rateLimitKey = "rate_limit:" . $key;
            $sql = "SELECT data FROM {$this->table} WHERE `key` = ? AND key_type = 'rate_limit' AND expires_at > ? LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([$rateLimitKey, time()]);
            $data = $stmt->fetchColumn();
            
            if ($data === false) {
                return null;
            }
            
            $decoded = json_decode($data, true);
            return $decoded ?: null;
        } catch (\PDOException $e) {
            error_log("SQLiteStorage: Failed to get rate limit bucket: " . $e->getMessage());
            return null;
        }
    }

    /**
     * @inheritDoc
     */
    public function deleteRateLimitBucket(string $key): bool
    {
        try {
            $rateLimitKey = "rate_limit:" . $key;
            $sql = "DELETE FROM {$this->table} WHERE `key` = ? AND key_type = 'rate_limit'";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([$rateLimitKey]);
        } catch (\PDOException $e) {
            error_log("SQLiteStorage: Failed to delete rate limit bucket: " . $e->getMessage());
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
            `key` TEXT NOT NULL,
            `key_type` TEXT NOT NULL,
            `data` TEXT NOT NULL,
            `expires_at` INTEGER NOT NULL,
            PRIMARY KEY (`key`)
        );
        CREATE INDEX IF NOT EXISTS `idx_expires` ON `{$this->table}` (`expires_at`);
        CREATE INDEX IF NOT EXISTS `idx_key_type` ON `{$this->table}` (`key_type`);";

        $this->pdo->exec($sql);
    }
}
