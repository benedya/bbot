<?php

namespace Bbot\Handler;

use Bbot\Request\AbstractBotRequest;

class CommandsHandler extends AbstractHandler
{
    public function startAction(AbstractBotRequest $botRequest)
    {
        $this->botApp->getWelcomeService()->getBotBridge()->sendText('Start!');
    }
}
