<?php

namespace App\Models\Repository;

use App\Database\CoreModel;
use App\Models\Entity\AttributeItem;

class AttributeItemRepository extends CoreModel
{
    protected function getTableName(): string
    {
        return 'attribute_items';
    }
    
    /**
     * Find attribute items by attribute ID
     * 
     * @param string $attributeId The parent attribute ID
     * @return array Array of attribute items
     */
    public function findByAttributeId(string $attributeId): array
    {
        return $this->query(
            "SELECT item_id as id, display_value as displayValue, value 
             FROM attribute_items 
             WHERE attribute_id = ?",
            [$attributeId]
        );
    }
    
    /**
     * Find attribute item by ID
     * 
     * @param string $id Attribute item ID
     * @return array|null Attribute item data or null if not found
     */
    public function findById(string $id): ?array
    {
        $result = $this->query(
            "SELECT item_id as id, display_value as displayValue, value 
             FROM attribute_items 
             WHERE item_id = ?",
            [$id]
        );
        
        return $result[0] ?? null;
    }
    
    /**
     * Check if an attribute item exists
     * 
     * @param string $itemId The item ID
     * @return bool Whether the attribute item exists
     */
    public function attributeItemExists(string $itemId): bool
    {
        $result = $this->query(
            "SELECT COUNT(*) as count FROM attribute_items WHERE item_id = ?",
            [$itemId]
        );
        
        return !empty($result) && (int)$result[0]['count'] > 0;
    }
    
    /**
     * Check if an item belongs to an attribute
     * 
     * @param string $attributeId The attribute ID
     * @param string $itemId The item ID
     * @return bool Whether the item belongs to the attribute
     */
    public function itemBelongsToAttribute(string $attributeId, string $itemId): bool
    {
        $result = $this->query(
            "SELECT COUNT(*) as count 
             FROM attribute_items 
             WHERE attribute_id = ? AND item_id = ?",
            [$attributeId, $itemId]
        );
        
        return !empty($result) && (int)$result[0]['count'] > 0;
    }
   
} 