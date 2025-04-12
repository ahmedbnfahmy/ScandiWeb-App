<?php

namespace App\Utility;

use ErrorException;
use Throwable;

class ErrorHandler
{
    /**
     * Register the error handler
     */
    public static function register(): void
    {
        set_error_handler([self::class, 'handleError']);
    }
    
    /**
     * Unregister the error handler
     */
    public static function unregister(): void
    {
        restore_error_handler();
    }
    
    /**
     * Convert PHP errors to exceptions
     */
    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    
    /**
     * Handle uncaught exceptions
     */
    public static function handleException(Throwable $e): void
    {
        // Log the error
        error_log($e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
        
        // Clear any output buffers
        if (ob_get_length()) ob_clean();
        
        // Send error response
        header('Content-Type: application/json');
        http_response_code(500);
        
        echo json_encode([
            'errors' => [
                [
                    'message' => $e->getMessage(),
                    'location' => $e->getFile() . ':' . $e->getLine()
                ]
            ]
        ]);
    }
} 