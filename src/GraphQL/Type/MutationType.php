<?php

namespace App\GraphQL\Type;

use App\GraphQL\Resolver\OrderResolver;
use App\GraphQL\Type\Order\OrderType;
use App\GraphQL\Type\Order\OrderInputType;
use App\Models\Repository\OrderRepository;
use App\Models\Repository\OrderItemRepository;
use App\Models\Repository\ProductRepository;
use App\Models\Repository\AttributeRepository;
use App\Models\Repository\AttributeSetRepository;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class MutationType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            $orderRepo = new OrderRepository();
            $orderItemRepo = new OrderItemRepository();
            $productRepo = new ProductRepository();
            $attributeRepo = new AttributeRepository();
            $attributeSetRepo = new AttributeSetRepository();
            
            $orderResolver = new OrderResolver(
                $orderRepo,
                $orderItemRepo,
                $productRepo,
                $attributeRepo,
                $attributeSetRepo
            );

            self::$type = new ObjectType([
                'name' => 'Mutation',
                'fields' => [
                    'createOrder' => [
                        'type' => OrderType::get(),
                        'description' => 'Create a new order with items and selected attributes',
                        'args' => [
                            'input' => [
                                'type' => Type::nonNull(OrderInputType::get()),
                                'description' => 'Order input data including items and attributes'
                            ]
                        ],
                        'resolve' => function($rootValue, $args) use ($orderResolver) {
                            return $orderResolver->createOrder($args['input']);
                        }
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}