<?php

namespace App\Models\Product;

use App\Models\Abstract\AbstractProduct;

class ClothingProduct extends AbstractProduct
{
    public function getType(): string
    {
        return 'clothes';
    }
    
    public function validate(): bool
    {
        // Clothing-specific validation
        return !empty($this->getData('name')) && 
               !empty($this->getData('brand')) &&
               $this->hasSizeAttribute();
    }
    
    public function getSpecificAttributes(): array
    {
        // Clothing-specific attributes logic
        return [];
    }
    
    private function hasSizeAttribute(): bool
    {
        $sizeAttributes = $this->query(
            "SELECT COUNT(*) as count FROM attributes 
             WHERE product_id = ? AND name = 'Size'",
            [$this->getData('id')]
        );
        
        return $sizeAttributes[0]['count'] > 0;
    }
}