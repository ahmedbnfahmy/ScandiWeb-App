<?php

namespace App\Models\Entity;

class Attribute
{
    
    public int $id;
    
    
    public string $product_id;
    
    
    public string $name;
    
    
    public string $type;
    
    
    public ?array $items = null;
    
    
    public static function fromArray(array $data): self
    {
        $attribute = new self();
        
        foreach ($data as $key => $value) {
            if (property_exists($attribute, $key)) {
                $attribute->$key = $value;
            }
        }
        
        return $attribute;
    }
    
    
    public function toArray(): array
    {
        $result = [
            'id' => $this->id,
            'product_id' => $this->product_id,
            'name' => $this->name,
            'type' => $this->type
        ];
        
        if ($this->items !== null) {
            $result['items'] = $this->items;
        }
        
        return $result;
    }
    
    
    public function toGraphQL(): array
    {
        $result = [
            'id' => (string)$this->id,
            'name' => $this->name,
            'type' => $this->type
        ];
        
        if ($this->items !== null) {
            $result['items'] = array_map(function($item) {
                return is_array($item) ? $item : $item->toGraphQL();
            }, $this->items);
        }
        
        return $result;
    }
} 