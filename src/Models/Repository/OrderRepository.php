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
                            'attributeName' => $attr['attributeName'],
                            'attributeItemId' => $attr['attributeItemId'],
                            'displayValue' => $attr['displayValue'] ?? null
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
    
    /**
     * Create a new order with items and attributes in the database
     * 
     * @param array $data The order data
     * @return array The created order with all details
     * @throws InvalidArgumentException If validation fails
     */
    public function createOrder(array $data): array
    {
        // Start a transaction
        $this->query("START TRANSACTION");
        
        try {
            $orderId = $data['id'] ?? UuidGenerator::generate();
            
            // Insert the order
            $this->query(
                "INSERT INTO orders (id, customer_name, customer_email, address, total_amount, status, created_at) 
                 VALUES (?, ?, ?, ?, ?, ?, ?)",
                [
                    $orderId,
                    $data['customerName'],
                    $data['customerEmail'],
                    $data['address'] ?? null,
                    $data['totalAmount'],
                    $data['status'] ?? 'pending',
                    date('Y-m-d H:i:s')
                ]
            );
            
            $orderItems = [];
            $productRepo = new ProductRepository();
            $attributeRepo = new ProductAttributeRepository();
            
            if (isset($data['items']) && !empty($data['items'])) {
                foreach ($data['items'] as $item) {
                    $itemId = UuidGenerator::generate();
                    
                    $product = $productRepo->findById($item['productId']);
                    if (!$product) {
                        throw new InvalidArgumentException("Product not found: {$item['productId']}");
                    }
                    
                    $price = 0;
                    if (isset($product['prices']) && !empty($product['prices'])) {
                        $price = $product['prices'][0]['amount'] ?? 0;
                    }
                    
                    $this->query(
                        "INSERT INTO order_items (id, order_id, product_id, quantity, price) 
                         VALUES (?, ?, ?, ?, ?)",
                        [$itemId, $orderId, $item['productId'], $item['quantity'], $price]
                    );
                    
                    $createdItem = [
                        'id' => $itemId,
                        'productId' => $item['productId'],
                        'quantity' => (int)$item['quantity'],
                        'price' => (float)$price,
                        'selectedAttributes' => []
                    ];
                    
                    if (isset($item['selectedAttributes']) && !empty($item['selectedAttributes'])) {
                        foreach ($item['selectedAttributes'] as $attr) {
                            $attributeId = UuidGenerator::generate();
                            
                            $attributeInfo = $attributeRepo->getAttributeInfo(
                                $item['productId'],
                                $attr['attributeName'],
                                $attr['attributeItemId']
                            );
                            
                            $this->query(
                                "INSERT INTO order_item_attributes 
                                (id, order_item_id, attribute_name, attribute_item_id, attribute_id, attribute_items_id, display_value) 
                                VALUES (?, ?, ?, ?, ?, ?, ?)",
                                [
                                    $attributeId,
                                    $itemId,
                                    $attr['attributeName'],
                                    $attr['attributeItemId'],
                                    $attributeInfo['attribute_id'] ?? null,
                                    $attributeInfo['attribute_items_id'] ?? null,
                                    $attr['displayValue'] ?? ($attributeInfo['display_value'] ?? null)
                                ]
                            );
                            
                            $createdItem['selectedAttributes'][] = [
                                'attributeName' => $attr['attributeName'],
                                'attributeItemId' => $attr['attributeItemId'],
                                'displayValue' => $attr['displayValue'] ?? ($attributeInfo['display_value'] ?? null)
                            ];
                        }
                    }
                    
                    $orderItems[] = $createdItem;
                }
            }
            
            $this->query("COMMIT");
            
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
        } catch (\Exception $e) {
            $this->query("ROLLBACK");
            throw $e;
        }
    }
    
    /**
     * Get attribute information from the attribute repository
     * Helper method that can be moved to ProductAttributeRepository later
     * 
     * @param string $productId The product ID
     * @param string $attributeName The attribute name
     * @param string $attributeItemId The attribute item ID
     * @return array Attribute information
     */
    private function getAttributeInfo(string $productId, string $attributeName, string $attributeItemId): array
    {
        // Find the attribute ID by name
        $attributeQuery = $this->query(
            "SELECT id FROM attributes WHERE product_id = ? AND LOWER(name) = LOWER(?)",
            [$productId, $attributeName]
        );
        
        if (empty($attributeQuery)) {
            return [];
        }
        
        $attributeId = $attributeQuery[0]['id'];
        
        // Find the attribute item ID 
        $itemQuery = $this->query(
            "SELECT id, display_value FROM attribute_items 
             WHERE attribute_id = ? AND (item_id = ? OR LOWER(display_value) = LOWER(?))",
            [$attributeId, $attributeItemId, $attributeItemId]
        );
        
        if (empty($itemQuery)) {
            return ['attribute_id' => $attributeId];
        }
        
        return [
            'attribute_id' => $attributeId,
            'attribute_items_id' => $itemQuery[0]['id'],
            'display_value' => $itemQuery[0]['display_value']
        ];
    }
}