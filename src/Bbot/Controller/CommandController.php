<?php

namespace Bbot\Controller;

use Bbot\Bridge\Bot;
use Bbot\Request\Request;

class CommandController
{
    public function start(Request $request, Bot $bot)
    {
        $bot->sendText(sprintf('Hey from "%s" controller :)', get_class($this)));
    }
}
