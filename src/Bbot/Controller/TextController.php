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
        $bot->sendText(sprintf(
            'Hey from button, got action fom request "%s"',
            $request->get('action')
        ));

        $bot->sendButtons([
            'caption' => 'Wanna specify text handler for text message?',
            'buttons' => [
                [
                    'type' => 'postback',
                    'title' => 'Yes',
                    'url' => $router->toPostback(TextController::class, 'setTextHandler'),
                ],
            ],
        ]);
    }

    public function setTextHandler(Request $request, Bot $bot, Router $router)
    {
        $bot->sendText(sprintf(
            'Ok, write some text and "%s::%s" text controller will be called ;)',
            get_class($this),
            'textHandler'
        ));

        $router->setTxtHandler(TextController::class, 'textHandler');
    }

    public function textHandler(Request $request, Bot $bot, Router $router)
    {
        $bot->sendText(sprintf(
            'Action "%s::%s" got text "%s"',
            get_class($this),
            'textHandler',
            $request->getTextMessage()
        ));
    }
}
