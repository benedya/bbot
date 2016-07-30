<?php

namespace Bbot\Handler;

use Bbot\Handler\AbstractHandler;
use Bbot\Request\AbstractBotRequest;

class CommonHandler extends AbstractHandler
{
    public function indexAction(AbstractBotRequest $botRequest)
    {
        $this->botApp->getWelcomeService()->getBotBridge()->sendText($botRequest->getSimpleText());
    }
}
