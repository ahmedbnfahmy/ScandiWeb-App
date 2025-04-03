<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoryType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'Category',
                'fields' => [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Category ID'
                    ],
                    'name' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Category name'
                    ],
                    'products' => [
                        'type' => Type::listOf(ProductType::get()),
                        'description' => 'Products in this category',
                        'resolve' => function ($category) {
                            $resolver = new \App\GraphQL\Resolver\ProductResolver();
                            return $resolver->getProducts($category['name']);
                        }
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}