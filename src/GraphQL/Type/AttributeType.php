<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'Attribute',
                'fields' => [
                    'displayValue' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Display value of the attribute'
                    ],
                    'value' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Actual value of the attribute'
                    ],
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Attribute ID'
                    ]
                ]
            ]);
        }
        return self::$type;
    }
}