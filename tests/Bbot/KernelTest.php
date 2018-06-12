<?php

namespace Bbot\Tests;

use Bbot\Kernel;
use Bbot\Provider\AppProvider;
use Bbot\Request\TelegramRequest;
use Psr\Log\NullLogger;

class KernelTest extends \PHPUnit_Framework_TestCase
{
    protected $exampleTelegramRequestMsg = '{"update_id":1,"channel_post":{"message_id":1,"chat":{"id":-1,"title":"Title","username":"username","type":"channel"},"date":1527529238,"text":"Since yesterday"}}';

    /**
     * @dataProvider kernelProvider
     */
    public function testHandleTelegramRequest(Kernel $kernel, TelegramRequest $request)
    {
        $this->assertNull($kernel->handle($request));
    }

    public function kernelProvider()
    {
        return [
            [(new Kernel(
                [new AppProvider()],
                new \Bbot\Bridge\TelegramBot(
                    getenv('telegram_api_key'),
                    getenv('telegram_chat_id'),
                    new NullLogger()
                )
            )), new \Bbot\Request\TelegramRequest(json_decode($this->exampleTelegramRequestMsg, true))],
            // todo implement kernels for others bot-platforms
        ];
    }
}
