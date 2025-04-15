<?php

namespace App\Models\Entity;

class Attribute
{
    /**
     * @var int
     */
    public int $id;
    
    /**
     * @var string
     */
    public string $product_id;
    
    /**
     * @var string
     */
    public string $name;
    
    /**
     * @var string
     */
    public string $type;
    
    /**
     * @var array|null
     */
    public ?array $items = null;
    
    /**
     * Create an Attribute instance from an array of data
     * 
     * @param array $data The attribute data
     * @return self
     */
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
    
    /**
     * Convert the Attribute to an array
     * 
     * @return array
     */
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
    
    /**
     * Convert to GraphQL compatible format
     * 
     * @return array
     */
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