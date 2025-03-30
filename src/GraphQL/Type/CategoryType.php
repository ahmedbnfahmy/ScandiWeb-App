<?php

namespace App\GraphQL\Type;

use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;

class CategoryType
{
    private static ?ObjectType $type = null;

    public static function get(): ObjectType
    {
        if (self::$type === null) {
            self::$type = new ObjectType([
                'name' => 'Category',
                'fields' => [
                    'name' => [
                        'type' => Type::nonNull(Type::string()),
                        'description' => 'Category name'
                    ]
                ]
            ]);
        }
        return self::$type;
    }
}