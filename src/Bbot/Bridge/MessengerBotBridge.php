<?php

namespace Bbot\Bridge;

use pimax\FbBotApp;
use pimax\Messages\ImageMessage;
use pimax\Messages\Message;
use pimax\Messages\MessageButton;
use pimax\Messages\MessageElement;
use pimax\Messages\StructuredMessage;
use Psr\Log\LoggerAwareTrait;

class MessengerBotBridge implements BotBridgeInterface
{
    use LoggerAwareTrait;

    /** @var FbBotApp */
    protected $bot;
    protected $userId;
    protected $sendMsgFromCli;

    function __construct($pageToken, $userId, $sendMsgFromCli = false)
    {
        $this->bot = new FbBotApp($pageToken);
        $this->userId = $userId;
        $this->sendMsgFromCli = $sendMsgFromCli;
    }

    public function sendKeyboard($text, array $keyboard, $recipient = null)
    {
        $this->logger->alert('This is not supported yet.');
        // todo implement when it will be possible
    }

    public function hideKeyboard($text, $recipient = null)
    {
        $this->logger->alert('This is not supported yet.');
        // todo implement when it will be possible
    }

    public function sendImg($path, $caption = null, $recipient = null)
    {
        $recipient = $recipient ? $recipient : $this->userId;
        $this->sendBotMsg(new ImageMessage($recipient, $path));
        if($caption) {
            $this->sendText($caption, $recipient);
        }
    }

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUserProfile()
    {
        $fbUser = (array)$this->bot->userProfile($this->userId);
        $fbUser = array_pop($fbUser);
        return array_merge($fbUser, ['id' => $this->userId]);
    }

    public function sendText($text, $recipient = null)
    {
        $this->logger->info('Send text: "' . $text . '"');
        $recipient = $recipient ? $recipient : $this->userId;
        $this->sendBotMsg(new Message($recipient, $text));
    }

    public function sendButtons(array $data, $recipient = null)
    {
        $recipient = $recipient ? $recipient : $this->userId;
        $countButtons = count($data['buttons']);
        if($countButtons < 1 or $countButtons > 3) {
            throw new \Exception('Number of must be from 1 till 3, got ' . $countButtons . ', data ' . print_r($data, true));
        }
        $urls = [];
        array_walk($data['buttons'], function($item) use(&$urls) {
            $urls[] = $item['url'];
        });
        $buttons = $this->buildButtons($data['buttons']);
        $this->logger->info('Send ' . $countButtons . ' buttons, caption "' . $data['caption'] . '", urls: ' . join(', ', $urls));
        $this->sendBotMsg(new StructuredMessage($recipient,
            StructuredMessage::TYPE_BUTTON,
            [
                'text' => $data['caption'],
                'buttons' => $buttons
            ]
        ));
    }

    public function buildButtons(array $data)
    {
        $buttons = [];
        foreach($data as $item) {
            $type = ($item['type'] == 'postback') ? MessageButton::TYPE_POSTBACK : MessageButton::TYPE_WEB;
            $buttons[] = new MessageButton(
                $type,
                $item['title'],
                $item['url']
            );
        }

        return $buttons;
    }

    public function buildItemWithButtons(array $data, array $buttons = [])
    {
        return new MessageElement(
            $data['title'],
            $data['subtitle'],
            (isset($data['image']) ? $data['image'] : ''),
            $this->buildButtons($buttons));
    }

    public function sendListItems(array $items, $recipient = null)
    {
        $recipient = $recipient ? $recipient : $this->userId;
        $this->sendBotMsg(new StructuredMessage($recipient,
            StructuredMessage::TYPE_GENERIC, [
                'elements' => $items
            ]
        ));
    }

    protected function sendBotMsg($msg)
    {
        // if script launched via cli no needs to send msg to bot
        if(!$this->sendMsgFromCli and php_sapi_name() == "cli") {
            $this->logger->alert("SKIP SEND MSG BECAUSE SCRIPT LAUNCHED VIA CLI\n");
            return;
        }
        $res = $this->bot->send($msg);
        if(isset($res['error'])) {
            throw new \Exception('Api returned error: ' . print_r($res, true));
        }
    }
}
