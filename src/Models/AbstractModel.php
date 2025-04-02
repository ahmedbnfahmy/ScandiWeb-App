<?php
namespace App\Models;

abstract class AbstractModel extends \App\Database\CoreModel
{
    abstract public function validate(): bool;
    abstract public function getType(): string;
}