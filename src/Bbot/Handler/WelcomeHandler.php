<?php

namespace Bbot\Handler;

use Bbot\Request\AbstractBotRequest;

class WelcomeHandler extends AbstractHandler
{
    public function indexAction(AbstractBotRequest $botRequest)
    {
        $this->botApp->getWelcomeService()->showWelcomeMsg($botRequest);
    }
}
