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
    
    
    public function findByAttributeId(string $attributeId): array
    {
        return $this->query(
            "SELECT item_id as id, display_value as displayValue, value 
             FROM attribute_items 
             WHERE attribute_id = ?",
            [$attributeId]
        );
    }
    
    
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
    
    
    public function attributeItemExists(string $itemId): bool
    {
        $result = $this->query(
            "SELECT COUNT(*) as count FROM attribute_items WHERE item_id = ?",
            [$itemId]
        );
        
        return !empty($result) && (int)$result[0]['count'] > 0;
    }
    
    
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