<?php

namespace App\GraphQL\Resolver;

use App\Models\Repository\OrderItemRepository;
use App\Models\Repository\ProductRepository;

class OrderItemResolver
{
    private OrderItemRepository $orderItemRepository;
    private ProductRepository $productRepository;

    
    public function __construct(
        OrderItemRepository $orderItemRepository = null,
        ProductRepository $productRepository = null
    ) {
        $this->orderItemRepository = $orderItemRepository ?? new OrderItemRepository();
        $this->productRepository = $productRepository ?? new ProductRepository();
    }

    
    public function getOrderItems(string $orderId): array
    {
        return $this->orderItemRepository->getOrderItems($orderId);
    }

    
    public function getOrderItemById(string $orderItemId): array
    {
        $orderItem = $this->orderItemRepository->findById($orderItemId);
        
        if (!$orderItem) {
            throw new \InvalidArgumentException("Order item not found: {$orderItemId}");
        }
        
        return $orderItem;
    }
    
    
    public function getProduct(string $productId): array
    {
        $product = $this->productRepository->findById($productId);
        
        if (!$product) {
            throw new \InvalidArgumentException("Product not found: {$productId}");
        }
        
        return $product;
    }
    
    
    public function calculateSubtotal(array $orderItem): float
    {
        $product = $this->productRepository->findById($orderItem['product_id']);
        
        if (!$product) {
            throw new \InvalidArgumentException("Product not found: {$orderItem['product_id']}");
        }
        
        return $product['price'] * $orderItem['quantity'];
    }
}