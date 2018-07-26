<?php

namespace Bbot\Storage;

interface Storage
{
    public function set(string $key, string $value);

    public function get(string $key);

    public function remove(string $key);
}
