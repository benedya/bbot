<?php

namespace Bbot\Controller;

use Bbot\Bridge\Bot;
use Bbot\Request\Request;
use Bbot\Route\Router;

class TextController
{
    public function defaultAction(Request $request, Bot $bot, Router $router)
    {
        $bot->sendText(sprintf('Hey from "`%s`" controller ;)', get_class($this)), ['parseMode' => 'markdown']);
        $bot->sendButtons([
            'caption' => 'Do you to <b>test</b> this <i>button</i>?',
            'buttons' => [
                [
                    'type' => 'postback',
                    'title' => 'Yes',
                    'url' => $router->toPostback(TextController::class, 'button', [
                        'action' => 'confirm',
                        '_removeable' => true,
                    ]),
                ],
            ],
        ], ['parseMode' => 'html']);
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
        $parameter = 'test';

        $bot->sendText(sprintf(
            'Ok, write some text and "%s::%s" will be called (with parameter "%s") ;)',
            get_class($this),
            'textHandler',
            $parameter
        ));

        $router->setTxtHandler(TextController::class, 'textHandler', [
            'parameter' => $parameter,
        ]);
    }

    public function textHandler(Request $request, Bot $bot, Router $router)
    {
        $bot->sendText(sprintf(
            'Action "%s::%s" got text "%s" (parameter "%s")',
            get_class($this),
            'textHandler',
            $request->getTextMessage(),
            $request->get('parameter')
        ));
    }
}
