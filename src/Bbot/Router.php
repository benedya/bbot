<?php

namespace Bbot;

class BotRouter
{
    public static function toPostback(string $controller, string $action, array $data = [])
    {
        return $controller . $action .($data ? '?.' . http_build_query($data) : '');
    }

    public static function fromPostback(Request\Request $request)
    {
        $result = '';

        if ($request->getPostback()) {

        }

        return $result;
    }
}
