<?php

namespace Bbot\Tests\Builder;

use PHPUnit\Framework\TestCase;

class TelegramFactoryTest extends TestCase
{
    protected $exampleRequestMsg = '{"update_id":1,"channel_post":{"message_id":1,"chat":{"id":-1,"title":"Title","username":"username","type":"channel"},"date":1527529238,"text":"Since yesterday"}}';

    /** @var \Bbot\Builder\TelegramFactory */
    protected static $factory;

    public static function setUpBeforeClass(): void
    {
        self::$factory = new \Bbot\Builder\TelegramFactory(
            '',
            -1
        );
    }

    public static function tearDownAfterClass(): void
    {
        self::$factory = null;
    }

    public function testGetRequest()
    {
        $this->assertInstanceOf(
            \Bbot\Request\TelegramRequest::class,
            self::$factory->getRequest(json_decode($this->exampleRequestMsg, true))
        );
    }

    public function testGetBot()
    {
        $this->assertInstanceOf(
            \Bbot\Bridge\TelegramBot::class,
            self::$factory->getBot()
        );
    }

    public function testBuildKernel()
    {
        $this->assertInstanceOf(
            \Bbot\Kernel::class,
            self::$factory->buildKernel()
        );
    }
}
