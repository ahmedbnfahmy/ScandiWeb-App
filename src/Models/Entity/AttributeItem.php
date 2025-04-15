<?php

namespace App\Models\Entity;

class AttributeItem
{
    /**
     * @var int
     */
    public int $id;
    
    /**
     * @var int
     */
    public int $attribute_id;
    
    /**
     * @var string
     */
    public string $display_value;
    
    /**
     * @var string
     */
    public string $value;
    
    /**
     * @var string
     */
    public string $item_id;
    
    /**
     * @var string|null
     */
    public ?string $__typename;
    
    /**
     * Create an AttributeItem instance from an array of data
     * 
     * @param array $data The attribute item data
     * @return self
     */
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
    
    /**
     * Convert the AttributeItem to an array
     * 
     * @return array
     */
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
    
    /**
     * Convert to GraphQL compatible format
     * 
     * @return array
     */
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