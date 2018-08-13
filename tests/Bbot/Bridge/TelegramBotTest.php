<?php

namespace Bbot\Tests;

use Bbot\CliLogger;
use Bbot\Bridge\TelegramBot;

class TelegramBotTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider bridgeProvider
     */
    public function testSendFile(TelegramBot $bot)
    {
        $this->assertNull($bot->sendFile(realpath(__DIR__.'/../../.data/test.txt'), 'Test'));
    }

    public function bridgeProvider()
    {
        $bridge = new \Bbot\Bridge\TelegramBot(
            getenv('TELEGRAM_API_KEY'),
            getenv('TELEGRAM_CHAT_ID'),
            new CliLogger()
        );

        return [[$bridge]];
    }
}
