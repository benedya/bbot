<?php

namespace Bbot\Service;

use Bbot\Request\AbstractBotRequest;

class WelcomeService extends AbstractBotService
{
    public function showWelcomeMsg(AbstractBotRequest $botRequest)
    {
        $this->botBridge->sendText('Say hello!');
    }
}
