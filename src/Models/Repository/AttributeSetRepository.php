<?php

namespace App\Models\Repository;

use App\Database\CoreModel;

class AttributeSetRepository extends CoreModel
{
    protected function getTableName(): string
    {
        return 'attributes';
    }
    
    /**
     * Find attribute sets by product ID
     *
     * Gets all attributes associated with a specific product and formats them
     * for the GraphQL schema as AttributeSet objects with proper structure.
     * 
     * @param string $productId The product ID to fetch attributes for
     * @return array List of attribute sets with their metadata
     */
    public function findByProductId(string $productId): array
    {
        // Get all attributes for this product
        $attributeSets = $this->query(
            "SELECT id, name, type FROM attributes WHERE product_id = ?",
            [$productId]
        );
        
        if (empty($attributeSets)) {
            return [];
        }
        
        // Get attribute IDs for fetching items
        $attributeIds = array_column($attributeSets, 'id');
        $placeholders = implode(',', array_fill(0, count($attributeIds), '?'));
        
        // Get all items for these attributes in a single query (more efficient)
        $items = $this->query(
            "SELECT attribute_id, item_id as id, display_value as displayValue, value 
             FROM attribute_items 
             WHERE attribute_id IN ($placeholders)",
            $attributeIds
        );
        
        // Group items by attribute ID
        $itemsByAttribute = [];
        foreach ($items as $item) {
            $attributeId = $item['attribute_id'];
            unset($item['attribute_id']); // Remove the join field from output
            
            if (!isset($itemsByAttribute[$attributeId])) {
                $itemsByAttribute[$attributeId] = [];
            }
            
            $itemsByAttribute[$attributeId][] = $item;
        }
        
        // Add items to each attribute set
        foreach ($attributeSets as &$attributeSet) {
            $attributeId = $attributeSet['id'];
            $attributeSet['items'] = $itemsByAttribute[$attributeId] ?? [];
        }
        
        return $attributeSets;
    }
}