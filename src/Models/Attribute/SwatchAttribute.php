<?php

namespace App\Models\Attribute;

class SwatchAttribute extends AbstractAttribute
{
    const TYPE = 'swatch';
    
    public function renderInput(): array
    {
        return [
            'type' => 'color-swatch',
            'options' => $this->getItems(),
            'layout' => 'grid'
        ];
    }
    
    public function validateValue($value): bool
    {
        $items = $this->getItems();
        return in_array($value, array_column($items, 'value'));
    }
    
    public function validate(): bool
    {
        $items = $this->getItems();
        foreach ($items as $item) {
            // Color should be a valid hex code
            if (!preg_match('/^#[a-f0-9]{6}$/i', $item['value'])) {
                return false;
            }
        }
        return !empty($items);
    }
}