<?php

namespace App\Models\Repository;

use App\Database\CoreModel;
use App\Util\UuidGenerator;

class OrderItemRepository extends CoreModel
{
    protected function getTableName(): string
    {
        return 'order_items';
    }
    
    /**
     * Get items for a specific order
     * 
     * @param string $orderId The order ID
     * @return array Order items with their attributes
     */
    public function getOrderItems(string $orderId): array
    {
        $query = "
            SELECT oi.id, oi.product_id, oi.quantity, 
                   oia.attribute_set_id, oia.attribute_id
            FROM order_items oi
            LEFT JOIN order_item_attributes oia ON oi.id = oia.order_item_id
            WHERE oi.order_id = ?
            ORDER BY oi.id, oia.attribute_set_id, oia.attribute_id
        ";
        
        $results = $this->query($query, [$orderId]);
        
        // Group the results by order item
        $groupedItems = [];
        foreach ($results as $row) {
            $itemId = $row['id'];
            
            if (!isset($groupedItems[$itemId])) {
                $groupedItems[$itemId] = [
                    'id' => $itemId,
                    'productId' => $row['product_id'],
                    'quantity' => (int)$row['quantity'],
                    'selectedAttributes' => []
                ];
            }
            
            // Add attributes only if they exist (non-null)
            if ($row['attribute_set_id'] !== null && $row['attribute_id'] !== null) {
                $groupedItems[$itemId]['selectedAttributes'][] = [
                    'attributeSetId' => $row['attribute_set_id'],
                    'attributeId' => $row['attribute_id']
                ];
            }
        }
        
        // Convert to indexed array
        return array_values($groupedItems);
    }
    
    /**
     * Save order items and their attributes
     * 
     * @param string $orderId The order ID
     * @param array $items The order items
     * @return void
     */
    public function saveOrderItems(string $orderId, array $items): void
    {
        $productRepo = new ProductRepository();
        
        foreach ($items as $item) {
            $itemId = UuidGenerator::generate();
            
            // Get the product to fetch its price
            $product = $productRepo->findById($item['productId']);
            
            if (!$product) {
                throw new \InvalidArgumentException("Product not found: {$item['productId']}");
            }
            
            // Get the first price from the product (or use default if not found)
            $price = 0;
            if (isset($product['prices']) && !empty($product['prices'])) {
                $price = $product['prices'][0]['amount'] ?? 0;
            }
            
            // Insert order item with price
            $this->query(
                "INSERT INTO order_items (id, order_id, product_id, quantity, price) 
                 VALUES (?, ?, ?, ?, ?)",
                [$itemId, $orderId, $item['productId'], $item['quantity'], $price]
            );
            
            // Insert selected attributes if any
            if (isset($item['selectedAttributes']) && !empty($item['selectedAttributes'])) {
                $attributeRepo = new OrderItemAttributeRepository();
                $attributeRepo->saveItemAttributes($itemId, $item['selectedAttributes']);
            }
        }
    }
    
    /**
     * Find order items by product ID
     * 
     * @param string $productId The product ID
     * @return array Order items
     */
    public function findByProductId(string $productId): array
    {
        return $this->findBy(['product_id' => $productId]);
    }
    
    /**
     * Delete order item
     * 
     * @param string $id The order item ID
     * @return bool Whether the delete was successful
     */
    public function deleteOrderItem(string $id): bool
    {
        // Delete related attributes first
        $attributeRepo = new OrderItemAttributeRepository();
        $attributeRepo->deleteByOrderItemId($id);
        
        // Then delete the order item
        return parent::delete($id);
    }
    
    /**
     * Create order items for an order
     * 
     * @param string $orderId The order ID
     * @param array $items The order items data
     * @return array The created order items with IDs
     */
    public function createOrderItems(string $orderId, array $items): array
    {
        $productRepo = new ProductRepository();
        $createdItems = [];
        
        foreach ($items as $item) {
            $itemId = UuidGenerator::generate();
            
            // Get the product to fetch its price
            $product = $productRepo->findById($item['productId']);
            
            if (!$product) {
                throw new \InvalidArgumentException("Product not found: {$item['productId']}");
            }
            
            // Get the first price from the product (or use default if not found)
            $price = 0;
            if (isset($product['prices']) && !empty($product['prices'])) {
                $price = $product['prices'][0]['amount'] ?? 0;
            }
            
            // Insert order item with price
            $this->query(
                "INSERT INTO order_items (id, order_id, product_id, quantity, price) 
                 VALUES (?, ?, ?, ?, ?)",
                [$itemId, $orderId, $item['productId'], $item['quantity'], $price]
            );
            
            // Create the item data structure for return
            $createdItem = [
                'id' => $itemId,
                'productId' => $item['productId'],
                'quantity' => (int)$item['quantity'],
                'price' => (float)$price
            ];
            
            // Insert selected attributes if any
            $attributeRepo = new OrderItemAttributeRepository();
            
            if (isset($item['selectedAttributes']) && !empty($item['selectedAttributes'])) {
                $attributeRepo->createItemAttributes($itemId, $item['selectedAttributes']);
            }
            
            $createdItems[] = $createdItem;
        }
        
        return $createdItems;
    }
}