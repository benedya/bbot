<?php

namespace Bbot\Tests;

use Bbot\CliLogger;

class MessengerBotBridge extends \PHPUnit_Framework_TestCase
{
    /** @var  \Bbot\Bridge\MessengerBotBridge */
    protected static $messengerBridge;

    public static function setUpBeforeClass()
    {
        self::$messengerBridge = new \Bbot\Bridge\MessengerBotBridge(
            '',
            1
        );
        self::$messengerBridge->setLogger(new CliLogger());
    }

    public static function tearDownAfterClass()
    {
        self::$messengerBridge = null;
    }

    public function testSendKeyboard()
    {
        $this->assertNull(self::$messengerBridge->sendKeyboard('test', [[]]));
    }

    public function testHideKeyboard()
    {
        $this->assertNull(self::$messengerBridge->hideKeyboard('test'));
    }

    public function tstGetUserId()
    {
        $this->assertEquals(1, self::$messengerBridge->getUserId());
    }

    public function testSendText()
    {
        $this->assertNull(self::$messengerBridge->sendText('test'));
    }
}
