<?php

namespace App\Utility;

class RateLimiter
{
    private const LIMIT = 30; // Maximum requests allowed
    private const TIME_FRAME = 60; // Time frame in seconds
    private const STORAGE_DIR = '/tmp/rate_limiter/'; // Directory to store rate limit data

    /**
     * Checks if the request from a given client IP is allowed
     * 
     * @param string $clientIp Client IP address
     * @return bool True if request is allowed, false otherwise
     */
    public static function isAllowed(string $clientIp): bool
    {
        // Create storage directory if it doesn't exist
        if (!file_exists(self::STORAGE_DIR)) {
            mkdir(self::STORAGE_DIR, 0755, true);
        }

        $clientIp = self::getNormalizedClientIp($clientIp);
        $storageFile = self::STORAGE_DIR . md5($clientIp) . '.json';
        $currentTime = time();
        
        // Get current data for this IP
        $data = self::getClientData($storageFile);
        
        // If no data exists or time frame has expired, initialize with first request
        if (!$data || ($currentTime - $data['firstRequestTime'] > self::TIME_FRAME)) {
            $data = [
                'count' => 1,
                'firstRequestTime' => $currentTime
            ];
            self::saveClientData($storageFile, $data);
            self::logRequest($clientIp, 1);
            return true;
        }
        
        // Increment request count
        $data['count']++;
        self::saveClientData($storageFile, $data);
        
        // Log the request
        self::logRequest($clientIp, $data['count']);
        
        // Check if the limit has been reached
        return $data['count'] <= self::LIMIT;
    }
    
    /**
     * Get current client data from storage
     */
    private static function getClientData(string $storageFile): ?array
    {
        if (!file_exists($storageFile)) {
            return null;
        }
        
        $content = file_get_contents($storageFile);
        if (!$content) {
            return null;
        }
        
        return json_decode($content, true);
    }
    
    /**
     * Save client data to storage
     */
    private static function saveClientData(string $storageFile, array $data): void
    {
        file_put_contents($storageFile, json_encode($data));
    }
    
    /**
     * Get normalized client IP (handle proxies)
     */
    private static function getNormalizedClientIp(string $defaultIp): string
    {
        if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $forwardedIps = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
            $clientIp = trim($forwardedIps[0]);
            
            if (filter_var($clientIp, FILTER_VALIDATE_IP)) {
                return $clientIp;
            }
        }
        
        return $defaultIp;
    }
    
    /**
     * Log rate limit information
     */
    private static function logRequest(string $clientIp, int $count): void
    {
        error_log("Rate limit: IP {$clientIp}, request {$count}/" . self::LIMIT);
    }
    
    /**
     * Get remaining requests for a client
     */
    public static function getRemainingRequests(string $clientIp): int
    {
        $clientIp = self::getNormalizedClientIp($clientIp);
        $storageFile = self::STORAGE_DIR . md5($clientIp) . '.json';
        $data = self::getClientData($storageFile);
        
        if (!$data) {
            return self::LIMIT;
        }
        
        // Check if time frame has expired
        if (time() - $data['firstRequestTime'] > self::TIME_FRAME) {
            return self::LIMIT;
        }
        
        return max(0, self::LIMIT - $data['count']);
    }
    
    /**
     * Add rate limit headers to response
     */
    public static function addRateLimitHeaders(string $clientIp): void
    {
        $remaining = self::getRemainingRequests($clientIp);
        header('X-RateLimit-Limit: ' . self::LIMIT);
        header('X-RateLimit-Remaining: ' . $remaining);
        
        if ($remaining === 0) {
            $clientIp = self::getNormalizedClientIp($clientIp);
            $storageFile = self::STORAGE_DIR . md5($clientIp) . '.json';
            $data = self::getClientData($storageFile);
            
            if ($data) {
                $resetTime = $data['firstRequestTime'] + self::TIME_FRAME;
                header('X-RateLimit-Reset: ' . $resetTime);
            }
        }
    }
}
