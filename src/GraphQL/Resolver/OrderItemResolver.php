<?php

namespace App\GraphQL\Resolver;

use App\Models\Repository\OrderItemRepository;
use App\Models\Repository\ProductRepository;

class OrderItemResolver
{
    private OrderItemRepository $orderItemRepository;
    private ProductRepository $productRepository;

    /**
     * Initialize repositories
     */
    public function __construct(
        OrderItemRepository $orderItemRepository = null,
        ProductRepository $productRepository = null
    ) {
        $this->orderItemRepository = $orderItemRepository ?? new OrderItemRepository();
        $this->productRepository = $productRepository ?? new ProductRepository();
    }

    /**
     * Get order items for a specific order - used for queries, not during mutations
     * 
     * @param string $orderId Order ID
     * @return array Array of order items
     */
    public function getOrderItems(string $orderId): array
    {
        return $this->orderItemRepository->getOrderItems($orderId);
    }

    /**
     * Get a specific order item by ID
     * 
     * @param string $orderItemId Order item ID
     * @return array Order item data
     */
    public function getOrderItemById(string $orderItemId): array
    {
        $orderItem = $this->orderItemRepository->findById($orderItemId);
        
        if (!$orderItem) {
            throw new \InvalidArgumentException("Order item not found: {$orderItemId}");
        }
        
        return $orderItem;
    }
    
    /**
     * Get product information for an order item
     * 
     * @param string $productId Product ID
     * @return array Product data
     */
    public function getProduct(string $productId): array
    {
        $product = $this->productRepository->findById($productId);
        
        if (!$product) {
            throw new \InvalidArgumentException("Product not found: {$productId}");
        }
        
        return $product;
    }
    
    /**
     * Calculate subtotal for an order item
     * 
     * @param array $orderItem Order item data
     * @return float Calculated subtotal
     */
    public function calculateSubtotal(array $orderItem): float
    {
        $product = $this->productRepository->findById($orderItem['product_id']);
        
        if (!$product) {
            throw new \InvalidArgumentException("Product not found: {$orderItem['product_id']}");
        }
        
        return $product['price'] * $orderItem['quantity'];
    }
}