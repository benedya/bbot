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
        $this->sendMsgFromCli = $sendMsgFromCli;
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
        if(!$this->canSend()) {
            return;
        }
        $recipient = $recipient ? $recipient : $recipient = $this->chatId;
        $this->bot->sendMessage($recipient, $text);
    }

    public function sendKeyboard($text, array $keyboard, $recipient = null)
    {
        if(!$this->canSend()) {
            return;
        }
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
        if(!$this->canSend()) {
            return;
        }
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
        if(!$this->canSend()) {
            return;
        }
        $recipient = $recipient ? $recipient : $recipient = $this->chatId;
        $tmpFile = false;
        // checks if there isset a local file using server variable
        if(!is_file($path) and isset($_SERVER['DOCUMENT_ROOT']) and $_SERVER['DOCUMENT_ROOT']) {
            $arr = parse_url($path);
            $serverPath = $_SERVER['DOCUMENT_ROOT'] . $arr['path'];
            if(is_file($serverPath)) {
                $path = $serverPath;
            }
        }
        // if there is no file - download it and create a tmp file
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
        $buttons = [];
        // checks if there are buttons
        if(is_array($caption)) {
            $data = $caption;
            $caption = $data['caption'];
            $buttons = $data['buttons'];
            // if buttons were not built yet - build it now
            if(is_array($buttons)) {
                $buttons = $this->buildButtons($buttons);
            }
        }
        $this->bot->sendPhoto(
            $recipient,
            new \CURLFile($path),
            $caption,
            null,
            $buttons
        );
        if($tmpFile) {
            unlink($tmpFile);
        }
    }

    public function sendButtons(array $data, $recipient = null)
    {
        if(!$this->canSend()) {
            return;
        }
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
            'simpleText' => $data['title']."\r\n".$data['subtitle'],
            'image' => (isset($data['image']) ? $data['image'] : null),
            'parseMode' => 'HTML',
            'buttons' => $this->buildButtons($buttons),
        ];
    }

    public function sendListItems(array $items, $recipient = null)
    {
        if(!$this->canSend()) {
            return;
        }
        $recipient = $recipient ? $recipient : $recipient = $this->chatId;
        foreach($items as $item) {
            if($item['image']) {
                $this->sendImg($item['image'], [
                    'caption' => $item['simpleText'],
                    'buttons' => $item['buttons'],
                ]);
            } else {
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

    /**
     * @return bool
     */
    protected function canSend()
    {
        if(!$this->sendMsgFromCli and php_sapi_name() == "cli") {
            $this->logger->alert("SKIP SEND MSG BECAUSE SCRIPT LAUNCHED VIA CLI\n");
            return false;
        }
        return true;
    }
}
