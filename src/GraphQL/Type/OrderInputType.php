<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class OrderInputType
{
    private static ?InputObjectType $type = null;

    public static function get(): InputObjectType
    {
        if (self::$type === null) {
            self::$type = new InputObjectType([
                'name' => 'OrderInput',
                'fields' => [
                    'customerName' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Customer name'
                    ],
                    'customerEmail' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Customer email'
                    ],
                    'address' => [
                        'type' => Type::string(),
                        'description' => 'Delivery address'
                    ],
                    'items' => [
                        'type' => Type::nonNull(Type::listOf(OrderItemInputType::get())),
                        'description' => 'Order items'
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}