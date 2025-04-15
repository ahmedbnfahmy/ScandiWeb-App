<?php

namespace App\Controller;

use App\GraphQL\Resolver\ProductResolver;
use App\GraphQL\Type\ProductType;
use App\GraphQL\Type\MutationType;
use App\GraphQL\Type\Order\OrderType;
use App\GraphQL\Type\Order\OrderInputType;
use App\GraphQL\Type\Order\OrderItemType;
use App\GraphQL\Type\Order\OrderItemInputType;
use App\GraphQL\Type\Order\SelectedAttributeType;
use App\GraphQL\Type\Order\SelectedAttributeInputType;
use GraphQL\GraphQL as GraphQLBase;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\Type\Schema;
use GraphQL\Type\SchemaConfig;
use GraphQL\Error\DebugFlag;
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
            OrderType::get();
            OrderInputType::get();
            OrderItemInputType::get();
            SelectedAttributeInputType::get();
            OrderItemType::get();
            SelectedAttributeType::get();
        
            $schema = new Schema(
                (new SchemaConfig())
                ->setQuery($queryType)
                ->setMutation(MutationType::get())
            );
        
            $rawInput = file_get_contents('php://input');
            if ($rawInput === false) {
                throw new RuntimeException('Failed to get php://input');
            }
            $input = json_decode($rawInput, true);
            $query = $input['query'];
            $variableValues = $input['variables'] ?? null;
        
            // Add debug flags to get more detailed error information
            $result = GraphQLBase::executeQuery($schema, $query, null, null, $variableValues);
            $output = $result->toArray(DebugFlag::INCLUDE_DEBUG_MESSAGE | DebugFlag::INCLUDE_TRACE);
        } catch (Throwable $e) {
            // Provide more detailed error information
            $output = [
                'errors' => [
                    [
                        'message' => 'Exception: ' . $e->getMessage(),
                        'locations' => [],
                        'trace' => $e->getTraceAsString()
                    ]
                ]
            ];
        }

        header('Content-Type: application/json; charset=UTF-8');
        return json_encode($output);
    }
}