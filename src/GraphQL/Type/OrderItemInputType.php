<?php

namespace App\GraphQL\Type;

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
                        'description' => 'Quantity ordered'
                    ],
                ]
            ]);
        }
        
        return self::$type;
    }
}