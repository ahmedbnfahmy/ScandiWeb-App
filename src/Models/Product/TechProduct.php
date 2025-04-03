<?php

namespace App\Models\Product;

use App\Models\Abstract\AbstractProduct;

class TechProduct extends AbstractProduct
{
    public function getType(): string
    {
        return 'tech';
    }
    
    public function validate(): bool
    {
        // Tech-specific validation
        return !empty($this->getData('name')) && 
               !empty($this->getData('brand')) &&
               !empty($this->getData('description'));
    }
    
    public function getSpecificAttributes(): array
    {
        // Tech products often have capacity/color
        $specificAttrs = [];
        
        // Add tech-specific attribute processing here
        
        return $specificAttrs;
    }
    
    public function getTechnicalSpecs(): array
    {
        // Tech-specific method
        return [
            'hasWarranty' => true,
            'warrantyPeriod' => '2 years'
        ];
    }
}