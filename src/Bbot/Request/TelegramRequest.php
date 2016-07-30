<?php

namespace Bbot\Request;

class TelegramRequest extends AbstractBotRequest
{
    protected $requestData;
    protected $delimiter = '___';
    protected $isPostBack;
    protected $chatData;
    protected $conf = [
        'textHandler' => 'common',
        'textAction' => 'index',
    ];

    function __construct(array $data, array $conf = [])
    {
        $this->requestData = $data;
        $this->isPostBack = false;
        $this->conf = array_merge($this->conf, $conf);
    }

    public function processRequestData()
    {
        $isCommand = false;
        $message = $this->requestData['message'];
        $this->chatData = $message['chat'];
        if(isset($message['entities']) and count($message['entities'])) {
            $entity = array_pop($message['entities']);
            if(isset($entity['type']) and $entity['type'] === 'bot_command') {
                $isCommand = true;
            }
        }
        if($isCommand) {
            $this->handler = 'commands';
            $this->action = preg_replace("/^\//", "", $message['text']);
        } else {
            $this->simpleText = trim($message['text']);
            $this->handler = $this->conf['textHandler'];
            $this->action = $this->conf['textAction'];
        }
        return $this;
    }

    public function isBtnClick()
    {
        return $this->isPostBack;
    }

    /**
     * @return mixed
     */
    public function getChatData()
    {
        return $this->chatData;
    }
}
