<?php

namespace Bbot;

use Bbot\Bridge\MessengerBotBridge;
use Bbot\Request\AbstractBotRequest;

class BotRequestProcessBuilder
{
    /** @var AbstractBotRequest  */
    protected $botRequest;
    protected $userId;
    protected $sendMsgFromCli;
    protected $botBridge;

    function __construct($userId, $sendMsgFromCli = false)
    {
        $this->userId = $userId;
        $this->sendMsgFromCli = $sendMsgFromCli;
    }

    /**
     * @return BotApp
     * @throws \Exception
     */
    public function createBotApp()
    {
        if(!$this->botBridge) {
            throw new \Exception('Bot bridge was not set.');
        }
        if(!$this->botRequest) {
            throw new \Exception('Bot request was not set.');
        }
        return new BotApp($this->botBridge, $this->botRequest);
    }

    public static function createPostBackRequest($handler, $action, array $data = [])
    {
        return [
            'postback' => [
                'payload' => BotRouter::generatePostBack($handler, $action, $data),
            ],
        ];
    }

    public static function createTextRequest($msg)
    {
        return [
            'message' => [
                'text' => $msg,
            ],
        ];
    }

    /**
     * @param $pageToken
     * @param $requestData
     * @param array $requestConf
     * @return $this
     */
    public function initMessengerBotRequest($pageToken, $requestData, array $requestConf = [])
    {
        $this->createMessengerBotBridge($pageToken);
        $this->createMessengerRequest($requestData, $requestConf);
        return $this;
    }

    /**
     * @param $pageToken
     * @return $this
     */
    protected function createMessengerBotBridge($pageToken)
    {
        $this->botBridge = new MessengerBotBridge(
            $pageToken,
            $this->userId,
            $this->sendMsgFromCli
        );
        return $this;
    }

    protected function createMessengerRequest($requestData, array $requestConf = [])
    {
        $this->botRequest = (new \Bbot\Request\MessengerRequest($requestData, $requestConf))
            ->processRequestData();
        return $this;
    }

    /**
     * @return AbstractBotRequest
     */
    public function getBotRequest()
    {
        return $this->botRequest;
    }

}
