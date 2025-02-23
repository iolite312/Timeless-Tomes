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

        $data = array_diff_key(get_object_vars($this), array_flip($excludedProperties));

        // Convert nested models to array
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $data[$key] = array_map(fn ($item) => $item instanceof Model ? $item->toArray() : $item, $value);
            } elseif ($value instanceof Model) {
                $data[$key] = $value->toArray();
            }
        }

        return $data;
    }
}
