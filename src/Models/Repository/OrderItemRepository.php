<?php

namespace App\Models\Repository;

use App\Database\CoreModel;

class OrderItemRepository extends CoreModel
{
    protected function getTableName(): string
    {
        return 'order_items';
    }
    
    /**
     * Find order items by order ID
     */
    public function findByOrderId(string $orderId): array
    {
        return $this->query(
            "SELECT id, order_id as orderId, product_id as productId, 
                    quantity, unit_price as unitPrice, selected_attributes as selectedAttributes
             FROM {$this->getTableName()} 
             WHERE order_id = ?",
            [$orderId]
        );
    }
    
    /**
     * Create new order item
     */
    public function create(array $data): string
    {
        return parent::create($data);
    }
}