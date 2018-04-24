<?php

namespace Bbot\Handler;

use Bbot\BotApp;

abstract class AbstractHandler
{
    /** @var BotApp */
    protected $botApp;

    public function __construct(BotApp $botApp)
    {
        $this->botApp = $botApp;
    }
}
