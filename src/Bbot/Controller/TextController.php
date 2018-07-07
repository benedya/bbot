<?php

namespace Bbot\Controller;

use Bbot\Bridge\Bot;
use Bbot\Request\Request;
use Bbot\Route\Router;

class TextController
{
    public function index(Request $request, Bot $bot, Router $router)
    {
        $bot->sendText(sprintf('Hey from "%s" controller ;)', get_class($this)));

        $bot->sendButtons([
            'caption' => 'Do you to test this button?',
            'buttons' => [
                [
                    'type' => 'postback',
                    'title' => 'Yes',
                    'url' => $router->toPostback(TextController::class, 'button', [
                        'action' => 'confirm',
                    ]),
                ],
            ],
        ]);
    }

    public function button(Request $request, Bot $bot, Router $router)
    {
        $bot->sendText('Hey from button '.$request->get('action'));
    }
}
