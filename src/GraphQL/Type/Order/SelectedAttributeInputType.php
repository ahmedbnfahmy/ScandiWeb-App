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
                    'attributeSetId' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'ID of the attribute set'
                    ],
                    'attributeId' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'ID of the selected attribute value'
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}