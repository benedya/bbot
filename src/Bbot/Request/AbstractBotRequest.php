<?php

namespace Bbot\Request;

abstract class AbstractBotRequest
{
    protected $handler;
    protected $action;
    protected $requestOptions;
    protected $simpleText;
    protected $isTriggered;
    protected $isTextMsg;
    protected $requestData;
    protected $userData;
    protected $delimiter = '___';
    protected $isPostBack;
    protected $conf = [
        'textHandler' => 'common',
        'textAction' => 'index',
    ];

    public function getHandler()
    {
        return $this->handler;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function canHandle()
    {
        if($this->action and $this->handler) {
            return true;
        }
        return false;
    }

    /**
     * @return mixed
     */
    public function getRequestOptions()
    {
        return $this->requestOptions;
    }

    /**
     * @param $name
     * @return null
     */
    public function get($name)
    {
        return isset($this->requestOptions[$name]) ? $this->requestOptions[$name] : null;
    }

    public function set($key, $value)
    {
        $this->requestOptions[$key] = $value;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getSimpleText()
    {
        return $this->simpleText;
    }

    /**
     * @param $action
     * @return $this
     */
    public function setAction($action)
    {
        $this->action = $action;
        return $this;
    }

    /**
     * @param $handler
     * @return $this
     */
    public function setHandler($handler)
    {
        $this->handler = $handler;
        return $this;
    }

    /**
     * @param $requestOptions
     * @return $this
     */
    public function setRequestOptions($requestOptions)
    {
        $this->requestOptions = $requestOptions;
        return $this;
    }

    /**
     * @param mixed $isTriggered
     * @return AbstractBotRequest
     */
    public function setIsTriggered($isTriggered)
    {
        $this->isTriggered = $isTriggered;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isTriggered()
    {
        return $this->isTriggered;
    }

    /**
     * @param mixed $simpleText
     * @return AbstractBotRequest
     */
    public function setSimpleText($simpleText)
    {
        $this->simpleText = $simpleText;
        return $this;
    }

    /**
     * @return mixed
     */
    public function isTextMsg()
    {
        return $this->isTextMsg;
    }

    /**
     * @return mixed
     */
    public function getUserData()
    {
        return $this->userData;
    }

    /**
     * @param mixed $isTextMsg
     * @return AbstractBotRequest
     */
    public function setIsTextMsg($isTextMsg)
    {
        $this->isTextMsg = $isTextMsg;
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

    public abstract function isBtnClick();
    public abstract function processRequestData();
}
