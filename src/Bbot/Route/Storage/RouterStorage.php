<?php

namespace Bbot\Route\Storage;

interface RouterStorage
{
    public function set(string $key, string $value);

    public function get(string $key);
}
