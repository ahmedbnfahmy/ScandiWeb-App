<?php

namespace App\GraphQL\Resolver;

use App\Models\Repository\AttributeRepository;

class AttributeResolver
{
    private AttributeRepository $repository;
    
    public function __construct()
    {
        $this->repository = new AttributeRepository();
    }
    
    /**
     * Get a single attribute by ID
     * 
     * @param string $id Attribute ID
     * @return array|null Attribute data or null if not found
     */
    public function getAttribute(string $id): ?array
    {
        try {
            $attribute = $this->repository->findById($id);
            
            if (!$attribute) {
                return null;
            }
            
            return $attribute->toGraphQL();
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch attribute: " . $e->getMessage());
        }
    }
    
    /**
     * Get all attributes for a product
     * 
     * @param string $productId Product ID
     * @return array List of attributes
     */
    public function getAttributesForProduct(string $productId): array
    {
        try {
            $attributes = $this->repository->findByProductIdWithItems($productId);
            
            // Convert to GraphQL format
            return array_map(function($attribute) {
                return $attribute->toGraphQL();
            }, $attributes);
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch attributes: " . $e->getMessage());
        }
    }
    
    /**
     * Get attribute by name
     * 
     * @param string $name Attribute name
     * @return array|null Attribute data or null if not found
     */
    public function getAttributeByName(string $name): ?array
    {
        try {
            $attribute = $this->repository->findByName($name);
            
            if (!$attribute) {
                return null;
            }
            
            return $attribute->toGraphQL();
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch attribute: " . $e->getMessage());
        }
    }
}