<?php

namespace App\GraphQL\Type\Order;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class SelectedAttributeType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'SelectedAttribute',
                'fields' => [
                    'attributeName' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Name of the attribute (e.g., "Size", "Color")'
                    ],
                    'attributeItemId' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'ID of the selected attribute item (e.g., "Small", "Red")'
                    ],
                    'displayValue' => [
                        'type' => Type::string(),
                        'description' => 'Display value of the selected attribute item',
                        'resolve' => function ($selectedAttribute) {
                            return $selectedAttribute['displayValue'] ?? null;
                        }
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
} 