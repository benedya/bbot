<?php

namespace Bbot;

class Router
{
    protected static $delimiter = '::';

    public static function toPostback(string $controller, string $action, array $data = [])
    {
        return $controller.self::$delimiter.$action.($data ? '?'.http_build_query($data) : '');
    }

    public static function fromPostback(Request\Request $request)
    {
        $result = [];

        if ($postback = $request->getPostback()) {
            $postback = preg_replace("/\?.*/", '', $postback);

            return explode(self::$delimiter, $postback);
        }

        return $result;
    }
}
