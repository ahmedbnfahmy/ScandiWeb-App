<?php

namespace App\GraphQL\Resolver;

use App\Models\Repository\OrderRepository;
use App\Models\Repository\OrderItemRepository;
use App\Models\Repository\ProductRepository;
use App\Models\Repository\ProductAttributeRepository;
use InvalidArgumentException;

class OrderResolver
{
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;
    
    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->orderItemRepository = new OrderItemRepository();
    }
    
    /**
     * Create a new order
     * 
     * @param array $input The order input data
     * @return array The created order
     */
    public function createOrder(array $input): array
    {
        try {
            // Create the order and return data directly without fetching from DB
            return $this->orderRepository->createAndReturn($input);
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (\Exception $e) {
            error_log('Error creating order: ' . $e->getMessage());
            throw new \Exception('Failed to create order: ' . $e->getMessage());
        }
    }
    
    /**
     * Get order items for displaying existing orders
     * 
     * @param string $orderId The order ID
     * @return array The order items
     */
    public function getOrderItems(string $orderId): array
    {
        return $this->orderItemRepository->getOrderItems($orderId);
    }
}