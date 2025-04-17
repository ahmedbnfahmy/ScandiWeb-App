<?php

namespace App\Models\Entity;

class AttributeItem
{
    
    public int $id;
    
    
    public int $attribute_id;
    
    
    public string $display_value;
    
    
    public string $value;
    
    
    public string $item_id;
    
    
    public ?string $__typename;
    
    
    public static function fromArray(array $data): self
    {
        $attributeItem = new self();
        
        foreach ($data as $key => $value) {
            if (property_exists($attributeItem, $key)) {
                $attributeItem->$key = $value;
            }
        }
        
        return $attributeItem;
    }
    
    
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'attribute_id' => $this->attribute_id,
            'display_value' => $this->display_value,
            'value' => $this->value,
            'item_id' => $this->item_id,
            '__typename' => $this->__typename
        ];
    }
    
    
    public function toGraphQL(): array
    {
        return [
            'id' => $this->item_id,
            'displayValue' => $this->display_value,
            'value' => $this->value,
            '__typename' => $this->__typename
        ];
    }
} 