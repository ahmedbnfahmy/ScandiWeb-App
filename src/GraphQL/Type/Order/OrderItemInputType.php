<?php

namespace App\GraphQL\Type\Order;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class OrderItemInputType
{
    private static ?InputObjectType $type = null;

    public static function get(): InputObjectType
    {
        if (self::$type === null) {
            self::$type = new InputObjectType([
                'name' => 'OrderItemInput',
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
                        'type' => Type::listOf(SelectedAttributeInputType::get()),
                        'description' => 'Selected product attributes'
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
} 