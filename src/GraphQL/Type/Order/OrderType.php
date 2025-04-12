<?php

namespace App\GraphQL\Type\Order;

use App\GraphQL\Type\Order\OrderItemType;
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
                    'totalAmount' => [
                        'type' => Type::nonNull(Type::float()),
                        'description' => 'Total order amount'
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