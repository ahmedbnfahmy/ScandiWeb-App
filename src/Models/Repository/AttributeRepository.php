<?php

namespace App\Models\Repository;

use App\Database\CoreModel;
use App\Models\Entity\Attribute;
use App\Models\Entity\AttributeItem;

class AttributeRepository extends CoreModel
{
    protected function getTableName(): string
    {
        return 'attributes';
    }
    
    
    public function findById(int $id): ?Attribute
    {
        $data = parent::find($id);
        
        if (!$data) {
            return null;
        }
        
        return Attribute::fromArray($data);
    }
    
    
    public function findByName(string $name): ?Attribute
    {
        $result = $this->findBy(['name' => $name]);
        
        if (empty($result)) {
            return null;
        }
        
        return Attribute::fromArray($result[0]);
    }
    
    
    public function findByProductId(string $productId): array
    {
        $result = $this->findBy(['product_id' => $productId]);
        
        if (empty($result)) {
            return [];
        }
        
        $attributes = [];
        foreach ($result as $data) {
            $attributes[] = Attribute::fromArray($data);
        }
        
        return $attributes;
    }
    
    
    public function findByProductIdWithItems(string $productId): array
    {
        $attributes = $this->findByProductId($productId);
        
        if (empty($attributes)) {
            return [];
        }
        
        $attributeItemRepo = new AttributeItemRepository();
        
        foreach ($attributes as $attribute) {
            $attribute->items = $attributeItemRepo->findByAttributeId($attribute->id);
        }
        
        return $attributes;
    }
    
    
    public function attributeExists(string $name): bool
    {
        $result = $this->query(
            "SELECT COUNT(*) as count FROM {$this->getTableName()} WHERE name = ?",
            [$name]
        );
        
        return !empty($result) && (int)$result[0]['count'] > 0;
    }
}