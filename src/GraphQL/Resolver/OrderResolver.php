<?php

namespace App\GraphQL\Resolver;

use App\Models\Repository\OrderRepository;
use App\Models\Repository\OrderItemRepository;
use App\Models\Repository\ProductRepository;
use App\Models\Repository\ProductAttributeRepository;
use InvalidArgumentException;

class OrderResolver
{
    private OrderRepository $orderRepository;
    private OrderItemRepository $orderItemRepository;
    private ProductRepository $productRepository;
    private ProductAttributeRepository $productAttributeRepository;
    
    public function __construct()
    {
        $this->orderRepository = new OrderRepository();
        $this->orderItemRepository = new OrderItemRepository();
        $this->productRepository = new ProductRepository();
        $this->productAttributeRepository = new ProductAttributeRepository();
    }
    
    /**
     * Create a new order
     * 
     * @param array $input The order input data
     * @return array The created order data
     */
    public function createOrder(array $input): array
    {
        try {
            // Validate input data
            $this->validateInput($input);
            
            // Create the order and return data directly
            return $this->orderRepository->createAndReturn($input);
        } catch (InvalidArgumentException $e) {
            throw $e;
        } catch (\Exception $e) {
            error_log('Error creating order: ' . $e->getMessage());
            throw new \Exception('Failed to create order: ' . $e->getMessage());
        }
    }
    
    /**
     * Validate input data
     * 
     * @param array $input The input data
     * @throws InvalidArgumentException If validation fails
     */
    private function validateInput(array $input): void
    {
        // Validate items
        if (!isset($input['items']) || empty($input['items'])) {
            throw new InvalidArgumentException('Order must contain at least one item');
        }
        
        // Validate each item
        foreach ($input['items'] as $index => $item) {
            $this->validateItem($item, $index);
        }
    }
    
    /**
     * Validate order item
     * 
     * @param array $item The order item
     * @param int $index The item index
     * @throws InvalidArgumentException If validation fails
     */
    private function validateItem(array $item, int $index): void
    {
        // Check required fields
        if (!isset($item['productId']) || empty($item['productId'])) {
            throw new InvalidArgumentException("Item at index $index is missing productId");
        }
        
        if (!isset($item['quantity']) || !is_numeric($item['quantity']) || $item['quantity'] <= 0) {
            throw new InvalidArgumentException("Item at index $index has invalid quantity");
        }
        
                // Verify product exists
        if (!$this->productRepository->productExists($item['productId'])) {
            throw new InvalidArgumentException("Product with ID {$item['productId']} does not exist");
        }
        
        // Validate selected attributes if present
        if (isset($item['selectedAttributes']) && !empty($item['selectedAttributes'])) {
            foreach ($item['selectedAttributes'] as $attrIndex => $attribute) {
                if (!isset($attribute['attributeName']) || empty($attribute['attributeName'])) {
                    throw new InvalidArgumentException("Attribute at index $attrIndex for item $index is missing attributeName");
                }
                
                if (!isset($attribute['attributeItemId']) || empty($attribute['attributeItemId'])) {
                    throw new InvalidArgumentException("Attribute at index $attrIndex for item $index is missing attributeItemId");
                }
                
                // Validate that the attribute is valid for this product
                if (!$this->productAttributeRepository->attributeExistsForProduct(
                    $item['productId'],
                    $attribute['attributeName'],
                    $attribute['attributeItemId']
                )) {
                    throw new InvalidArgumentException(
                        "Attribute item {$attribute['attributeItemId']} for attribute {$attribute['attributeName']} " .
                        "is not valid for product {$item['productId']}"
                    );
                }
            }
        }
    }
    
    /**
     * Get order items for displaying existing orders
     * 
     * @param string $orderId The order ID
     * @return array The order items
     */
    public function getOrderItems(string $orderId): array
    {
        return $this->orderItemRepository->getOrderItems($orderId);
    }
}