<?php

namespace Bbot\Route\Storage;

class ArrayStorage implements RouterStorage
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
}
