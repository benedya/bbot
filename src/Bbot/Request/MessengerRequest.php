<?php

namespace Bbot\Request;

class MessengerRequest extends AbstractBotRequest
{
    protected $isProcessedData = false;

    public function __construct(array $data, array $conf = [])
    {
        $this->requestData = $data;
        $this->isPostBack = false;
        $this->conf = array_merge($this->conf, $conf);
    }

    public function processRequestData()
    {
        if ($this->isProcessedData) {
            return $this;
        }
        $this->userData = $this->requestData['sender'];
        // checks postback
        if (isset($this->requestData['postback'])) {
            $this->simpleText = $this->requestData['postback']['payload'];
            $this->setHandlerData($this->requestData['postback']['payload']);
            $this->isPostBack = true;
        } elseif (isset($this->requestData['optin'])) {
            if (isset($this->requestData['optin']['ref']) and 'welcomescreen' === $this->requestData['optin']['ref']) {
                $this->handler = 'welcome';
                $this->action = 'index';
                $this->simpleText = 'welcomescreen';
                $this->requestOptions = $this->requestData;
            }
        } elseif (isset($this->requestData['message']) and !empty($this->requestData['message'])) {
            $this->isTextMsg = true;
            $message = $this->requestData;
            // checks attachments
            if (isset($message['message']['attachments'])) {
                $attachment = array_pop($message['message']['attachments']);
                if (isset($attachment['payload']['coordinates'])) {
                    $this->requestOptions = $attachment['payload']['coordinates'];
                    $this->set('lat', $this->requestOptions['lat']);
                    $this->set('lng', $this->requestOptions['long']);
                    $this->handler = 'location';
                    $this->action = 'setlocation';
                }
                // processes message
            } else {
                $this->simpleText = trim($message['message']['text']);
                $this->handler = $this->conf['textHandler'];
                $this->action = $this->conf['textAction'];
            }
        }
        $this->isProcessedData = true;

        return $this;
    }

    public function isBtnClick()
    {
        return $this->isPostBack;
    }
}
