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
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}