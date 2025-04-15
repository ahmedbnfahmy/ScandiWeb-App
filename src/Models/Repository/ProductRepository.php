<?php

namespace App\Models\Repository;

use App\Database\CoreModel;
use App\Factories\ProductFactory;

class ProductRepository extends CoreModel
{
    protected function getTableName(): string
    {
        return 'products';
    }
    
    /**
     * Check if a product exists
     * 
     * @param string $productId The product ID
     * @return bool Whether the product exists
     */
    public function productExists(string $productId): bool
    {
        $result = $this->query(
            "SELECT COUNT(*) as count FROM products WHERE id = ?",
            [$productId]
        );
        
        return !empty($result) && (int)$result[0]['count'] > 0;
    }
    
    /**
     * Find all products with attributes
     */
    public function findAll(): array
    {
        // Get all products
        $products = $this->all();
        return $this->enrichProducts($products);
    }
    
    /**
     * Find product by ID with attributes
     */
    public function findById($id): ?array
    {
        $product = $this->find($id);
        if (!$product) {
            return null;
        }
        
        $enriched = $this->enrichProducts([$product]);
        return $enriched[0] ?? null;
    }
    
    /**
     * Find products by category
     */
    public function findByCategory(string $category): array
    {
        $products = $this->findBy(['category' => $category]);
        return $this->enrichProducts($products);
    }
    
    /**
     * Save a product
     */
    public function save(array $data): string
    {
        // If has ID, update existing record
        if (isset($data['id'])) {
            $id = $data['id'];
            $dataCopy = $data;
            unset($dataCopy['id']);
            
            $this->update($dataCopy, ['id' => $id]);
            return $id;
        }
        
        // Otherwise create new record
        return $this->create($data);
    }
    
    
    /**
     * Enrich products with attributes and other related data
     */
    private function enrichProducts(array $products): array
    {
        if (empty($products)) {
            return [];
        }
        
        $productIds = array_column($products, 'id');
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        
        // Get attributes
        $attributes = $this->getAttributesForProducts($productIds, $placeholders);
        
        // Get prices
        $prices = $this->getPricesForProducts($productIds, $placeholders);
        
        // Get gallery
        $gallery = $this->getGalleryForProducts($productIds, $placeholders);
        
        // Enrich products
        foreach ($products as &$product) {
            $id = $product['id'];
            $product['attributes'] = $attributes[$id] ?? [];
            $product['prices'] = $prices[$id] ?? [];
            $product['gallery'] = $gallery[$id] ?? [];
            
            // Handle field name conversion
            if (isset($product['in_stock'])) {
                $product['inStock'] = (bool)$product['in_stock'];
            }
        }
        
        return $products;
    }
    
    /**
     * Get attributes for multiple products
     */
    private function getAttributesForProducts(array $productIds, string $placeholders): array
    {
        $attributeRows = $this->query(
            "SELECT a.product_id, a.id as attribute_id, a.name, a.type
             FROM attributes a
             WHERE a.product_id IN ($placeholders)",
            $productIds
        );
        
        if (empty($attributeRows)) {
            return [];
        }
        
        $attributeIds = array_column($attributeRows, 'attribute_id');
        $attrPlaceholders = implode(',', array_fill(0, count($attributeIds), '?'));
        
        $attributeItems = $this->query(
            "SELECT ai.attribute_id, ai.item_id as id, ai.display_value as displayValue, ai.value
             FROM attribute_items ai
             WHERE ai.attribute_id IN ($attrPlaceholders)",
            $attributeIds
        );
        
        // Group items by attribute
        $itemsByAttr = [];
        foreach ($attributeItems as $item) {
            $attrId = $item['attribute_id'];
            if (!isset($itemsByAttr[$attrId])) {
                $itemsByAttr[$attrId] = [];
            }
            $itemsByAttr[$attrId][] = $item;
        }
        
        // Group attributes by product
        $attributesByProduct = [];
        foreach ($attributeRows as $attr) {
            $productId = $attr['product_id'];
            if (!isset($attributesByProduct[$productId])) {
                $attributesByProduct[$productId] = [];
            }
            
            $attributesByProduct[$productId][] = [
                'id' => $attr['attribute_id'],
                'name' => $attr['name'],
                'type' => $attr['type'],
                'items' => $itemsByAttr[$attr['attribute_id']] ?? []
            ];
        }
        
        return $attributesByProduct;
    }
    
    /**
     * Get prices for multiple products
     */
    private function getPricesForProducts(array $productIds, string $placeholders): array
    {
        $priceRows = $this->query(
            "SELECT p.product_id, p.amount, p.currency_label as label, p.currency_symbol as symbol
             FROM prices p
             WHERE p.product_id IN ($placeholders)",
            $productIds
        );
        
        // Group prices by product
        $pricesByProduct = [];
        foreach ($priceRows as $price) {
            $productId = $price['product_id'];
            if (!isset($pricesByProduct[$productId])) {
                $pricesByProduct[$productId] = [];
            }
            
            $pricesByProduct[$productId][] = [
                'amount' => (float)$price['amount'],
                'currency' => [
                    'label' => $price['label'],
                    'symbol' => $price['symbol']
                ]
            ];
        }
        
        return $pricesByProduct;
    }
    
    /**
     * Get gallery for multiple products
     */
    private function getGalleryForProducts(array $productIds, string $placeholders): array
    {
        $galleryRows = $this->query(
            "SELECT pg.product_id, pg.image_url
             FROM product_gallery pg
             WHERE pg.product_id IN ($placeholders)",
            $productIds
        );
        
        // Group images by product
        $galleryByProduct = [];
        foreach ($galleryRows as $image) {
            $productId = $image['product_id'];
            if (!isset($galleryByProduct[$productId])) {
                $galleryByProduct[$productId] = [];
            }
            
            $galleryByProduct[$productId][] = $image['image_url'];
        }
        
        return $galleryByProduct;
    }
}