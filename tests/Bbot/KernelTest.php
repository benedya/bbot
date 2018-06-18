<?php

namespace Bbot\Tests;

use Bbot\Controller\TextController;
use Bbot\Kernel;
use Bbot\Provider\AppProvider;
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
        return [
            [(new Kernel(
                [new AppProvider()],
                new \Bbot\Bridge\TelegramBot(
                    getenv('TELEGRAM_API_KEY'),
                    getenv('TELEGRAM_CHAT_ID'),
                    new NullLogger()
                )
            ))->setTextController(TextController::class), new \Bbot\Request\TelegramRequest(json_decode(getenv('TELEGRAM_REQUEST'), true))],
            // todo implement kernels for others bot-platforms
        ];
    }
}
