<?php

namespace App\Models\Attribute;

use App\Models\Abstract\AbstractModel;

abstract class AbstractAttribute extends AbstractModel
{
    abstract public function renderInput(): array;
    abstract public function validateValue($value): bool;
    
    public function getType(): string
    {
        return static::TYPE;
    }
    
    
    public function getItems(): array
    {
        return $this->query(
            "SELECT item_id as id, display_value as displayValue, value 
             FROM attribute_items 
             WHERE attribute_id = ?",
            [$this->getData('id')]
        );
    }
}