<?php

namespace App\GraphQL\Type;

use App\GraphQL\Resolver\CategoryResolver;
use App\GraphQL\Resolver\ProductResolver;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class QueryType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    
                    'categories' => [
                        'type' => Type::listOf(CategoryType::get()),
                        'resolve' => function () {
                            $resolver = new CategoryResolver();
                            return $resolver->getCategories();
                        }
                    ],
                    'category' => [
                        'type' => CategoryType::get(),
                        'args' => [
                            'id' => Type::nonNull(Type::string())
                        ],
                        'resolve' => function ($rootValue, $args) {
                            $resolver = new CategoryResolver();
                            return $resolver->getCategory($args['id']);
                        }
                    ],
                    
                    
                    'products' => [
                        'type' => Type::listOf(ProductType::get()),
                        'args' => [
                            'category' => Type::string()
                        ],
                        'resolve' => function ($rootValue, $args) {
                            $resolver = new ProductResolver();
                            return $resolver->getProducts($args['category'] ?? null);
                        }
                    ],
                    'product' => [
                        'type' => ProductType::get(),
                        'args' => [
                            'id' => Type::nonNull(Type::string())
                        ],
                        'resolve' => function ($rootValue, $args) {
                            $resolver = new ProductResolver();
                            return $resolver->getProduct($args['id']);
                        }
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}