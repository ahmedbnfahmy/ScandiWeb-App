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
    
    
    public function getAttributesForProduct(string $productId): array
    {
        try {
            $attributes = $this->repository->findByProductIdWithItems($productId);
            
            
            return array_map(function($attribute) {
                return $attribute->toGraphQL();
            }, $attributes);
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch attributes: " . $e->getMessage());
        }
    }
    
    
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