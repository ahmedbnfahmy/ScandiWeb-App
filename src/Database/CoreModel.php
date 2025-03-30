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
}