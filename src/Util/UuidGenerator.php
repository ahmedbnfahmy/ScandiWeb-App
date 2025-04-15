<?php

namespace App\Util;

class UuidGenerator
{
    /**
     * Generate a UUID v4
     * 
     * @return string UUID
     */
    public static function generate(): string
    {
        // Generate 16 random bytes
        $data = random_bytes(16);
        
        // Set version to 0100
        $data[6] = chr(ord($data[6]) & 0x0f | 0x40);
        // Set bits 6-7 to 10
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80);
        
        // Output the 36 character UUID
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
} 