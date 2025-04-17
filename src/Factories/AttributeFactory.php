<?php

namespace App\Factories;

use App\Models\Attribute\AbstractAttribute;
use App\Models\Attribute\TextAttribute;
use App\Models\Attribute\SwatchAttribute;

class AttributeFactory
{
    
    public static function create(array $data): AbstractAttribute
    {
        $type = $data['type'] ?? '';
        
        $attribute = match ($type) {
            'text' => new TextAttribute(),
            'swatch' => new SwatchAttribute(),
            default => new TextAttribute() 
        };
        
        $attribute->setData($data);
        return $attribute;
    }
}