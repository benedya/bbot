<?php

namespace Bbot\Tests\Bridge;

use Bbot\Bridge\TelegramBot;
use PHPUnit\Framework\TestCase;
use TelegramBot\Api\BotApi;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;

class TelegramBotTest extends TestCase
{
    private TelegramBot $telegramBot;
    private BotApi $botApiMocked;

    protected function setUp(): void
    {
        $this->telegramBot = new TelegramBot(-1, -1);
        $this->botApiMocked = $this->createMock(\TelegramBot\Api\BotApi::class);

        $this->telegramBot->setBot(
            $this->botApiMocked
        );
    }

    public function testSendFlashMessageFailed(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Field "callbackQueryId" is required.');

        $this->telegramBot->sendFlashMessage('test');
    }

    public function testSendFlashMessageSuccess(): void
    {
        $callbackQueryId = -1;
        $message = 'test';
        $showAlert = true;

        $this
            ->botApiMocked
            ->expects($this->once())
            ->method('answerCallbackQuery')
            ->with($callbackQueryId, $message, $showAlert)
        ;

        $this->telegramBot->sendFlashMessage($message, [
            'callbackQueryId' => $callbackQueryId,
            'showAlert' => $showAlert,
        ]);
    }

    public function testSendButtons(): void
    {
        $caption = 'test';
        $buttons = [];

        $this->botApiMocked
            ->expects($this->once())
            ->method('sendMessage')
            ->with(
                $this->telegramBot->getTarget(),
                $caption,
                null,
                false,
                null,
                new InlineKeyboardMarkup($buttons)
            )
        ;

        $this->telegramBot->sendButtons([
            'caption' => $caption,
            'buttons' => $buttons,
        ]);
    }

    public function testEditButtons(): void
    {
        $editMessageId = -1;
        $buttons = [];

        $this->botApiMocked
            ->expects($this->once())
            ->method('editMessageReplyMarkup')
            ->with(
                $this->telegramBot->getTarget(),
                $editMessageId,
                new InlineKeyboardMarkup($buttons)
            )
        ;

        $this->telegramBot->sendButtons([
            'caption' => 'test',
            'buttons' => $buttons,
            'editMessageId' => $editMessageId,
        ]);
    }
}
