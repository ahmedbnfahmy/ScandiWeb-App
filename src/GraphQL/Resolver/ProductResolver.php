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

    /**
     * Get all products
     */
    public function getProducts(): array
    {
        try {
            // Get raw product data from repository
            $productsData = $this->repository->findAll();
            $enrichedProducts = [];
            
            // Create the appropriate product type instance for each product
            foreach ($productsData as $productData) {
                $product = ProductFactory::create($productData);
                
                // Enrich the product data with type-specific attributes
                $enrichedProduct = $productData;
                $enrichedProduct['attributes'] = $product->getAttributes();
                $enrichedProduct['prices'] = $product->getPrices();
                $enrichedProduct['gallery'] = $product->getGallery();
                
                // Handle field name conversion
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

    /**
     * Get single product
     */
    public function getProduct(string $id): ?array
    {
        try {
            // Get raw product data
            $productData = $this->repository->findById($id);
            
            if (!$productData) {
                return null;
            }
            
            // Create the appropriate product type instance
            $product = ProductFactory::create($productData);
            
            // Enrich with type-specific data
            $productData['attributes'] = $product->getAttributes();
            $productData['prices'] = $product->getPrices();
            $productData['gallery'] = $product->getGallery();
            
            // Handle field name conversion
            if (isset($productData['in_stock'])) {
                $productData['inStock'] = (bool)$productData['in_stock'];
            }
            
            return $productData;
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch product: " . $e->getMessage());
        }
    }

    /**
     * Create product
     */
    public function createProduct(array $data): array
    {
        try {
            // Convert GraphQL input to database format
            $productData = [
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'category' => $data['category'],
                'brand' => $data['brand'],
                'in_stock' => $data['inStock'] ?? true,
                'gallery' => json_encode($data['gallery'] ?? [])
            ];
            
            // Create product instance of appropriate type to validate
            $product = ProductFactory::create($productData);
            
            if (!$product->validate()) {
                throw new \Exception("Product validation failed");
            }
            
            // Save the product
            $productId = $this->repository->save($productData);
            
            // Return the created product
            return $this->getProduct($productId);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create product: " . $e->getMessage());
        }
    }

    /**
     * Update product
     */
    public function updateProduct(string $id, array $data): ?array
    {
        try {
            // Get existing product data
            $existingData = $this->repository->findById($id);
            
            if (!$existingData) {
                return null;
            }
            
            // Prepare update data
            $updateData = array_filter([
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'price' => $data['price'] ?? null,
                'category' => $data['category'] ?? null,
                'brand' => $data['brand'] ?? null,
                'in_stock' => $data['inStock'] ?? null,
                'gallery' => isset($data['gallery']) ? json_encode($data['gallery']) : null
            ], fn($value) => $value !== null);
            
            // Merge with existing data for validation
            $mergedData = array_merge($existingData, $updateData);
            
            // Create product instance of appropriate type to validate
            $product = ProductFactory::create($mergedData);
            
            if (!$product->validate()) {
                throw new \Exception("Product validation failed");
            }
            
            // Update the product
            $success = $this->repository->update($id, $updateData);
            
            return $success ? $this->getProduct($id) : null;
        } catch (\Exception $e) {
            throw new \Exception("Failed to update product: " . $e->getMessage());
        }
    }

    /**
     * Delete product
     */
    public function deleteProduct(string $id): bool
    {
        try {
            return $this->repository->delete($id);
        } catch (\Exception $e) {
            throw new \Exception("Failed to delete product: " . $e->getMessage());
        }
    }
}