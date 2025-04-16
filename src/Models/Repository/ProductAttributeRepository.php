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
     * @param string $attributeName The attribute name
     * @param string $attributeItemId The attribute value ID (display_value or item_id)
     * @return bool Whether the attribute exists for the product
     */
    public function attributeExistsForProduct(string $productId, string $attributeName, string $attributeItemId): bool
    {
        // First find the attribute matching the name (case-insensitive)
        $attribute = $this->query(
            "SELECT id FROM attributes 
             WHERE product_id = ? AND LOWER(name) = LOWER(?)",
            [$productId, $attributeName]
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
            [$attributeRowId, $attributeItemId, $attributeItemId]
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
     * @param string $attributeName The attribute name
     * @param string $attributeItemId The attribute item ID
     * @param string $value The attribute value
     * @param string|null $displayValue Optional display value
     * @return bool Whether the save was successful
     */
    public function saveAttribute(string $productId, string $attributeName, string $attributeItemId, string $value, ?string $displayValue = null): bool
    {
        // Check if attribute already exists
        if ($this->attributeExistsForProduct($productId, $attributeName, $attributeItemId)) {
            // Update existing attribute
            return $this->update(
                ['value' => $value, 'display_value' => $displayValue],
                [
                    'product_id' => $productId,
                    'attribute_name' => $attributeName,
                    'attribute_item_id' => $attributeItemId
                ]
            );
        }
        
        // Create new attribute
        return $this->create([
            'product_id' => $productId,
            'attribute_name' => $attributeName,
            'attribute_item_id' => $attributeItemId,
            'value' => $value,
            'display_value' => $displayValue
        ]) != null;
    }
    
    /**
     * Delete an attribute for a product
     * 
     * @param string $productId The product ID
     * @param string $attributeName The attribute name
     * @param string $attributeItemId The attribute item ID
     * @return bool Whether the delete was successful
     */
    public function deleteAttribute(string $productId, string $attributeName, string $attributeItemId): bool
    {
        return $this->query(
            "DELETE FROM product_attributes 
             WHERE product_id = ? AND attribute_name = ? AND attribute_item_id = ?",
            [$productId, $attributeName, $attributeItemId]
        ) !== false;
    }
    
    /**
     * Get attribute information by name and item ID
     * 
     * @param string $productId The product ID
     * @param string $attributeName The attribute name
     * @param string $attributeItemId The attribute item ID
     * @return array|null The attribute information or null if not found
     */
    public function getAttributeInfo(string $productId, string $attributeName, string $attributeItemId): ?array
    {
        // First find the attribute by name
        $attribute = $this->query(
            "SELECT id FROM attributes 
             WHERE product_id = ? AND LOWER(name) = LOWER(?)",
            [$productId, $attributeName]
        );
        
        if (empty($attribute)) {
            return null;
        }
        
        $attributeId = $attribute[0]['id'];
        
        // Then find the attribute item
        $attributeItem = $this->query(
            "SELECT id, display_value 
             FROM attribute_items 
             WHERE attribute_id = ? AND (LOWER(display_value) = LOWER(?) OR item_id = ?)",
            [$attributeId, $attributeItemId, $attributeItemId]
        );
        
        if (empty($attributeItem)) {
            // Return just the attribute ID if item not found
            return [
                'attribute_id' => $attributeId,
                'attribute_name' => $attributeName
            ];
        }
        
        // Return both IDs and the display value
        return [
            'attribute_id' => $attributeId,
            'attribute_items_id' => $attributeItem[0]['id'],
            'attribute_name' => $attributeName,
            'display_value' => $attributeItem[0]['display_value']
        ];
    }
} 