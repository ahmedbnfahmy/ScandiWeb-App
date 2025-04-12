<?php

namespace App\GraphQL\Type\Order;

use App\GraphQL\Type\AttributeType;
use App\GraphQL\Type\Order\SelectedAttributeType;
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
                    'productId' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Product ID'
                    ],
                    'quantity' => [
                        'type' => Type::nonNull(Type::int()),
                        'description' => 'Quantity of the product'
                    ],
                    'selectedAttributes' => [
                        'type' => Type::listOf(SelectedAttributeType::get()),
                        'description' => 'Selected product attributes',
                        'resolve' => function ($orderItem) {
                            $resolver = new \App\GraphQL\Resolver\AttributeResolver();
                            return $resolver->getSelectedAttributes($orderItem['id']);
                        }
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}