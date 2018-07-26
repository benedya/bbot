<?php

namespace Bbot\Tests;

use Bbot\Kernel;
use Bbot\Provider\DefaultProvider;
use Bbot\Request\TelegramRequest;
use Psr\Log\NullLogger;

class KernelTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider kernelProvider
     */
    public function testHandleTelegramRequest(Kernel $kernel, TelegramRequest $request)
    {
        $this->assertNull($kernel->handle($request));
    }

    public function kernelProvider()
    {
        $kernel = (new Kernel(
            [new DefaultProvider()],
            new \Bbot\Bridge\TelegramBot(
                getenv('TELEGRAM_API_KEY'),
                getenv('TELEGRAM_CHAT_ID'),
                new NullLogger()
            )
        ));

        return [
            [$kernel, new \Bbot\Request\TelegramRequest(json_decode(getenv('TELEGRAM_REQUEST'), true))],
            [$kernel, new \Bbot\Request\TelegramRequest(json_decode(getenv('TELEGRAM_POSTBACK_REQUEST'), true))],
            // todo implement kernels for others bot-platforms
        ];
    }
}
