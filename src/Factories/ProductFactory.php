<?php

namespace App\Factories;

use App\Models\Abstract\AbstractProduct;
use App\Models\Product\ClothingProduct;
use App\Models\Product\TechProduct;

class ProductFactory
{
    public static function create(array $data): AbstractProduct
    {
        $category = $data['category'] ?? '';
        
        $product = match ($category) {
            'clothes' => new ClothingProduct(),
            'tech' => new TechProduct(),
            default => new TechProduct() // Default case
        };
        
        $product->setData($data);
        return $product;
    }
}