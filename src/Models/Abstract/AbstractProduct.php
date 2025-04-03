<?php

namespace App\Models\Abstract;

abstract class AbstractProduct extends AbstractModel
{
    protected function getTableName(): string
    {
        return 'products';
    }
    
    abstract public function getSpecificAttributes(): array;
    
    public function getAttributes(): array
    {
        $attributes = $this->query(
            "SELECT a.id, a.name, a.type 
             FROM attributes a
             WHERE a.product_id = ?",
            [$this->getData('id')]
        );
        
        foreach ($attributes as &$attribute) {
            $attribute['items'] = $this->query(
                "SELECT item_id as id, display_value as displayValue, value 
                 FROM attribute_items 
                 WHERE attribute_id = ?",
                [$attribute['id']]
            );
        }
        
        return array_merge($attributes, $this->getSpecificAttributes());
    }
    
    public function getPrices(): array
    {
        return $this->query(
            "SELECT amount, currency_label as label, currency_symbol as symbol 
             FROM prices 
             WHERE product_id = ?",
            [$this->getData('id')]
        );
    }
    
    public function getGallery(): array
    {
        $gallery = $this->query(
            "SELECT image_url 
             FROM product_gallery 
             WHERE product_id = ?",
            [$this->getData('id')]
        );
        
        return array_column($gallery, 'image_url');
    }
}