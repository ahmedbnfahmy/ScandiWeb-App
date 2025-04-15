<?php

namespace App\GraphQL\Type\Order;

use App\GraphQL\Type\Order\OrderItemInputType;
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
                        'description' => 'Name of the customer'
                    ],
                    'customerEmail' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Email of the customer'
                    ],
                    'address' => [
                        'type' => Type::string(),
                        'description' => 'Shipping address'
                    ],
                    'totalAmount' => [
                        'type' => Type::nonNull(Type::float()),
                        'description' => 'Total order amount'
                    ],
                    'items' => [
                        'type' => Type::nonNull(Type::listOf(Type::nonNull(OrderItemInputType::get()))),
                        'description' => 'List of order items'
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}