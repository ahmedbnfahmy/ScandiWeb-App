<?php

namespace App\GraphQL\Resolver;

use App\Models\Repository\OrderItemRepository;

class OrderItemResolver
{
    private OrderItemRepository $repository;
    
    public function __construct()
    {
        $this->repository = new OrderItemRepository();
    }
    
    /**
     * Get order items for a specific order
     */
    public function getOrderItems(string $orderId): array
    {
        try {
            return $this->repository->findByOrderId($orderId);
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch order items: " . $e->getMessage());
        }
    }
}