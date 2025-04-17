<?php

namespace App\GraphQL\Resolver;

use App\Models\Repository\AttributeItemRepository;

class AttributeItemResolver
{
    private AttributeItemRepository $repository;
    
    public function __construct()
    {
        $this->repository = new AttributeItemRepository();
    }
    
    
    public function findByAttributeId(string $attributeId): array
    {
        try {
            return $this->repository->findByAttributeId($attributeId);
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch attribute items: " . $e->getMessage());
        }
    }
    
    
    public function findById(string $id): ?array
    {
        try {
            return $this->repository->findById($id);
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch attribute item: " . $e->getMessage());
        }
    }
    
    
    public function validateAttributeItem(string $productId, string $attributeName, string $itemId): bool
    {
        try {
            $productAttributeRepo = new \App\Models\Repository\ProductAttributeRepository();
            return $productAttributeRepo->attributeExistsForProduct($productId, $attributeName, $itemId);
        } catch (\Exception $e) {
            throw new \Exception("Failed to validate attribute item: " . $e->getMessage());
        }
    }
} 