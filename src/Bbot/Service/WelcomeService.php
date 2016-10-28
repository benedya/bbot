<?php

namespace Bbot\Service;

class WelcomeService extends AbstractBotService
{
    public function showWelcomeMsg()
    {
        $this->botBridge->sendText('Hello!');
    }
}
