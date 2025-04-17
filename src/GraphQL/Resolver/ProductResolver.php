<?php

namespace App\GraphQL\Resolver;

use App\Factories\ProductFactory;
use App\Models\Abstract\AbstractProduct;
use App\Models\Product;
use App\Models\Repository\ProductRepository;

class ProductResolver
{
    private ProductRepository $repository;

    public function __construct()
    {
        $this->repository = new ProductRepository();
    }

    
    public function getProducts(): array
    {
        try {
            
            $productsData = $this->repository->findAll();
            $enrichedProducts = [];
            
            
            foreach ($productsData as $productData) {
                $product = ProductFactory::create($productData);
                
                
                $enrichedProduct = $productData;
                $enrichedProduct['attributes'] = $product->getAttributes();
                $enrichedProduct['prices'] = $product->getPrices();
                $enrichedProduct['gallery'] = $product->getGallery();
                
                
                if (isset($enrichedProduct['in_stock'])) {
                    $enrichedProduct['inStock'] = (bool)$enrichedProduct['in_stock'];
                }
                
                $enrichedProducts[] = $enrichedProduct;
            }
            
            return $enrichedProducts;
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch products: " . $e->getMessage());
        }
    }

    
    public function getProduct(string $id): ?array
    {
        try {
            
            $productData = $this->repository->findById($id);
            
            if (!$productData) {
                return null;
            }
            
            
            $product = ProductFactory::create($productData);
            
            
            $productData['attributes'] = $product->getAttributes();
            $productData['prices'] = $product->getPrices();
            $productData['gallery'] = $product->getGallery();
            
            
            if (isset($productData['in_stock'])) {
                $productData['inStock'] = (bool)$productData['in_stock'];
            }
            
            return $productData;
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch product: " . $e->getMessage());
        }
    }

    
    public function createProduct(array $data): array
    {
        try {
            
            $productData = [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'category' => $data['category'],
                'brand' => $data['brand'],
                'in_stock' => $data['inStock'] ?? true,
                'gallery' => json_encode($data['gallery'] ?? [])
            ];
            
            
            $product = ProductFactory::create($productData);
            
            if (!$product->validate()) {
                throw new \Exception("Product validation failed");
            }
            
            
            $productId = $this->repository->save($productData);
            
            
            return $this->getProduct($productId);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create product: " . $e->getMessage());
        }
    }

    
    public function updateProduct(string $id, array $data): ?array
    {
        try {
            
            $existingData = $this->repository->findById($id);
            
            if (!$existingData) {
                return null;
            }
            
            
            $updateData = array_filter([
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'price' => $data['price'] ?? null,
                'category' => $data['category'] ?? null,
                'brand' => $data['brand'] ?? null,
                'in_stock' => $data['inStock'] ?? null,
                'gallery' => isset($data['gallery']) ? json_encode($data['gallery']) : null
            ], fn($value) => $value !== null);
            
            
            $mergedData = array_merge($existingData, $updateData);
            
            
            $product = ProductFactory::create($mergedData);
            
            if (!$product->validate()) {
                throw new \Exception("Product validation failed");
            }
            
            
            $success = $this->repository->update($id, $updateData);
            
            return $success ? $this->getProduct($id) : null;
        } catch (\Exception $e) {
            throw new \Exception("Failed to update product: " . $e->getMessage());
        }
    }

    
    public function deleteProduct(string $id): bool
    {
        try {
            return $this->repository->delete($id);
        } catch (\Exception $e) {
            throw new \Exception("Failed to delete product: " . $e->getMessage());
        }
    }
}