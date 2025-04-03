<?php

namespace App\GraphQL\Resolver;

use App\Models\Repository\AttributeRepository;
use App\Factories\AttributeFactory;

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
            $rawData = $this->repository->findById($id);
            
            if (!$rawData) {
                return null;
            }
            
            // Create the right type of attribute object
            $attribute = AttributeFactory::create($rawData);
            
            // Add attribute-specific enrichments
            $data = $rawData;
            $data['items'] = $attribute->getItems();
            
            // Add rendering information based on type
            $data['input'] = $attribute->renderInput();
            
            return $data;
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch attribute: " . $e->getMessage());
        }
    }
    
    /**
     * Get attribute items for an attribute set
     * 
     * @param string $attributeSetId ID of the attribute set
     * @return array List of attribute items
     */
    public function getAttributeItems(string $attributeSetId): array
    {
        try {
            return $this->repository->findByAttributeSetId($attributeSetId);
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch attribute items: " . $e->getMessage());
        }
    }
}