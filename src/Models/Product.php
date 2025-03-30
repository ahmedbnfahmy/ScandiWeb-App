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
}