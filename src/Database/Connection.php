<?php

namespace App\Database;

use PDO;
use PDOException;

class Connection
{
    protected PDO $db;

    public function __construct()
    {
        try {
            $this->db = Database::getConnection();
        } catch (PDOException $e) {
            throw new PDOException("Failed to connect to database: " . $e->getMessage());
        }
    }

    protected function getDb(): PDO
    {
        return $this->db;
    }
}