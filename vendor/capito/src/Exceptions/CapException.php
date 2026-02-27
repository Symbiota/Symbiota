<?php

namespace Capito\CapPhpServer\Exceptions;

use Exception;

/**
 * Cap exception class for typed error handling
 * Inspired by go-cap error constants
 */
class CapException extends Exception
{
    // Error codes
    const INVALID_CHALLENGE = 1;
    const CHALLENGE_EXPIRED = 2;
    const INVALID_SOLUTIONS = 3;
    const STORAGE_ERROR = 4;
    const RATE_LIMITED = 5;
    const GENERATE_FAILED = 6;
    const STORAGE_NOT_DEFINED = 7;

    // Error messages
    const MESSAGES = [
        self::INVALID_CHALLENGE => 'Invalid challenge body',
        self::CHALLENGE_EXPIRED => 'Challenge expired',
        self::INVALID_SOLUTIONS => 'Invalid solutions',
        self::STORAGE_ERROR => 'Storage operation failed',
        self::RATE_LIMITED => 'Rate limit exceeded',
        self::GENERATE_FAILED => 'Generate random string failed',
        self::STORAGE_NOT_DEFINED => 'Storage not defined'
    ];

    /**
     * Create a new CapException
     * @param int $code Error code
     * @param string|null $message Custom message (optional)
     * @param Exception|null $previous Previous exception
     */
    public function __construct(int $code, ?string $message = null, ?Exception $previous = null)
    {
        $message = $message ?? (self::MESSAGES[$code] ?? 'Unknown error');
        parent::__construct($message, $code, $previous);
    }

    /**
     * Create exception for invalid challenge
     * @param string|null $message Custom message
     * @return static
     */
    public static function invalidChallenge(?string $message = null): self
    {
        return new static(self::INVALID_CHALLENGE, $message);
    }

    /**
     * Create exception for expired challenge
     * @param string|null $message Custom message
     * @return static
     */
    public static function challengeExpired(?string $message = null): self
    {
        return new static(self::CHALLENGE_EXPIRED, $message);
    }

    /**
     * Create exception for invalid solutions
     * @param string|null $message Custom message
     * @return static
     */
    public static function invalidSolutions(?string $message = null): self
    {
        return new static(self::INVALID_SOLUTIONS, $message);
    }

    /**
     * Create exception for storage errors
     * @param string|null $message Custom message
     * @return static
     */
    public static function storageError(?string $message = null): self
    {
        return new static(self::STORAGE_ERROR, $message);
    }

    /**
     * Create exception for rate limiting
     * @param string|null $message Custom message
     * @return static
     */
    public static function rateLimited(?string $message = null): self
    {
        return new static(self::RATE_LIMITED, $message);
    }

    /**
     * Create exception for generation failures
     * @param string|null $message Custom message
     * @return static
     */
    public static function generateFailed(?string $message = null): self
    {
        return new static(self::GENERATE_FAILED, $message);
    }

    /**
     * Create exception for undefined storage
     * @param string|null $message Custom message
     * @return static
     */
    public static function storageNotDefined(?string $message = null): self
    {
        return new static(self::STORAGE_NOT_DEFINED, $message);
    }
}