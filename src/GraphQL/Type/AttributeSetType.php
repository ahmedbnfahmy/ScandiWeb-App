<?php

namespace App\GraphQL\Type;

use App\GraphQL\Resolver\AttributeResolver;
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class AttributeSetType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'AttributeSet',
                'fields' => [
                    'id' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'name' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'type' => [
                        'type' => Type::nonNull(Type::string())
                    ],
                    'items' => [
                        'type' => Type::listOf(AttributeType::get()),
                        'description' => 'List of attributes in this set',
                        'resolve' => function ($attributeSet) {
                            $resolver = new AttributeResolver();
                            return $resolver->getAttributeItems($attributeSet['id']);
                        }
                    ]
                ]
            ]);
        }
        
        return self::$type;
    }
}