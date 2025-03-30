<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class ProductType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'Product',
                'fields' => [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Product ID'
                    ],
                    'name' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Product name'
                    ],
                    'inStock' => [
                        'type' => Type::nonNull(Type::boolean()),
                        'description' => 'Whether the product is in stock'
                    ],
                    'gallery' => [
                        'type' => Type::listOf(Type::string()),
                        'description' => 'Product images'
                    ],
                    'description' => [
                        'type' => Type::string(),
                        'description' => 'Product description'
                    ],
                    'category' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Product category'
                    ],
                    'attributes' => [
                        'type' => Type::listOf(AttributeSetType::get()),
                        'description' => 'Product attributes'
                    ],
                    'prices' => [
                        'type' => Type::listOf(PriceType::get()),
                        'description' => 'Product prices'
                    ],
                    'brand' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Product brand'
                    ]
                ]
            ]);
        }
        return self::$type;
    }
}