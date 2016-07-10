<?php

namespace Bbot\Bridge;

use Bbot\CliLoggerTrait;
use pimax\FbBotApp;
use pimax\Messages\Message;
use pimax\Messages\MessageButton;
use pimax\Messages\MessageElement;
use pimax\Messages\StructuredMessage;

class MessengerBotBridge implements BotBridgeInterface
{
    use CliLoggerTrait;
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

    public function sendText($recipient, $text)
    {
        $this->cliLog('Send text: "' . $text . '"');
        $this->sendBotMsg(new Message($recipient, $text));
    }

    public function sendButtons($recipient, array $data)
    {
        $countButtons = count($data['buttons']);
        if($countButtons < 1 or $countButtons > 3) {
            throw new \Exception('Number of must be from 1 till 3, got ' . $countButtons . ', data ' . print_r($data, true));
        }
        $urls = [];
        array_walk($data['buttons'], function($item) use(&$urls) {
            $urls[] = $item['url'];
        });
        $buttons = $this->buildButtons($data['buttons']);
        $this->cliLog('Send ' . $countButtons . ' buttons, caption "' . $data['caption'] . '", urls: ' . join(', ', $urls));
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

    public function sendListItems($recipient, array $items)
    {
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
            $this->cliLog("SKIP SEND MSG BECAUSE SCRIPT LAUNCHED VIA CLI\n");
            return;
        }
        $res = $this->bot->send($msg);
        if(isset($res['error'])) {
            throw new \Exception('Api returned error: ' . print_r($res, true));
        }
    }
}
