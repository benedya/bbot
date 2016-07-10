<?php

namespace Bbot\Handler;

use BBot\BotApp;

abstract class AbstractHandler
{
    /** @var BotApp */
    protected $botApp;

    function __construct(BotApp $botApp)
    {
        $this->botApp = $botApp;
    }

}
