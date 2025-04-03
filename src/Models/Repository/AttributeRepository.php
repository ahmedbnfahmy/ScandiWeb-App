<?php

namespace App\Models\Repository;

use App\Database\CoreModel;

class AttributeRepository extends CoreModel
{
    protected function getTableName(): string
    {
        return 'attribute_items';
    }
    
    /**
     * Find attribute items by attribute set ID
     */
    public function findByAttributeSetId(string $attributeSetId): array
    {
        return $this->query(
            "SELECT item_id as id, display_value as displayValue, value 
             FROM attribute_items 
             WHERE attribute_id = ?",
            [$attributeSetId]
        );
    }
    
    /**
     * Find attribute by ID
     * 
     * @param string $id Attribute ID
     * @return array|null Attribute data or null if not found
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
}