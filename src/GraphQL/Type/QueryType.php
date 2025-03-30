<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class QueryType
{
    public static function get(): ObjectType
    {
        return new ObjectType([
            'name' => 'Query',
            'fields' => [
                'categories' => [
                    'type' => Type::listOf(CategoryType::get()),
                    'description' => 'List of all categories',
                    'resolve' => function($root, $args) {
                        // Implement resolver to fetch categories
                        return Database::select("SELECT * FROM categories");
                    }
                ],
                'products' => [
                    'type' => Type::listOf(ProductType::get()),
                    'description' => 'List of all products',
                    'args' => [
                        'category' => [
                            'type' => Type::string(),
                            'description' => 'Filter by category'
                        ]
                    ],
                    'resolve' => function($root, $args) {
                        if (isset($args['category'])) {
                            return Database::select(
                                "SELECT * FROM products WHERE category = ?",
                                [$args['category']]
                            );
                        }
                        return Database::select("SELECT * FROM products");
                    }
                ],
                'product' => [
                    'type' => ProductType::get(),
                    'args' => [
                        'id' => Type::nonNull(Type::string())
                    ],
                    'resolve' => function($root, $args) {
                        return Database::selectOne(
                            "SELECT * FROM products WHERE id = ?",
                            [$args['id']]
                        );
                    }
                ]
            ]
        ]);
    }
}