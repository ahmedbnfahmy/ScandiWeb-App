<?php

namespace App\Models\Repository;

use App\Database\CoreModel;
use App\Util\UuidGenerator;

class OrderItemAttributeRepository extends CoreModel
{
    protected function getTableName(): string
    {
        return 'order_item_attributes';
    }
    
    /**
     * Create attributes for an order item
     * 
     * @param string $orderItemId The order item ID
     * @param array $attributes The attributes to save
     * @return array The created attributes
     */
    public function createItemAttributes(string $orderItemId, array $attributes): array
    {
        $createdAttributes = [];
        
        foreach ($attributes as $attribute) {
            $attributeId = UuidGenerator::generate();
            
            $this->query(
                "INSERT INTO order_item_attributes 
                (id, order_item_id, attribute_name, attribute_id, attribute_items_id, display_value) 
                VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $attributeId,
                    $orderItemId, 
                    $attribute['attributeName'],
                    $attribute['attribute_id'] ?? null,
                    $attribute['attribute_items_id'] ?? null, 
                    $attribute['displayValue'] ?? null
                ]
            );
            
            $createdAttributes[] = [
                'attributeName' => $attribute['attributeName'],
                'attributeItemId' => $attribute['attributeItemId'],
                'displayValue' => $attribute['displayValue'] ?? null
            ];
        }
        
        return $createdAttributes;
    }
    
    /**
     * Save attributes for an order item
     * 
     * @param string $orderItemId The order item ID
     * @param array $attributes The attributes to save
     * @return void
     */
    public function saveItemAttributes(string $orderItemId, array $attributes): void
    {
        foreach ($attributes as $attribute) {
            $attributeId = UuidGenerator::generate();
            
            $this->query(
                "INSERT INTO order_item_attributes 
                (id, order_item_id, attribute_name, attribute_id, attribute_items_id, display_value) 
                VALUES (?, ?, ?, ?, ?, ?)",
                [
                    $attributeId,
                    $orderItemId, 
                    $attribute['attributeName'],
                    $attribute['attribute_id'] ?? null,
                    $attribute['attribute_items_id'] ?? null,
                    $attribute['displayValue'] ?? null
                ]
            );
        }
    }
    
    /**
     * Delete attributes by order item ID
     * 
     * @param string $orderItemId The order item ID
     * @return bool Whether the delete was successful
     */
    public function deleteByOrderItemId(string $orderItemId): bool
    {
        return $this->query(
            "DELETE FROM order_item_attributes WHERE order_item_id = ?",
            [$orderItemId]
        ) !== false;
    }
    
    /**
     * Get attributes for an order item
     * 
     * @param string $orderItemId The order item ID
     * @return array The attributes
     */
    public function getAttributesByOrderItemId(string $orderItemId): array
    {
        $attributes = $this->findBy(['order_item_id' => $orderItemId]);
        
        // Transform attribute keys to GraphQL format
        return array_map(function($attr) {
            return [
                'attributeName' => $attr['attribute_name'],
                'attributeItemId' => $attr['attribute_item_id'],
                'displayValue' => $attr['display_value'] ?? null
            ];
        }, $attributes);
    }
} 