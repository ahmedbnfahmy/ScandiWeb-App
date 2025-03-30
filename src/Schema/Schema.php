<?php 

use App\Database\Database;

// Get database connection
$db = Database::getConnection();

// Example query
$stmt = $db->query("SELECT * FROM your_table");
$results = $stmt->fetchAll();