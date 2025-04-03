<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'Order',
                'fields' => [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Order ID'
                    ],
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
                    'totalAmount' => [
                        'type' => Type::nonNull(Type::float()),
                        'description' => 'Total order amount'
                    ],
                    'status' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Order status'
                    ],
                    'createdAt' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Order creation date'
                    ],
                    'items' => [
                        'type' => Type::listOf(OrderItemType::get()),
                        'description' => 'Order items',
                        'resolve' => function ($order) {
                            $resolver = new \App\GraphQL\Resolver\OrderItemResolver();
                            return $resolver->getOrderItems($order['id']);
                        }
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}