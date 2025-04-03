<?php

namespace App\GraphQL;

use App\GraphQL\Type\QueryType;
use App\GraphQL\Type\MutationType;
use GraphQL\Type\Schema as GraphQLSchema;

class Schema
{
    private static ?GraphQLSchema $schema = null;

    public static function get(): GraphQLSchema
    {
        if (self::$schema === null) {
            self::$schema = new GraphQLSchema([
                'query' => QueryType::get(),
                'mutation' => MutationType::get()
            ]);
        }
        
        return self::$schema;
    }
}