<?php

namespace Bbot\Storage;

class ArrayStorage implements Storage
{
    protected $storage = [];

    public function set(string $key, string $value)
    {
        $this->storage[$key] = $value;
    }

    public function get(string $key)
    {
        return $this->storage[$key] ?? null;
    }

    public function remove(string $key)
    {
        if (isset($this->storage[$key])) {
            unset($this->storage[$key]);
        }
    }
}
