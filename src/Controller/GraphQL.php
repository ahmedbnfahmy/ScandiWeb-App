<?php

namespace App\Controller;

use App\GraphQL\Resolver\ProductResolver;
use App\GraphQL\Type\ProductType;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use RuntimeException;
use Throwable;

class GraphQL {
    static public function handle() {
        try {
            $productResolver = new ProductResolver();

            $queryType = new ObjectType([
                'name' => 'Query',
                'fields' => [
                    'products' => [
                        'type' => Type::listOf(ProductType::get()),
                        'resolve' => fn() => $productResolver->getProducts()
                    ],
                    'product' => [
                        'type' => ProductType::get(),
                        'args' => [
                            'id' => Type::nonNull(Type::string())
                        ],
                        'resolve' => fn($root, array $args) => $productResolver->getProduct($args['id'])
                    ]
                ]
            ]);
        
            $schema = new Schema(
                (new SchemaConfig())
                ->setQuery($queryType)
            );
        
            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }
        
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;
        
            $result = GraphQLBase::executeQuery($schema, $query, null, null, $variableValues);
            $output = $result->toArray();
        } catch (Throwable $e) {
            $output = [
                'error' => [
                    'message' => $e->getMessage(),
                ],
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}