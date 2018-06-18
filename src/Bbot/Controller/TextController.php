<?php

namespace Bbot\Controller;

use Bbot\Bridge\Bot;
use Bbot\Request\Request;
use Psr\Container\ContainerInterface;

class TextController
{
    public function index(Request $request, Bot $bot, ContainerInterface $container)
    {
        $bot->sendText('Hey' . $request->getData()['message']['text']);
    }
}
