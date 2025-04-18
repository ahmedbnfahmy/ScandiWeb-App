<?php

namespace App\GraphQL\Type\Order;

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
                        'type' => Type::string(),
                        'description' => 'Order item ID'
                    ],
                    'productId' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Product ID'
                    ],
                    'quantity' => [
                        'type' => Type::nonNull(Type::int()),
                        'description' => 'Quantity of the product'
                    ],
                    'price' => [
                        'type' => Type::float(),
                        'description' => 'Price of the item'
                    ],
                    'selectedAttributes' => [
                        'type' => Type::listOf(SelectedAttributeType::get()),
                        'description' => 'Selected product attributes'
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
} 