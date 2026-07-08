<?php

namespace App\Serializer\Attribute;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS)]
class ApiAuthGroups
{
    public function __construct(public $groups)
    {
    }
}