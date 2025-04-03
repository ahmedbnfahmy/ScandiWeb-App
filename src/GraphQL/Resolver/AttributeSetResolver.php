<?php

namespace App\GraphQL\Resolver;

use App\Models\Repository\AttributeSetRepository;

class AttributeSetResolver
{
    private AttributeSetRepository $repository;
    
    public function __construct()
    {
        $this->repository = new AttributeSetRepository();
    }
    
    /**
     * Get attribute sets for a product
     */
    public function getAttributeSetsForProduct(string $productId): array
    {
        try {
            return $this->repository->findByProductId($productId);
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch attribute sets: " . $e->getMessage());
        }
    }
    
    /**
     * Get attribute set by ID
     */
    public function getAttributeSet(string $id): ?array
    {
        try {
            return $this->repository->findById($id);
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch attribute set: " . $e->getMessage());
        }
    }
}