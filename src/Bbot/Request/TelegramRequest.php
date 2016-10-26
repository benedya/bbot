<?php

namespace Bbot\Request;

class TelegramRequest extends AbstractBotRequest
{
    function __construct(array $data, array $conf = [])
    {
        $this->requestData = $data;
        $this->isPostBack = false;
        $this->conf = array_merge($this->conf, $conf);
    }

    public function processRequestData()
    {
        if(isset($this->requestData['callback_query'])) {
            $callbackQuery = $this->requestData['callback_query'];
            $this->simpleText = $callbackQuery['data'];
            $this->setHandlerData($callbackQuery['data']);
            $this->userData = $callbackQuery['message']['chat'];
            $this->isPostBack = true;
        } else {
            $isCommand = false;
            $message = $this->requestData['message'];
            $this->userData = $message['chat'];
            if(isset($message['entities']) and count($message['entities'])) {
                $entity = array_pop($message['entities']);
                if(isset($entity['type']) and $entity['type'] === 'bot_command') {
                    $isCommand = true;
                }
            }
            if($isCommand) {
                $this->handler = 'commands';
                $this->action = preg_replace("/^\//", "", $message['text']);
            } elseif(isset($message['location'])) {
                $this->requestOptions = $message['location'];
                $this->set('lat', $message['location']['latitude']);
                $this->set('lng', $message['location']['longitude']);
                $this->handler = 'location';
                $this->action = 'setlocation';
            } else {
                $this->isTextMsg = true;
                $this->simpleText = trim($message['text']);
                $this->handler = $this->conf['textHandler'];
                $this->action = $this->conf['textAction'];
            }
        }
        return $this;
    }

    public function isBtnClick()
    {
        return $this->isPostBack;
    }
}
