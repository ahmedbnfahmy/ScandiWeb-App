<?php

namespace App\Utility;

class CorsHandler
{
    /**
     * Add CORS headers for all origins
     */
    public static function addHeaders(): void
    {
        header('Access-Control-Allow-Origin: *');
        header('Access-Control-Allow-Methods: GET, POST, OPTIONS, PUT, DELETE, PATCH');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        
        // Note: We cannot use Access-Control-Allow-Credentials with wildcard origin
    }
    
    /**
     * Handle preflight requests
     */
    public static function handlePreflight(): void
    {
        if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            http_response_code(200);
            exit();
        }
    }
} 