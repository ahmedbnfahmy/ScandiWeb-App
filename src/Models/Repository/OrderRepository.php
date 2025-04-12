<?php

namespace App\Models\Repository;

use App\Database\CoreModel;

class OrderRepository extends CoreModel
{
    protected function getTableName(): string
    {
        return 'orders';
    }
    
    /**
     * Find order by ID
     * 
     * @param string $id The order ID
     * @return array|null The formatted order data or null if not found
     */
    public function findById(string $id): ?array
    {
        $result = $this->find($id);
        
        if (!$result) {
            return null;
        }
        
        return $this->formatOrder($result);
    }
    
    /**
     * Find all orders
     * 
     * @return array List of formatted orders
     */
    public function findAll(): array
    {
        $orders = $this->all();
        $formattedOrders = [];
        
        foreach ($orders as $order) {
            $formattedOrders[] = $this->formatOrder($order);
        }
        
        return $formattedOrders;
    }
    
    /**
     * Create new order with complete order data
     * 
     * @param array $orderData Complete order data including customer info and total
     * @return array The created order with formatted fields
     */
    public function createOrder(array $orderData): array
    {
        $dbData = [
            'id' => $orderData['id'] ?? uniqid('order_'),
            'customer_name' => $orderData['customerName'] ?? 'Guest',
            'customer_email' => $orderData['customerEmail'] ?? 'guest@example.com',
            'address' => $orderData['address'] ?? null,
            'total_amount' => $orderData['totalAmount'],
            'status' => $orderData['status'] ?? 'pending',
        ];
        
        $orderId = $this->create($dbData);
        
        return $this->findById($orderId);
    }
    
    /**
     * Find orders by customer email
     * 
     * @param string $email Customer email address
     * @return array List of formatted orders
     */
    public function findByCustomerEmail(string $email): array
    {
        $query = "SELECT * FROM {$this->getTableName()} WHERE customer_email = :email ORDER BY created_at DESC";
        $stmt = $this->getDb()->prepare($query);
        $stmt->execute(['email' => $email]);
        
        $orders = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        $formattedOrders = [];
        
        foreach ($orders as $order) {
            $formattedOrders[] = $this->formatOrder($order);
        }
        
        return $formattedOrders;
    }
    
    /**
     * Update order status
     * 
     * @param string $id Order ID
     * @param string $status New status
     * @return bool Success flag
     */
    public function updateStatus(string $id, string $status): bool
    {
        return $this->update($id, ['status' => $status]);
    }
    
    /**
     * Format database order record for GraphQL
     * 
     * @param array $order Raw database order record
     * @return array Formatted order for GraphQL
     */
    private function formatOrder(array $order): array
    {
        return [
            'id' => $order['id'],
            'customerName' => $order['customer_name'],
            'customerEmail' => $order['customer_email'],
            'address' => $order['address'],
            'totalAmount' => (float)$order['total_amount'],
            'status' => $order['status'],
            'createdAt' => $order['created_at']
        ];
    }
    
    /**
     * Begin a database transaction
     */
    public function beginTransaction(): void
    {
        $this->getDb()->beginTransaction();
    }
    
    /**
     * Commit a database transaction
     */
    public function commit(): void
    {
        $this->getDb()->commit();
    }
    
    /**
     * Rollback a database transaction
     */
    public function rollback(): void
    {
        $this->getDb()->rollBack();
    }
}