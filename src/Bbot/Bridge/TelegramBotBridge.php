<?php

namespace Bbot\Bridge;

use Psr\Log\LoggerAwareTrait;
use TelegramBot\Api\Types\ReplyKeyboardHide;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class TelegramBotBridge implements BotBridgeInterface
{
    use LoggerAwareTrait;

    protected $sendMsgFromCli;
    protected $chatId;
    protected $messageId;
    protected $userData;

    function __construct($apiKey, $chatId, array $userData, $sendMsgFromCli = false)
    {
        $this->userData = $userData;
        $this->chatId = $chatId;
        $this->bot = new \TelegramBot\Api\BotApi($apiKey);
    }

    public function getUserId()
    {
        return $this->userData['id'];
    }

    public function getUserProfile()
    {
        return $this->userData;
    }

    public function sendText($text, $recipient = null)
    {
        $this->logger->info('Send text: "' . $text . '"');
        $recipient = $recipient ? $recipient : $recipient = $this->chatId;
        $this->bot->sendMessage($recipient, $text);
    }

    public function sendKeyboard($text, array $keyboard, $recipient = null)
    {
        $recipient = $recipient ? $recipient : $recipient = $this->chatId;
        $item = new ReplyKeyboardMarkup($keyboard, true);
        $this->bot->sendMessage(
            $recipient,
            $text,
            null,
            false,
            null,
            $item
        );
    }

    public function hideKeyboard($text, $recipient = null)
    {
        $recipient = $recipient ? $recipient : $recipient = $this->chatId;
        $item = new ReplyKeyboardHide(true);
        $this->bot->sendMessage(
            $recipient,
            $text,
            null,
            false,
            null,
            $item
        );
    }

    public function sendImg($path, $caption = null, $recipient = null)
    {
        $recipient = $recipient ? $recipient : $recipient = $this->chatId;
        if(!is_file($path)) {
            throw new \Exception('File "'.$path.'" not found.');
        }
        return $this->bot->sendPhoto($recipient, new \CURLFile($path), $caption);
    }

    public function sendButtons($recipient, array $data)
    {
        $this->logger->alert('This is not supported yet.');
        // todo implement
    }

    public function buildButtons(array $data)
    {
        $this->logger->alert('This is not supported yet.');
        // todo implement
    }

    public function buildItemWithButtons(array $data, array $buttons = [])
    {
        $this->logger->alert('This is not supported yet.');
        // todo implement
    }

    public function sendListItems($recipient, array $items)
    {
        $this->logger->alert('This is not supported yet.');
        // todo implement
    }
}
