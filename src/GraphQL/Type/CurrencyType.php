<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CurrencyType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'Currency',
                'fields' => [
                    'label' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Currency label (e.g., USD)'
                    ],
                    'symbol' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Currency symbol (e.g., $)'
                    ]
                ]
            ]);
        }
        return self::$type;
    }
}