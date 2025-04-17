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
    
    
    public function productExists(string $productId): bool
    {
        $result = $this->query(
            "SELECT COUNT(*) as count FROM products WHERE id = ?",
            [$productId]
        );
        
        return !empty($result) && (int)$result[0]['count'] > 0;
    }
    
    
    public function findAll(): array
    {
        
        $products = $this->all();
        return $this->enrichProducts($products);
    }
    
    
    public function findById($id): ?array
    {
        $product = $this->find($id);
        if (!$product) {
            return null;
        }
        
        $enriched = $this->enrichProducts([$product]);
        return $enriched[0] ?? null;
    }
    
    
    public function findByCategory(string $category): array
    {
        $products = $this->findBy(['category' => $category]);
        return $this->enrichProducts($products);
    }
    
    
    public function save(array $data): string
    {
        
        if (isset($data['id'])) {
            $id = $data['id'];
            $dataCopy = $data;
            unset($dataCopy['id']);
            
            $this->update($dataCopy, ['id' => $id]);
            return $id;
        }
        
        
        return $this->create($data);
    }
    
    
    
    private function enrichProducts(array $products): array
    {
        if (empty($products)) {
            return [];
        }
        
        $productIds = array_column($products, 'id');
        $placeholders = implode(',', array_fill(0, count($productIds), '?'));
        
        
        $attributes = $this->getAttributesForProducts($productIds, $placeholders);
        
        
        $prices = $this->getPricesForProducts($productIds, $placeholders);
        
        
        $gallery = $this->getGalleryForProducts($productIds, $placeholders);
        
        
        foreach ($products as &$product) {
            $id = $product['id'];
            $product['attributes'] = $attributes[$id] ?? [];
            $product['prices'] = $prices[$id] ?? [];
            $product['gallery'] = $gallery[$id] ?? [];
            
            
            if (isset($product['in_stock'])) {
                $product['inStock'] = (bool)$product['in_stock'];
            }
        }
        
        return $products;
    }
    
    
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
        
        
        $itemsByAttr = [];
        foreach ($attributeItems as $item) {
            $attrId = $item['attribute_id'];
            if (!isset($itemsByAttr[$attrId])) {
                $itemsByAttr[$attrId] = [];
            }
            $itemsByAttr[$attrId][] = $item;
        }
        
        
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
    
    
    private function getPricesForProducts(array $productIds, string $placeholders): array
    {
        $priceRows = $this->query(
            "SELECT p.product_id, p.amount, p.currency_label as label, p.currency_symbol as symbol
             FROM prices p
             WHERE p.product_id IN ($placeholders)",
            $productIds
        );
        
        
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
    
    
    private function getGalleryForProducts(array $productIds, string $placeholders): array
    {
        $galleryRows = $this->query(
            "SELECT pg.product_id, pg.image_url
             FROM product_gallery pg
             WHERE pg.product_id IN ($placeholders)",
            $productIds
        );
        
        
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