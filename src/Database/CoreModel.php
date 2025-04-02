<?php

namespace App\Database;

use PDO;
use PDOException;

abstract class CoreModel extends Connection
{
    abstract protected function getTableName(): string;

    /**
     * Fetch all records
     */
    public function all(): array
    {
        try {
            $stmt = $this->getDb()->prepare("SELECT * FROM {$this->getTableName()}");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Query failed: " . $e->getMessage());
        }
    }

    /**
     * Find by ID
     */
    public function find(int $id): ?array
    {
        try {
            $stmt = $this->getDb()->prepare("SELECT * FROM {$this->getTableName()} WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            throw new PDOException("Find failed: " . $e->getMessage());
        }
    }

    /**
     * Create new record
     */
    public function create(array $data): int
    {
        try {
            $columns = implode(', ', array_keys($data));
            $values = implode(', ', array_fill(0, count($data), '?'));
            
            $stmt = $this->getDb()->prepare(
                "INSERT INTO {$this->getTableName()} ({$columns}) VALUES ({$values})"
            );
            $stmt->execute(array_values($data));
            
            return (int) $this->getDb()->lastInsertId();
        } catch (PDOException $e) {
            throw new PDOException("Create failed: " . $e->getMessage());
        }
    }

    /**
     * Update record
     */
    public function update(array $data, array $conditions): bool
    {
        try {
            $set = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
            $where = implode(' AND ', array_map(fn($col) => "{$col} = ?", array_keys($conditions)));
            
            $stmt = $this->getDb()->prepare(
                "UPDATE {$this->getTableName()} SET {$set} WHERE {$where}"
            );
            return $stmt->execute([...array_values($data), ...array_values($conditions)]);
        } catch (PDOException $e) {
            throw new PDOException("Update failed: " . $e->getMessage());
        }
    }

    /**
     * Delete record
     */
    public function delete(array $conditions): bool
    {
        try {
            $where = implode(' AND ', array_map(fn($col) => "{$col} = ?", array_keys($conditions)));
            
            $stmt = $this->getDb()->prepare(
                "DELETE FROM {$this->getTableName()} WHERE {$where}"
            );
            return $stmt->execute(array_values($conditions));
        } catch (PDOException $e) {
            throw new PDOException("Delete failed: " . $e->getMessage());
        }
    }

    /**
     * Custom query
     */
    public function query(string $query, array $params = []): array
    {
        try {
            $stmt = $this->getDb()->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Query failed: " . $e->getMessage());
        }
    }

    /**
     * Find by conditions
     */
    public function findBy(array $conditions): array
    {
        try {
            $where = implode(' AND ', array_map(fn($col) => "{$col} = ?", array_keys($conditions)));
            
            $stmt = $this->getDb()->prepare(
                "SELECT * FROM {$this->getTableName()} WHERE {$where}"
            );
            $stmt->execute(array_values($conditions));
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("FindBy failed: " . $e->getMessage());
        }
    }

    
public function getAllProductsWithAttributes(): array
{
    // First, get all products
    $products = $this->all();
    $productIds = array_column($products, 'id');
    
    if (empty($productIds)) {
        return [];
    }
    
    // Create placeholders for the IN clause
    $placeholders = implode(',', array_fill(0, count($productIds), '?'));
    
    // Fetch attributes for all products in a single query
    $allAttributes = $this->query(
        "SELECT pa.product_id, pa.attribute_id, a.name, a.type, 
                pa.value_id, v.display_value, v.value
         FROM product_attributes pa
         JOIN attributes a ON pa.attribute_id = a.id
         JOIN attribute_items v ON pa.value_id = v.id
         WHERE pa.product_id IN ($placeholders)",
        $productIds
    );
    
    // Group attributes by product
    $attributesByProduct = [];
    foreach ($allAttributes as $attr) {
        $productId = $attr['product_id'];
        $attrId = $attr['attribute_id'];
        
        if (!isset($attributesByProduct[$productId])) {
            $attributesByProduct[$productId] = [];
        }
        
        if (!isset($attributesByProduct[$productId][$attrId])) {
            $attributesByProduct[$productId][$attrId] = [
                'id' => $attrId,
                'name' => $attr['name'],
                'type' => $attr['type'],
                'items' => []
            ];
        }
        
        $attributesByProduct[$productId][$attrId]['items'][] = [
            'displayValue' => $attr['display_value'],
            'value' => $attr['value'],
            'id' => $attr['value_id']
        ];
    }
    
    // Add attributes to each product
    foreach ($products as &$product) {
        $id = $product['id'];
        $product['attributes'] = isset($attributesByProduct[$id]) 
            ? array_values($attributesByProduct[$id]) 
            : [];
            
        // Convert in_stock to inStock for GraphQL schema
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
