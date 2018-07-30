<?php

namespace Bbot\Controller;

use Bbot\Bridge\Bot;
use Bbot\Request\Request;

class DefaultController
{
    public function index(Request $request, Bot $bot)
    {
        $bot->sendText(sprintf(
            'Hey from "%s" controller, request data %s',
            get_class($this),
            json_encode($request->getData())
        ));
    }
}
