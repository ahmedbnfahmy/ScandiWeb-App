<?php

namespace App\Models\Abstract;

use App\Database\CoreModel;

abstract class AbstractModel extends CoreModel
{
    abstract public function getType(): string;
    abstract public function validate(): bool;
}