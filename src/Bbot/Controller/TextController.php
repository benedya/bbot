<?php

namespace Bbot\Controller;

use Bbot\Bridge\Bot;
use Bbot\Request\Request;
use Bbot\Router;
use Psr\Container\ContainerInterface;

class TextController
{
    public function index(Request $request, Bot $bot, ContainerInterface $container)
    {
        $bot->sendText(sprintf('Hey from "%s" controller ;)', get_class($this)));

        $bot->sendButtons([
            'caption' => 'Do you to test this button?',
            'buttons' => [
                [
                    'type' => 'postback',
                    'title' => 'Yes',
                    'url' => Router::toPostback(TextController::class, 'button', [
                        'action' => 'confirm',
                    ]),
                ],
            ],
        ]);
    }

    public function button(Request $request, Bot $bot, ContainerInterface $container)
    {
        $bot->sendText('Hey from button '.$request->get('action'));
    }
}
