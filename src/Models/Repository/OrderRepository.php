<?php

namespace App\Models\Repository;

use App\Database\CoreModel;
use App\Util\UuidGenerator;
use InvalidArgumentException;

class OrderRepository extends CoreModel
{
    protected function getTableName(): string
    {
        return 'orders';
    }
    
    /**
     * Create a new order in the database
     * 
     * @param array $data The order data
     * @return string The ID of the created order
     * @throws InvalidArgumentException If validation fails
     */
    public function create(array $data): string
    {
        // Generate a UUID for the order ID if not provided
        $orderId = $data['id'] ?? UuidGenerator::generate();
        
        // Prepare order data for database
        $orderData = [
            'id' => $orderId,
            'customer_name' => $data['customerName'],
            'customer_email' => $data['customerEmail'],
            'address' => $data['address'] ?? null,
            'total_amount' => $data['totalAmount'],
            'status' => $data['status'] ?? 'pending',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        // Insert the order directly
        $this->query(
            "INSERT INTO orders (id, customer_name, customer_email, address, total_amount, status, created_at) 
             VALUES (?, ?, ?, ?, ?, ?, ?)",
            [
                $orderData['id'],
                $orderData['customer_name'],
                $orderData['customer_email'],
                $orderData['address'],
                $orderData['total_amount'],
                $orderData['status'],
                $orderData['created_at']
            ]
        );
        
        // Insert order items if they exist
        if (isset($data['items']) && !empty($data['items'])) {
            $orderItemRepo = new OrderItemRepository();
            $orderItemRepo->createOrderItems($orderId, $data['items']);
        }
        
        return $orderId;
    }
    
    /**
     * Create order and return order data
     * 
     * @param array $data The order data
     * @return array The created order data
     */
    public function createAndReturn(array $data): array
    {
        // Call the regular create method to insert the order
        $orderId = $this->create($data);
        
        // Format order items for response
        $orderItems = [];
        if (isset($data['items']) && !empty($data['items'])) {
            foreach ($data['items'] as $item) {
                // Generate an ID for each item in the response
                $itemId = UuidGenerator::generate();
                
                $orderItems[] = [
                    'id' => $itemId,
                    'productId' => $item['productId'],
                    'quantity' => (int)$item['quantity'],
                    'selectedAttributes' => array_map(function($attr) {
                        return [
                            'attributeSetId' => $attr['attributeSetId'],
                            'attributeId' => $attr['attributeId']
                        ];
                    }, $item['selectedAttributes'] ?? [])
                ];
            }
        }
        
        // Return the created order data directly
        return [
            'id' => $orderId,
            'customerName' => $data['customerName'],
            'customerEmail' => $data['customerEmail'],
            'address' => $data['address'] ?? null,
            'totalAmount' => (float)$data['totalAmount'],
            'status' => $data['status'] ?? 'pending',
            'createdAt' => date('Y-m-d H:i:s'),
            'items' => $orderItems
        ];
    }
}