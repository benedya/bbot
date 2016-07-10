<?php

namespace Bbot\Service;

use Application\Bot\Request\AbstractBotRequest;

class WelcomeService extends AbstractBotService
{
    public function showWelcomeMsg(AbstractBotRequest $botRequest)
    {
        $this->botBridge->sendText(10, 'Say hello!');
    }
}
