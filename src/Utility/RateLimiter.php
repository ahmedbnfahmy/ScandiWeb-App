<?php

namespace App\Utility;

class RateLimiter
{
    private const LIMIT = 30; 
    private const TIME_FRAME = 60; 
    private const STORAGE_DIR = '/tmp/rate_limiter/'; 

    
    public static function isAllowed(string $clientIp): bool
    {
        
        if (!file_exists(self::STORAGE_DIR)) {
            mkdir(self::STORAGE_DIR, 0755, true);
        }

        $clientIp = self::getNormalizedClientIp($clientIp);
        $storageFile = self::STORAGE_DIR . md5($clientIp) . '.json';
        $currentTime = time();
        
        
        $data = self::getClientData($storageFile);
        
        
        if (!$data || ($currentTime - $data['firstRequestTime'] > self::TIME_FRAME)) {
            $data = [
                'count' => 1,
                'firstRequestTime' => $currentTime
            ];
            self::saveClientData($storageFile, $data);
            self::logRequest($clientIp, 1);
            return true;
        }
        
        
        $data['count']++;
        self::saveClientData($storageFile, $data);
        
        
        self::logRequest($clientIp, $data['count']);
        
        
        return $data['count'] <= self::LIMIT;
    }
    
    
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
    
    
    private static function saveClientData(string $storageFile, array $data): void
    {
        file_put_contents($storageFile, json_encode($data));
    }
    
    
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
    
    
    private static function logRequest(string $clientIp, int $count): void
    {
        error_log("Rate limit: IP {$clientIp}, request {$count}/" . self::LIMIT);
    }
    
    
    public static function getRemainingRequests(string $clientIp): int
    {
        $clientIp = self::getNormalizedClientIp($clientIp);
        $storageFile = self::STORAGE_DIR . md5($clientIp) . '.json';
        $data = self::getClientData($storageFile);
        
        if (!$data) {
            return self::LIMIT;
        }
        
        
        if (time() - $data['firstRequestTime'] > self::TIME_FRAME) {
            return self::LIMIT;
        }
        
        return max(0, self::LIMIT - $data['count']);
    }
    
    
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
