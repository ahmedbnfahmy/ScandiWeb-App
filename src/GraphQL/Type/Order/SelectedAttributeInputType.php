<?php

namespace App\GraphQL\Type\Order;

use GraphQL\Type\Definition\InputObjectType;
use GraphQL\Type\Definition\Type;

class SelectedAttributeInputType
{
    private static ?InputObjectType $type = null;

    public static function get(): InputObjectType
    {
        if (self::$type === null) {
            self::$type = new InputObjectType([
                'name' => 'SelectedAttributeInput',
                'fields' => [
                    'attributeName' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Name of the attribute (e.g., "Size", "Color")'
                    ],
                    'attributeItemId' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'ID of the selected attribute item (e.g., "Small", "Red")'
                    ],
                    'displayValue' => [
                        'type' => Type::string(),
                        'description' => 'Optional display value of the selected attribute item'
                    ],
                    'attribute_id' => [
                        'type' => Type::int(),
                        'description' => 'Database ID of the attribute (for internal use)'
                    ],
                    'attribute_items_id' => [
                        'type' => Type::int(),
                        'description' => 'Database ID of the attribute item (for internal use)'
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}