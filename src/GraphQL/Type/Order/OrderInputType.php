<?php

namespace App\GraphQL\Type\Order;

use App\GraphQL\Type\Order\OrderItemType;
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
                    'items' => [
                        'type' => Type::nonNull(Type::listOf(Type::nonNull(OrderItemType::get()))),
                        'description' => 'List of order items'
                    ],
                    'totalAmount' => [
                        'type' => Type::nonNull(Type::float()),
                        'description' => 'Total order amount'
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}