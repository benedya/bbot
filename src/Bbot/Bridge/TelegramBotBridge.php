<?php

namespace Bbot\Bridge;

use Psr\Log\LoggerAwareTrait;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\ReplyKeyboardHide;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class TelegramBotBridge implements BotBridgeInterface
{
    use LoggerAwareTrait;

    protected $sendMsgFromCli;
    protected $chatId;
    protected $messageId;
    protected $userData;

    function __construct($apiKey, array $userData, $sendMsgFromCli = false)
    {
        $this->userData = $userData;
        $this->chatId = $userData['id'];
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
        $tmpFile = false;
        // create a tmp file in case when given url on a file
        if(!is_file($path)) {
            if(getimagesize($path)) {
               $ext = pathinfo($path, PATHINFO_EXTENSION);
                $tmpFile = sys_get_temp_dir() . '/' . md5($path) . '.' . $ext;
                file_put_contents($tmpFile, file_get_contents($path));
                $path = $tmpFile;
            } else {
                throw new \Exception('File "'.$path.'" not found.');
            }
        }
        $this->bot->sendPhoto($recipient, new \CURLFile($path), $caption);
        if($tmpFile) {
            unlink($tmpFile);
        }
    }

    public function sendButtons(array $data, $recipient = null)
    {
        $recipient = $recipient ? $recipient : $recipient = $this->chatId;
        return $this->bot->sendMessage(
            $recipient,
            $data['caption'],
            null,
            false,
            null,
            $this->buildButtons($data['buttons'])
        );
    }

    public function buildButtons(array $data)
    {
        $data = array_chunk($data, 3);
        $buttons = [];
        foreach($data as $line) {
            $listBtns = [];
            foreach($line as $btn) {
                $type = ($btn['type'] == 'postback') ? 'callback_data' : 'url';
                $listBtns[] = ['text' => $btn['title'], $type => $btn['url']];
            }
            $buttons[] = $listBtns;
        }
        return new InlineKeyboardMarkup($buttons);
    }

    public function buildItemWithButtons(array $data, array $buttons = [])
    {
        // todo implement msg with img
        return [
            'text' => "<b>".$data['title']."</b>\r\n".$data['subtitle'],
            'parseMode' => 'HTML',
            'buttons' => $this->buildButtons($buttons),
        ];
    }

    public function sendListItems(array $items, $recipient = null)
    {
        $recipient = $recipient ? $recipient : $recipient = $this->chatId;
        foreach($items as $item) {
            $this->bot->sendMessage(
                $recipient,
                $item['text'],
                isset($item['parseMode']) ? $item['parseMode'] : false,
                false,
                null,
                $item['buttons']
            );
        }
    }
}
