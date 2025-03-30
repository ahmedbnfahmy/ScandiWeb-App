<?php

namespace App\GraphQL\Type;

use App\GraphQL\Resolver\ProductResolver;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class MutationType
{
    public static function get(): ObjectType
    {
        $resolver = new ProductResolver();

        return new ObjectType([
            'name' => 'Mutation',
            'fields' => [
                'createProduct' => [
                    'type' => ProductType::get(),
                    'args' => [
                        'name' => Type::nonNull(Type::string()),
                        'description' => Type::string(),
                        'price' => Type::nonNull(Type::float()),
                        'category' => Type::nonNull(Type::string()),
                        'brand' => Type::nonNull(Type::string()),
                        'inStock' => Type::boolean(),
                        'gallery' => Type::listOf(Type::string())
                    ],
                    'resolve' => [$resolver, 'createProduct']
                ],
                'updateProduct' => [
                    'type' => ProductType::get(),
                    'args' => [
                        'id' => Type::nonNull(Type::string()),
                        'name' => Type::string(),
                        'description' => Type::string(),
                        'price' => Type::float(),
                        'category' => Type::string(),
                        'brand' => Type::string(),
                        'inStock' => Type::boolean(),
                        'gallery' => Type::listOf(Type::string())
                    ],
                    'resolve' => [$resolver, 'updateProduct']
                ],
                'deleteProduct' => [
                    'type' => Type::boolean(),
                    'args' => [
                        'id' => Type::nonNull(Type::string())
                    ],
                    'resolve' => [$resolver, 'deleteProduct']
                ]
            ]
        ]);
    }
}