<?php

namespace Bbot\Request;

class MessengerRequest extends AbstractBotRequest
{
    protected $requestData;
    protected $delimiter = '___';
    protected $isPostBack;
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
        // checks postback
        if(isset($this->requestData['postback'])) {
            $this->simpleText = $this->requestData['postback']['payload'];
            $this->setHandlerData($this->requestData['postback']['payload']);
            $this->isPostBack = true;
        } elseif(isset($this->requestData['optin'])) {
            if(isset($this->requestData['optin']['ref']) and $this->requestData['optin']['ref'] == 'welcomescreen') {
                $this->handler = 'welcome';
                $this->action = 'index';
                $this->simpleText = 'welcomescreen';
                $this->requestOptions = $this->requestData;
            }
        } elseif(isset($this->requestData['message']) and !empty($this->requestData['message'])) {
            $this->isTextMsg = true;
            $message = $this->requestData;
            // checks attachments
            if(isset($message['message']['attachments'])) {
                $attachment = array_pop($message['message']['attachments']);
                if(isset($attachment['payload']['coordinates'])) {
                    $this->handler = 'location';
                    $this->action = 'setlocation';
                    $this->requestOptions = $attachment['payload']['coordinates'];
                    $this->simpleText = $this->handler;
                }
            // processes message
            } else {
                $this->simpleText = trim($message['message']['text']);
                $this->handler = $this->conf['textHandler'];
                $this->action = $this->conf['textAction'];
            }
        }
        return $this;
    }

    protected function setHandlerData($str)
    {
        $chunks = explode($this->delimiter, $str);
        if(count($chunks) < 2) {
            throw new \Exception('Wrong request data' . print_r($this->requestData, true));
        }
        $this->handler = array_shift($chunks);
        $this->action = array_shift($chunks);
        if(count($chunks) == 1) {
            parse_str(array_shift($chunks), $this->requestOptions);
        } else {
            $this->requestOptions = $chunks;
        }
    }

    public function isBtnClick()
    {
        return $this->isPostBack;
    }
}
