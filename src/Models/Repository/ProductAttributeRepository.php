<?php

namespace App\Models\Repository;

use App\Database\CoreModel;

class ProductAttributeRepository extends CoreModel
{
    protected function getTableName(): string
    {
        return 'attributes';
    }
    
    /**
     * Check if an attribute exists for a product
     * 
     * @param string $productId The product ID
     * @param string $attributeSetId The attribute set ID (name in lowercase)
     * @param string $attributeId The attribute value ID (display_value or item_id)
     * @return bool Whether the attribute exists for the product
     */
    public function attributeExistsForProduct(string $productId, string $attributeSetId, string $attributeId): bool
    {
        // First find the attribute matching the name (case-insensitive)
        $attribute = $this->query(
            "SELECT id FROM attributes 
             WHERE product_id = ? AND LOWER(name) = LOWER(?)",
            [$productId, $attributeSetId]
        );
        
        if (empty($attribute)) {
            return false;
        }
        
        $attributeRowId = $attribute[0]['id'];
        
        // Then check if the attribute value exists
        $result = $this->query(
            "SELECT COUNT(*) as count 
             FROM attribute_items 
             WHERE attribute_id = ? AND (LOWER(display_value) = LOWER(?) OR item_id = ?)",
            [$attributeRowId, $attributeId, $attributeId]
        );
        
        return !empty($result) && (int)$result[0]['count'] > 0;
    }
    
    /**
     * Get all attributes for a product
     * 
     * @param string $productId The product ID
     * @return array The attributes with their items
     */
    public function getAttributesForProduct(string $productId): array
    {
        // Get all attributes for this product
        $attributes = $this->query(
            "SELECT id, name, type 
             FROM attributes 
             WHERE product_id = ?",
            [$productId]
        );
        
        if (empty($attributes)) {
            return [];
        }
        
        // For each attribute, get its items
        $result = [];
        foreach ($attributes as $attribute) {
            $items = $this->query(
                "SELECT item_id, display_value, value 
                 FROM attribute_items 
                 WHERE attribute_id = ?",
                [$attribute['id']]
            );
            
            $result[] = [
                'id' => $attribute['id'],
                'name' => $attribute['name'],
                'type' => $attribute['type'],
                'items' => $items
            ];
        }
        
        return $result;
    }
    
    /**
     * Get attribute items by attribute ID
     * 
     * @param string $attributeId The attribute ID
     * @return array The attribute items
     */
    public function getAttributeItems(string $attributeId): array
    {
        return $this->query(
            "SELECT item_id as id, display_value as displayValue, value 
             FROM attribute_items 
             WHERE attribute_id = ?",
            [$attributeId]
        );
    }
    
    /**
     * Save attribute for a product
     * 
     * @param string $productId The product ID
     * @param string $attributeSetId The attribute set ID
     * @param string $attributeId The attribute ID
     * @param string $value The attribute value
     * @return bool Whether the save was successful
     */
    public function saveAttribute(string $productId, string $attributeSetId, string $attributeId, string $value): bool
    {
        // Check if attribute already exists
        if ($this->attributeExistsForProduct($productId, $attributeSetId, $attributeId)) {
            // Update existing attribute
            return $this->update(
                ['value' => $value],
                [
                    'product_id' => $productId,
                    'attribute_set_id' => $attributeSetId,
                    'attribute_id' => $attributeId
                ]
            );
        }
        
        // Create new attribute
        return $this->create([
            'product_id' => $productId,
            'attribute_set_id' => $attributeSetId,
            'attribute_id' => $attributeId,
            'value' => $value
        ]) != null;
    }
    
    /**
     * Delete an attribute for a product
     * 
     * @param string $productId The product ID
     * @param string $attributeSetId The attribute set ID
     * @param string $attributeId The attribute ID
     * @return bool Whether the delete was successful
     */
    public function deleteAttribute(string $productId, string $attributeSetId, string $attributeId): bool
    {
        return $this->query(
            "DELETE FROM product_attributes 
             WHERE product_id = ? AND attribute_set_id = ? AND attribute_id = ?",
            [$productId, $attributeSetId, $attributeId]
        ) !== false;
    }
} 