<?php

namespace App\GraphQL\Type;

use App\GraphQL\Resolver\AttributeItemResolver;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'Attribute',
                'fields' => [
                    'id' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Attribute ID'
                    ],
                    'name' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Name of the attribute'
                    ],
                    'type' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Type of the attribute (text, swatch, etc.)'
                    ],
                    'items' => [
                        'type' => Type::listOf(AttributeItemType::get()),
                        'description' => 'List of items/values for this attribute',
                        'resolve' => function ($attribute) {
                            $resolver = new AttributeItemResolver();
                            return $resolver->findByAttributeId($attribute['id']);
                        }
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}