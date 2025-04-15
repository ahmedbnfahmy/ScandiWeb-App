<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeItemType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'AttributeItem',
                'fields' => [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Item ID'
                    ],
                    'displayValue' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Display value of the attribute item'
                    ],
                    'value' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Actual value of the attribute item'
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
} 