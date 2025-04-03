<?php

namespace App\GraphQL\Resolver;

use App\Models\Repository\OrderRepository;
use App\Models\Repository\OrderItemRepository;
use App\Models\Repository\ProductRepository;

class OrderResolver
{
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;
    private ProductRepository $productRepository;
    
    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->orderItemRepository = new OrderItemRepository();
        $this->productRepository = new ProductRepository();
    }
    
    /**
     * Get order by ID
     */
    public function getOrder(string $id): ?array
    {
        try {
            return $this->orderRepository->findById($id);
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch order: " . $e->getMessage());
        }
    }
    
    /**
     * Get all orders
     */
    public function getOrders(): array
    {
        try {
            return $this->orderRepository->findAll();
        } catch (\Exception $e) {
            throw new \Exception("Failed to fetch orders: " . $e->getMessage());
        }
    }
    
    /**
     * Create a new order
     */
    public function createOrder(array $input): array
    {
        try {
            // Start transaction
            $this->orderRepository->beginTransaction();
            
            // Calculate total amount
            $totalAmount = $this->calculateTotalAmount($input['items']);
            
            // Create order
            $orderData = [
                'customer_name' => $input['customerName'],
                'customer_email' => $input['customerEmail'],
                'address' => $input['address'] ?? '',
                'total_amount' => $totalAmount,
                'status' => 'pending',
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            $orderId = $this->orderRepository->create($orderData);
            
            // Create order items
            foreach ($input['items'] as $item) {
                // Get product information
                $product = $this->productRepository->findById($item['productId']);
                if (!$product) {
                    throw new \Exception("Product not found: {$item['productId']}");
                }
                
                // Check stock
                if (!$product['inStock']) {
                    throw new \Exception("Product not in stock: {$product['name']}");
                }
                
                // Get product price
                $prices = $this->productRepository->getPricesForProduct($item['productId']);
                $price = $prices[0]['amount'] ?? 0;
                
                // Format selected attributes
                $selectedAttributes = [];
                if (isset($item['selectedAttributes'])) {
                    foreach ($item['selectedAttributes'] as $attr) {
                        $selectedAttributes[$attr['attributeId']] = $attr['valueId'];
                    }
                }
                
                // Create order item
                $orderItemData = [
                    'order_id' => $orderId,
                    'product_id' => $item['productId'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $price,
                    'selected_attributes' => json_encode($selectedAttributes)
                ];
                
                $this->orderItemRepository->create($orderItemData);
            }
            
            // Commit transaction
            $this->orderRepository->commit();
            
            // Return the created order
            return $this->getOrder($orderId);
        } catch (\Exception $e) {
            // Rollback transaction on error
            $this->orderRepository->rollback();
            throw new \Exception("Failed to create order: " . $e->getMessage());
        }
    }
    
    /**
     * Calculate total order amount
     */
    private function calculateTotalAmount(array $items): float
    {
        $total = 0;
        
        foreach ($items as $item) {
            // Get product price
            $prices = $this->productRepository->getPricesForProduct($item['productId']);
            $price = $prices[0]['amount'] ?? 0;
            
            $total += $price * $item['quantity'];
        }
        
        return $total;
    }
}