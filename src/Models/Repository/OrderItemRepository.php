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
            SELECT oi.id, oi.product_id, oi.quantity, oi.price,
                   oia.attribute_name, oia.attribute_id, oia.attribute_items_id, oia.display_value
            FROM order_items oi
            LEFT JOIN order_item_attributes oia ON oi.id = oia.order_item_id
            WHERE oi.order_id = ?
            ORDER BY oi.id
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
                    'price' => (float)$row['price'],
                    'selectedAttributes' => []
                ];
            }
            
            // Add attributes only if they exist (non-null)
            if ($row['attribute_name'] !== null) {
                $groupedItems[$itemId]['selectedAttributes'][] = [
                    'attributeName' => $row['attribute_name'],
                    'attributeItemId' => $row['attribute_items_id'],
                    'displayValue' => $row['display_value']
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
            
            $this->query(
                "INSERT INTO order_items (id, order_id, product_id, quantity, price) 
                 VALUES (?, ?, ?, ?, ?)",
                [$itemId, $orderId, $item['productId'], $item['quantity'], $price]
            );
            
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
        $attributeRepo = new ProductAttributeRepository();
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
                'price' => (float)$price,
                'selectedAttributes' => []
            ];
            
            // Process and insert selected attributes if any
            $orderItemAttributeRepo = new OrderItemAttributeRepository();
            
            if (isset($item['selectedAttributes']) && !empty($item['selectedAttributes'])) {
                // Process each attribute to lookup database IDs
                $processedAttributes = [];
                
                foreach ($item['selectedAttributes'] as $attr) {
                    // Look up attribute DB IDs
                    $attributeInfo = $attributeRepo->getAttributeInfo(
                        $item['productId'], 
                        $attr['attributeName'],
                        $attr['attributeItemId']
                    );
                    
                    if ($attributeInfo) {
                        // Add database IDs to the attribute
                        $attr['attribute_id'] = $attributeInfo['attribute_id'];
                        $attr['attribute_items_id'] = $attributeInfo['attribute_items_id'];
                        
                        // If displayValue wasn't provided, use the one from the database
                        if (!isset($attr['displayValue']) && isset($attributeInfo['display_value'])) {
                            $attr['displayValue'] = $attributeInfo['display_value'];
                        }
                    }
                    
                    $processedAttributes[] = $attr;
                }
                
                // Create attributes with the enhanced data
                $createdAttributes = $orderItemAttributeRepo->createItemAttributes($itemId, $processedAttributes);
                $createdItem['selectedAttributes'] = $createdAttributes;
            }
            
            $createdItems[] = $createdItem;
        }
        
        return $createdItems;
    }
}