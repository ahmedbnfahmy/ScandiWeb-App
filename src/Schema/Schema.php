<?php

namespace App\Schema;

use App\Database\Database;
use PDO;
use PDOException;

class Schema
{
    private PDO $db;

    public function __construct()
    {
        try {
            $this->db = Database::getConnection();
        } catch (PDOException $e) {
            throw new PDOException("Failed to connect to database: " . $e->getMessage());
        }
    }

    /**
     * Fetch all records from a table
     * @param string $tableName
     * @return array
     */
    public function fetchAll(string $tableName): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$tableName}");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Query failed for table {$tableName}: " . $e->getMessage());
        }
    }

    /**
     * Fetch a single record by ID
     * @param string $tableName
     * @param int $id
     * @return array|null
     */
    public function fetchById(string $tableName, int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$tableName} WHERE id = ?");
            $stmt->execute([$id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            throw new PDOException("Query failed for table {$tableName}: " . $e->getMessage());
        }
    }

    /**
     * Insert data into a table
     * @param string $tableName
     * @param array $data Associative array of column => value
     * @return int Last insert ID
     */
    public function insert(string $tableName, array $data): int
    {
        try {
            $columns = implode(', ', array_keys($data));
            $values = implode(', ', array_fill(0, count($data), '?'));
            
            $stmt = $this->db->prepare("INSERT INTO {$tableName} ({$columns}) VALUES ({$values})");
            $stmt->execute(array_values($data));
            
            return (int) $this->db->lastInsertId();
        } catch (PDOException $e) {
            throw new PDOException("Insert failed for table {$tableName}: " . $e->getMessage());
        }
    }

    /**
     * Update records in a table
     * @param string $tableName
     * @param array $data Associative array of column => value
     * @param string $where WHERE clause
     * @param array $params Parameters for WHERE clause
     * @return bool
     */
    public function update(string $tableName, array $data, string $where, array $params = []): bool
    {
        try {
            $set = implode(', ', array_map(fn($col) => "{$col} = ?", array_keys($data)));
            
            $stmt = $this->db->prepare("UPDATE {$tableName} SET {$set} WHERE {$where}");
            return $stmt->execute([...array_values($data), ...$params]);
        } catch (PDOException $e) {
            throw new PDOException("Update failed for table {$tableName}: " . $e->getMessage());
        }
    }

    /**
     * Delete records from a table
     * @param string $tableName
     * @param string $where WHERE clause
     * @param array $params Parameters for WHERE clause
     * @return bool
     */
    public function delete(string $tableName, string $where, array $params = []): bool
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$tableName} WHERE {$where}");
            return $stmt->execute($params);
        } catch (PDOException $e) {
            throw new PDOException("Delete failed for table {$tableName}: " . $e->getMessage());
        }
    }

    /**
     * Execute a custom query
     * @param string $query
     * @param array $params
     * @return array
     */
    public function query(string $query, array $params = []): array
    {
        try {
            $stmt = $this->db->prepare($query);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            throw new PDOException("Query failed: " . $e->getMessage());
        }
    }
}