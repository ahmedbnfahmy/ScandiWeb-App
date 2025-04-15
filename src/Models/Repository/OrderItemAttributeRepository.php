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
                "INSERT INTO order_item_attributes (id, order_item_id, attribute_set_id, attribute_id) 
                 VALUES (?, ?, ?, ?)",
                [
                    $attributeId,
                    $orderItemId, 
                    $attribute['attributeSetId'], 
                    $attribute['attributeId']
                ]
            );
            
            $createdAttributes[] = [
                'attributeSetId' => $attribute['attributeSetId'],
                'attributeId' => $attribute['attributeId']
            ];
        }
        
        return $createdAttributes;
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
        return $this->findBy(['order_item_id' => $orderItemId]);
    }
} 