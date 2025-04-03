<?php

namespace App\Models\Attribute;

class TextAttribute extends AbstractAttribute
{
    const TYPE = 'text';
    
    public function renderInput(): array
    {
        return [
            'type' => 'dropdown',
            'options' => $this->getItems(),
            'placeholder' => "Select {$this->getData('name')}"
        ];
    }
    
    public function validateValue($value): bool
    {
        $items = $this->getItems();
        return in_array($value, array_column($items, 'value'));
    }
    
    public function validate(): bool
    {
        return !empty($this->getItems());
    }
}