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
                        'type' => Type::string(),
                        'description' => 'Order ID'
                    ],
                    'customerName' => [
                        'type' => Type::string(),
                        'description' => 'Customer name'
                    ],
                    'customerEmail' => [
                        'type' => Type::string(),
                        'description' => 'Customer email'
                    ],
                    'address' => [
                        'type' => Type::string(),
                        'description' => 'Shipping address'
                    ],
                    'totalAmount' => [
                        'type' => Type::nonNull(Type::float()),
                        'description' => 'Total order amount'
                    ],
                    'status' => [
                        'type' => Type::string(),
                        'description' => 'Order status'
                    ],
                    'createdAt' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Order creation date'
                    ],
                    'items' => [
                        'type' => Type::listOf(OrderItemType::get()),
                        'description' => 'Order items'
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}