<?php

namespace App\GraphQL\Type;

use App\GraphQL\Resolver\ProductResolver;
use App\GraphQL\Resolver\OrderResolver;
use App\GraphQL\Type\Order\OrderType;
use App\GraphQL\Type\Order\OrderInputType;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class MutationType
{
    public static function get(): ObjectType
    {
        $productResolver = new ProductResolver();
        $orderResolver = new OrderResolver();

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
                    'resolve' => [$productResolver, 'createProduct']
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
                    'resolve' => [$productResolver, 'updateProduct']
                ],
                'deleteProduct' => [
                    'type' => Type::boolean(),
                    'args' => [
                        'id' => Type::nonNull(Type::string())
                    ],
                    'resolve' => [$productResolver, 'deleteProduct']
                ],
                'createOrder' => [
                    'type' => OrderType::get(),
                    'description' => 'Create a new order',
                    'args' => [
                        'input' => Type::nonNull(OrderInputType::get())
                    ],
                    'resolve' => [$orderResolver, 'createOrder']
                ]
            ]
        ]);
    }
}