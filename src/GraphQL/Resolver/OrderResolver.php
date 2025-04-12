<?php

namespace App\GraphQL\Resolver;

use App\Models\Repository\OrderRepository;
use App\Models\Repository\OrderItemRepository;
use App\Models\Repository\ProductRepository;
use App\Models\Repository\OrderItemAttributeRepository;
class OrderResolver
{
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;
    private OrderItemAttributeRepository $orderItemAttributeRepository;
    private ProductRepository $productRepository;
    
    public function __construct(
        OrderRepository $orderRepository,
        OrderItemRepository $orderItemRepository,
        OrderItemAttributeRepository $orderItemAttributeRepository,
        ProductRepository $productRepository
    ) {
        $this->orderRepository = $orderRepository;
        $this->orderItemRepository = $orderItemRepository;
        $this->orderItemAttributeRepository = $orderItemAttributeRepository;
        $this->productRepository = $productRepository;
    }
    
    public function createOrder($rootValue, array $args): array
    {
        $input = $args['input'];
        
        $order = [
            'id' => uniqid('order_'),
            'customerName' => 'Guest', 
            'customerEmail' => 'guest@example.com',
            'totalAmount' => $input['totalAmount'],
            'status' => 'pending'
        ];
        
        $order = $this->orderRepository->create($order);
        
        // Process items
        foreach ($input['items'] as $itemInput) {
            $product = $this->productRepository->getById($itemInput['productId']);
            
            $orderItem = [
                'id' => uniqid('item_'),
                'orderId' => $order['id'],
                'productId' => $itemInput['productId'],
                'quantity' => $itemInput['quantity'],
                'price' => $product['price'] ?? 0
            ];
            
            $orderItem = $this->orderItemRepository->create($orderItem);
            
            if (isset($itemInput['selectedAttributes'])) {
                foreach ($itemInput['selectedAttributes'] as $attrInput) {
                    $attr = [
                        'orderItemId' => $orderItem['id'],
                        'attributeSetId' => $attrInput['attributeSetId'],
                        'attributeId' => $attrInput['attributeId']
                    ];
                    
                    $this->orderItemAttributeRepository->create($attr);
                }
            }
        }
        
        return $order;
    }
    
    public function getOrderItems(string $orderId): array
    {
        return $this->orderItemRepository->getByOrderId($orderId);
    }
}