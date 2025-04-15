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
    
    /**
     * Find attribute items by attribute ID
     * 
     * @param string $attributeId The attribute ID
     * @return array The attribute items
     */
    public function findByAttributeId(string $attributeId): array
    {
        try {
            return $this->repository->findByAttributeId($attributeId);
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch attribute items: " . $e->getMessage());
        }
    }
    
    /**
     * Find an attribute item by ID
     * 
     * @param string $id The attribute item ID
     * @return array|null The attribute item or null if not found
     */
    public function findById(string $id): ?array
    {
        try {
            return $this->repository->findById($id);
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch attribute item: " . $e->getMessage());
        }
    }
    
    /**
     * Validate if attribute item is valid for a product and attribute
     * 
     * @param string $productId Product ID
     * @param string $attributeName Attribute name
     * @param string $itemId Item ID
     * @return bool Whether the attribute item is valid
     */
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