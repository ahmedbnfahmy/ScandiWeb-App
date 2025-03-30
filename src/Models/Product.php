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

    /**
     * Search products by term
     */
    public function search(string $term): array
    {
        return $this->query(
            "SELECT * FROM products WHERE name LIKE ? OR description LIKE ?",
            ["%{$term}%", "%{$term}%"]
        );
    }

    /**
     * Get products by price range
     */
    public function getByPriceRange(float $minPrice, float $maxPrice): array
    {
        return $this->query(
            "SELECT * FROM products WHERE price BETWEEN ? AND ?",
            [$minPrice, $maxPrice]
        );
    }
    public function getAllProductsWithAttributes(): array
    {
        // First, get all products
        $products = $this->all();
        
        foreach ($products as &$product) {
            $attributeRows = $this->getAttributes($product['id']);
            
            // Group attributes by attribute ID
            $attributeSets = [];
            foreach ($attributeRows as $row) {
                $attrId = $row['attribute_id'];
                
                // Create new attribute set if it doesn't exist yet
                if (!isset($attributeSets[$attrId])) {
                    $attributeSets[$attrId] = [
                        'id' => $attrId,
                        'name' => $row['name'],
                        'type' => $row['type'],
                        'items' => []
                    ];
                }
                
                // Add this value to the attribute's items
                $attributeSets[$attrId]['items'][] = [
                    'displayValue' => $row['display_value'],
                    'value' => $row['value'],
                    'id' => $row['value_id']
                ];
            }
            
            $product['attributes'] = array_values($attributeSets);
            
            if (isset($product['in_stock'])) {
                $product['inStock'] = (bool)$product['in_stock'];
            }
            
            // Parse gallery JSON if it exists
            if (isset($product['gallery']) && is_string($product['gallery'])) {
                $product['gallery'] = json_decode($product['gallery'], true) ?? [];
            }
        }
        
        return $products;
    }

}