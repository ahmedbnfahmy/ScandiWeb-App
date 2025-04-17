<?php

namespace App\GraphQL\Resolver;

use App\Models\Repository\PriceRepository;

class PriceResolver
{
    private PriceRepository $repository;
    
    public function __construct()
    {
        $this->repository = new PriceRepository();
    }
    
    
    public function getPricesForProduct(string $productId): array
    {
        try {
            return $this->repository->findByProductId($productId);
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch prices: " . $e->getMessage());
        }
    }
}