<?php

namespace Bbot\Route\Storage;

class SessionStorage implements RouterStorage
{
    public function set(string $key, string $value)
    {
        $_SESSION[$key] = $value;
    }

    public function get(string $key)
    {
        return $_SESSION[$key];
    }
}
