<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class OrderItemType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'OrderItem',
                'fields' => [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Order item ID'
                    ],
                    'orderId' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'ID of parent order'
                    ],
                    'productId' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Product ID'
                    ],
                    'quantity' => [
                        'type' => Type::nonNull(Type::int()),
                        'description' => 'Quantity ordered'
                    ],
                    'unitPrice' => [
                        'type' => Type::nonNull(Type::float()),
                        'description' => 'Unit price'
                    ],
                    'selectedAttributes' => [
                        'type' => Type::string(),
                        'description' => 'Selected attributes (JSON)'
                    ],
                    'product' => [
                        'type' => ProductType::get(),
                        'description' => 'Product information',
                        'resolve' => function ($orderItem) {
                            $resolver = new \App\GraphQL\Resolver\ProductResolver();
                            return $resolver->getProduct($orderItem['productId']);
                        }
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}