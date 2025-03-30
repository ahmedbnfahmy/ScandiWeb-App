<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class PriceType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'Price',
                'fields' => [
                    'amount' => [
                        'type' => Type::nonNull(Type::float()),
                        'description' => 'Price amount'
                    ],
                    'currency' => [
                        'type' => Type::nonNull(CurrencyType::get()),
                        'description' => 'Currency information'
                    ]
                ]
            ]);
        }
        return self::$type;
    }
}