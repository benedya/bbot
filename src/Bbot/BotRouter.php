<?php

namespace Bbot;

class BotRouter
{
    protected static $delimiter = '___';

    public static function generatePostBack($handler, $action, array $data = [])
    {
            return $handler . self::$delimiter . $action . ($data ? self::$delimiter . http_build_query($data) : '');
    }
}
