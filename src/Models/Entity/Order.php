<?php

namespace App\Models\Entity;

class Order
{
    /**
     * @var string
     */
    public string $id;
    
    /**
     * @var string
     */
    public string $customer_name;
    
    /**
     * @var string
     */
    public string $customer_email;
    
    /**
     * @var string|null
     */
    public ?string $address;
    
    /**
     * @var float
     */
    public float $total_amount;
    
    /**
     * @var string
     */
    public string $status;
    
    /**
     * @var string
     */
    public string $created_at;
    
    /**
     * @var string|null
     */
    public ?string $updated_at;
    
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->address = null;
        $this->updated_at = null;
    }
    
    /**
     * Create an Order instance from an array of data
     * 
     * @param array $data The order data
     * @return self
     */
    public static function fromArray(array $data): self
    {
        $order = new self();
        
        foreach ($data as $key => $value) {
            if (property_exists($order, $key)) {
                $order->$key = $value;
            }
        }
        
        return $order;
    }
    
    /**
     * Convert the Order to an array
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'customer_name' => $this->customer_name,
            'customer_email' => $this->customer_email,
            'address' => $this->address,
            'total_amount' => $this->total_amount,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
} 