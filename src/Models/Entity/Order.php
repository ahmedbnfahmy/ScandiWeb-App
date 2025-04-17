<?php

namespace App\Models\Entity;

class Order
{
    
    public string $id;
    
    
    public string $customer_name;
    
    
    public string $customer_email;
    
    
    public ?string $address;
    
    
    public float $total_amount;
    
    
    public string $status;
    
    
    public string $created_at;
    
    
    public ?string $updated_at;
    
    
    public function __construct()
    {
        $this->address = null;
        $this->updated_at = null;
    }
    
    
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