<?php

namespace App\Models;

use App\Database\CoreModel;

class Product extends CoreModel
{
    protected function getTableName(): string
    {
        return 'products';
    }

    /**
     * Find products by category
     */
    public function findByCategory(string $category): array
    {
        return $this->findBy(['category' => $category]);
    }

    /**
     * Update product stock status
     */
    public function updateStock(int $id, bool $inStock): bool
    {
        return $this->update(
            ['in_stock' => $inStock],
            ['id' => $id]
        );
    }

    
    public function search(string $term): array
    {
        return $this->query(
            "SELECT * FROM products WHERE name LIKE ? OR description LIKE ?",
            ["%{$term}%", "%{$term}%"]
        );
    }

    
    public function getByPriceRange(float $minPrice, float $maxPrice): array
    {
        return $this->query(
            "SELECT * FROM products WHERE price BETWEEN ? AND ?",
            [$minPrice, $maxPrice]
        );
    }
    public function getAttributes(string $productId): array
    {
        $attributeRows = $this->query(
            "SELECT a.id as attribute_id, a.name, a.type 
             FROM attributes a
             WHERE a.product_id = ?",
            [$productId]
        );
        
        $result = [];
        foreach ($attributeRows as $attr) {
            $attrId = $attr['attribute_id'];
            
            // Get values for this attribute
            $values = $this->query(
                "SELECT id as value_id, display_value, value
                 FROM attribute_items
                 WHERE attribute_id = ?",
                [$attrId]
            );
            
            // Structure the attribute with its values
            $attribute = [
                'id' => $attrId,
                'name' => $attr['name'],
                'type' => $attr['type'],
                'items' => []
            ];
            
            // Add items to the attribute
            foreach ($values as $value) {
                $attribute['items'][] = [
                    'id' => $value['value_id'],
                    'displayValue' => $value['display_value'],
                    'value' => $value['value']
                ];
            }
            
            $result[] = $attribute;
        }
        
        return $result;
    }

}