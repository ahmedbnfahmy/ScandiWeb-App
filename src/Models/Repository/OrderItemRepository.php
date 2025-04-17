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
            
            
            if ($row['attribute_name'] !== null) {
                $groupedItems[$itemId]['selectedAttributes'][] = [
                    'attributeName' => $row['attribute_name'],
                    'attributeItemId' => $row['attribute_items_id'],
                    'displayValue' => $row['display_value']
                ];
            }
        }
        
        
        return array_values($groupedItems);
    }
    
    
    public function saveOrderItems(string $orderId, array $items): void
    {
        $productRepo = new ProductRepository();
        
        foreach ($items as $item) {
            $itemId = UuidGenerator::generate();
            
            
            $product = $productRepo->findById($item['productId']);
            
            if (!$product) {
                throw new \InvalidArgumentException("Product not found: {$item['productId']}");
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
            
            if (isset($item['selectedAttributes']) && !empty($item['selectedAttributes'])) {
                $attributeRepo = new OrderItemAttributeRepository();
                $attributeRepo->saveItemAttributes($itemId, $item['selectedAttributes']);
            }
        }
    }
    
    
    public function findByProductId(string $productId): array
    {
        return $this->findBy(['product_id' => $productId]);
    }
    
    
    public function deleteOrderItem(string $id): bool
    {
        
        $attributeRepo = new OrderItemAttributeRepository();
        $attributeRepo->deleteByOrderItemId($id);
        
        
        return parent::delete($id);
    }
    
    
    public function createOrderItems(string $orderId, array $items): array
    {
        $productRepo = new ProductRepository();
        $attributeRepo = new ProductAttributeRepository();
        $createdItems = [];
        
        foreach ($items as $item) {
            $itemId = UuidGenerator::generate();
            
            
            $product = $productRepo->findById($item['productId']);
            
            if (!$product) {
                throw new \InvalidArgumentException("Product not found: {$item['productId']}");
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
            
            
            $orderItemAttributeRepo = new OrderItemAttributeRepository();
            
            if (isset($item['selectedAttributes']) && !empty($item['selectedAttributes'])) {
                
                $processedAttributes = [];
                
                foreach ($item['selectedAttributes'] as $attr) {
                    
                    $attributeInfo = $attributeRepo->getAttributeInfo(
                        $item['productId'], 
                        $attr['attributeName'],
                        $attr['attributeItemId']
                    );
                    
                    if ($attributeInfo) {
                        
                        $attr['attribute_id'] = $attributeInfo['attribute_id'];
                        $attr['attribute_items_id'] = $attributeInfo['attribute_items_id'];
                        
                        
                        if (!isset($attr['displayValue']) && isset($attributeInfo['display_value'])) {
                            $attr['displayValue'] = $attributeInfo['display_value'];
                        }
                    }
                    
                    $processedAttributes[] = $attr;
                }
                
                
                $createdAttributes = $orderItemAttributeRepo->createItemAttributes($itemId, $processedAttributes);
                $createdItem['selectedAttributes'] = $createdAttributes;
            }
            
            $createdItems[] = $createdItem;
        }
        
        return $createdItems;
    }
}