<?php

namespace App\Models;

class Model
{
    public function __construct()
    {
    }

    public function toArray(): array
    {
        $excludedProperties = array_merge(['hidden'], $this->hidden ?? []);

        return array_diff_key(get_object_vars($this), array_flip($excludedProperties));
    }
}
