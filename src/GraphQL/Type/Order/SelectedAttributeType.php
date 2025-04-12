<?php

namespace App\GraphQL\Type\Order;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class SelectedAttributeType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'SelectedAttribute',
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