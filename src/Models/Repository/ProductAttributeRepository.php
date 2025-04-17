<?php

namespace App\Models\Repository;

use App\Database\CoreModel;

class ProductAttributeRepository extends CoreModel
{
    protected function getTableName(): string
    {
        return 'attributes';
    }
    
    
    public function attributeExistsForProduct(string $productId, string $attributeName, string $attributeItemId): bool
    {
        
        $attribute = $this->query(
            "SELECT id FROM attributes 
             WHERE product_id = ? AND LOWER(name) = LOWER(?)",
            [$productId, $attributeName]
        );
        
        if (empty($attribute)) {
            return false;
        }
        
        $attributeRowId = $attribute[0]['id'];
        
        
        $result = $this->query(
            "SELECT COUNT(*) as count 
             FROM attribute_items 
             WHERE attribute_id = ? AND (LOWER(display_value) = LOWER(?) OR item_id = ?)",
            [$attributeRowId, $attributeItemId, $attributeItemId]
        );
        
        return !empty($result) && (int)$result[0]['count'] > 0;
    }
    
    
    public function getAttributesForProduct(string $productId): array
    {
        
        $attributes = $this->query(
            "SELECT id, name, type 
             FROM attributes 
             WHERE product_id = ?",
            [$productId]
        );
        
        if (empty($attributes)) {
            return [];
        }
        
        
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
    
    
    public function getAttributeItems(string $attributeId): array
    {
        return $this->query(
            "SELECT item_id as id, display_value as displayValue, value 
             FROM attribute_items 
             WHERE attribute_id = ?",
            [$attributeId]
        );
    }
    
    
    public function saveAttribute(string $productId, string $attributeName, string $attributeItemId, string $value, ?string $displayValue = null): bool
    {
        
        if ($this->attributeExistsForProduct($productId, $attributeName, $attributeItemId)) {
            
            return $this->update(
                ['value' => $value, 'display_value' => $displayValue],
                [
                    'product_id' => $productId,
                    'attribute_name' => $attributeName,
                    'attribute_item_id' => $attributeItemId
                ]
            );
        }
        
        
        return $this->create([
            'product_id' => $productId,
            'attribute_name' => $attributeName,
            'attribute_item_id' => $attributeItemId,
            'value' => $value,
            'display_value' => $displayValue
        ]) != null;
    }
    
    
    public function deleteAttribute(string $productId, string $attributeName, string $attributeItemId): bool
    {
        return $this->query(
            "DELETE FROM product_attributes 
             WHERE product_id = ? AND attribute_name = ? AND attribute_item_id = ?",
            [$productId, $attributeName, $attributeItemId]
        ) !== false;
    }
    
    
    public function getAttributeInfo(string $productId, string $attributeName, string $attributeItemId): ?array
    {
        
        $attribute = $this->query(
            "SELECT id FROM attributes 
             WHERE product_id = ? AND LOWER(name) = LOWER(?)",
            [$productId, $attributeName]
        );
        
        if (empty($attribute)) {
            return null;
        }
        
        $attributeId = $attribute[0]['id'];
        
        
        $attributeItem = $this->query(
            "SELECT id, display_value 
             FROM attribute_items 
             WHERE attribute_id = ? AND (LOWER(display_value) = LOWER(?) OR item_id = ?)",
            [$attributeId, $attributeItemId, $attributeItemId]
        );
        
        if (empty($attributeItem)) {
            
            return [
                'attribute_id' => $attributeId,
                'attribute_name' => $attributeName
            ];
        }
        
        
        return [
            'attribute_id' => $attributeId,
            'attribute_items_id' => $attributeItem[0]['id'],
            'attribute_name' => $attributeName,
            'display_value' => $attributeItem[0]['display_value']
        ];
    }
} 