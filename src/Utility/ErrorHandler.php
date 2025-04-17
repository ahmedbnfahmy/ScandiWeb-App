<?php

namespace App\Utility;

use ErrorException;
use Throwable;

class ErrorHandler
{
    
    public static function register(): void
    {
        set_error_handler([self::class, 'handleError']);
    }
    
    
    public static function unregister(): void
    {
        restore_error_handler();
    }
    
    
    public static function handleError(int $severity, string $message, string $file, int $line): bool
    {
        throw new ErrorException($message, 0, $severity, $file, $line);
    }
    
    
    public static function handleException(Throwable $e): void
    {
        
        error_log($e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine());
        
        
        if (ob_get_length()) ob_clean();
        
        
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