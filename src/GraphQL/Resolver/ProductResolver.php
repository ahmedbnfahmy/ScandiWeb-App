<?php

namespace App\GraphQL\Resolver;

use App\Models\Product;

class ProductResolver
{
    private Product $model;

    public function __construct()
    {
        $this->model = new Product();
    }

    /**
     * Get all products
     */
    public function getProducts(): array
{
    try {
        $products = $this->model->all();
        // Add attributes to each product
        foreach ($products as &$product) {
            $product['attributes'] = $this->model->getAttributes($product['id']);
            
            // Handle field name conversion
            if (isset($product['in_stock'])) {
                $product['inStock'] = (bool)$product['in_stock'];
            }
        }
        
        return $products;
    } catch (\Exception $e) {
        throw new \Exception("Failed to fetch products: " . $e->getMessage());
    }
}

    /**
     * Get single product
     */
    public function getProduct(int $id): ?array
    {
        try {
            return $this->model->find($id);
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
            $productId = $this->model->create([
                'name' => $data['name'],
                'description' => $data['description'] ?? null,
                'price' => $data['price'],
                'category' => $data['category'],
                'brand' => $data['brand'],
                'in_stock' => $data['inStock'] ?? true,
                'gallery' => json_encode($data['gallery'] ?? [])
            ]);

            return $this->model->find($productId);
        } catch (\Exception $e) {
            throw new \Exception("Failed to create product: " . $e->getMessage());
        }
    }

    /**
     * Update product
     */
    public function updateProduct(int $id, array $data): ?array
    {
        try {
            $updateData = array_filter([
                'name' => $data['name'] ?? null,
                'description' => $data['description'] ?? null,
                'price' => $data['price'] ?? null,
                'category' => $data['category'] ?? null,
                'brand' => $data['brand'] ?? null,
                'in_stock' => $data['inStock'] ?? null,
                'gallery' => isset($data['gallery']) ? json_encode($data['gallery']) : null
            ], fn($value) => $value !== null);

            $success = $this->model->update($updateData, ['id' => $id]);
            return $success ? $this->model->find($id) : null;
        } catch (\Exception $e) {
            throw new \Exception("Failed to update product: " . $e->getMessage());
        }
    }

    /**
     * Delete product
     */
    public function deleteProduct(int $id): bool
    {
        try {
            return $this->model->delete(['id' => $id]);
        } catch (\Exception $e) {
            throw new \Exception("Failed to delete product: " . $e->getMessage());
        }
    }
}