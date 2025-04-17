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
        
        return !empty($this->getData('name')) && 
               !empty($this->getData('brand')) &&
               !empty($this->getData('description'));
    }
    
    public function getSpecificAttributes(): array
    {
        
        $specificAttrs = [];
        
        
        
        return $specificAttrs;
    }
    
    public function getTechnicalSpecs(): array
    {
        
        return [
            'hasWarranty' => true,
            'warrantyPeriod' => '2 years'
        ];
    }
}