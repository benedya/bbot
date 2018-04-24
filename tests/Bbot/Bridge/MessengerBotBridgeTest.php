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
            1,
            new CliLogger()
        );
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

    public function testSendImg()
    {
        // todo Is there a reason to use url of some img?
        $this->assertNull(self::$messengerBridge->sendImg(''));
    }

    public function testGetUserId()
    {
        $this->assertEquals(1, self::$messengerBridge->getUserId());
    }

    public function testGetUserProfile()
    {
        $this->assertArrayHasKey('id', self::$messengerBridge->getUserProfile());
    }

    public function testSendText()
    {
        $this->assertNull(self::$messengerBridge->sendText('test'));
    }

    /**
     * @dataProvider buttonsProvider
     */
    public function testSendButtons($buttons)
    {
        $this->assertNull(self::$messengerBridge->sendButtons([
            'caption' => 'Hey there!',
            'buttons' => $buttons,
        ]));
    }

    /**
     * @dataProvider buttonsProvider
     */
    public function testBuildButtons($buttons)
    {
        $this->assertInternalType('array', self::$messengerBridge->buildButtons($buttons));
    }

    /**
     * @dataProvider buttonsProvider
     */
    public function testBuildItemWithButtons($buttons)
    {
        $this->assertInstanceOf(
            \pimax\Messages\MessageElement::class,
            self::$messengerBridge->buildItemWithButtons(
                [
                    'title' => 'Build something for me',
                    'subtitle' => 'See up',
                ],
                $buttons
            )
        );
    }

    /**
     * @dataProvider buttonsProvider
     */
    public function testSendListItems($buttons)
    {
        $listItems = [];
        $listItems[] = self::$messengerBridge->buildItemWithButtons(
            [
                'title' => 'Build something for me',
                'subtitle' => 'See up',
            ],
            $buttons
        );
        $this->assertNull(self::$messengerBridge->sendListItems($listItems));
    }

    public function buttonsProvider()
    {
        $buttons = [];
        $buttons[] = [
            'type' => 'postback',
            'title' => 'Show me the google',
            'url' => 'http://google.com'
        ];
        return [[$buttons]];
    }
}
