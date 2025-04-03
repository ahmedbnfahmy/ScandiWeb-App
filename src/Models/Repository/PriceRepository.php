<?php

namespace App\Models\Repository;

use App\Database\CoreModel;

class PriceRepository extends CoreModel
{
    protected function getTableName(): string
    {
        return 'prices';
    }
    
    /**
     * Find prices by product ID
     */
    public function findByProductId(string $productId): array
    {
        $prices = $this->query(
            "SELECT amount, currency_label as label, currency_symbol as symbol 
             FROM prices 
             WHERE product_id = ?",
            [$productId]
        );
        
        // Format prices to match GraphQL schema
        $result = [];
        foreach ($prices as $price) {
            $result[] = [
                'amount' => (float)$price['amount'],
                'currency' => [
                    'label' => $price['label'],
                    'symbol' => $price['symbol']
                ]
            ];
        }
        
        return $result;
    }
}