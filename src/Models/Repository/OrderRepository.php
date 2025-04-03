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
     */
    public function findById(string $id): ?array
    {
        $result = $this->find($id);
        
        if (!$result) {
            return null;
        }
        
        // Format field names for GraphQL
        return [
            'id' => $result['id'],
            'customerName' => $result['customer_name'],
            'customerEmail' => $result['customer_email'],
            'address' => $result['address'],
            'totalAmount' => (float)$result['total_amount'],
            'status' => $result['status'],
            'createdAt' => $result['created_at']
        ];
    }
    
    /**
     * Find all orders
     */
    public function findAll(): array
    {
        $orders = $this->all();
        $formattedOrders = [];
        
        foreach ($orders as $order) {
            $formattedOrders[] = [
                'id' => $order['id'],
                'customerName' => $order['customer_name'],
                'customerEmail' => $order['customer_email'],
                'address' => $order['address'],
                'totalAmount' => (float)$order['total_amount'],
                'status' => $order['status'],
                'createdAt' => $order['created_at']
            ];
        }
        
        return $formattedOrders;
    }
    
    /**
     * Create new order
     */
    public function create(array $data): string
    {
        return parent::create($data);
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