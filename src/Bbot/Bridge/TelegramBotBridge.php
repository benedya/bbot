<?php

namespace Bbot\Bridge;

use Bbot\CliLoggerTrait;
use pimax\FbBotApp;
use pimax\Messages\Message;
use pimax\Messages\MessageButton;
use pimax\Messages\MessageElement;
use pimax\Messages\StructuredMessage;
use Longman\TelegramBot\Entities\Update;
use Longman\TelegramBot\Exception\TelegramException;
use Longman\TelegramBot\Request;
use TelegramBot\Api\Types\ReplyKeyboardHide;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class TelegramBotBridge implements BotBridgeInterface
{
    use CliLoggerTrait;
    protected $sendMsgFromCli;
    protected $chatId;
    protected $messageId;

    function __construct($apiKey, $chatId, $sendMsgFromCli = false)
    {
        $this->chatId = $chatId;
        $this->bot = new \TelegramBot\Api\BotApi($apiKey);
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUserProfile()
    {
        $data = [];
        return array_merge($data, ['id' => $this->userId]);
    }

    public function sendText($text, $recipient = null)
    {
        $this->cliLog('Send text: "' . $text . '"');
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
        return $this->bot->sendMessage(
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
        // todo implement
    }

    public function buildButtons(array $data)
    {
        // todo implement
    }

    public function buildItemWithButtons(array $data, array $buttons = [])
    {
        // todo implement
    }

    public function sendListItems($recipient, array $items)
    {
        // todo implement
    }

    protected function sendBotMsg($msg)
    {
        // if script launched via cli no needs to send msg to bot
        if(!$this->sendMsgFromCli and php_sapi_name() == "cli") {
            $this->cliLog("SKIP SEND MSG BECAUSE SCRIPT LAUNCHED VIA CLI\n");
            return;
        }
        Request::sendChatAction(['chat_id' => $this->chatId, 'action' => 'typing']);
        $data = [
            'chat_id'             => $this->chatId,
            'reply_to_message_id' => $this->messageId,
        ];
        $data = array_merge($data, $msg);
        $result = Request::sendMessage($data);
        return $result;
    }
}
