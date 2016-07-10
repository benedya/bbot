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

    function __construct($requestData, $userId, $sendMsgFromCli = false)
    {
        $this->botRequest = $this->createMessengerRequest($requestData);
        $this->userId = $userId;
        $this->sendMsgFromCli = $sendMsgFromCli;
    }

    /**
     * @return BotApp
     */
    public function createBotApp()
    {
        return new BotApp($this->createMessengerBotBridge(), $this->botRequest);
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

    protected function createMessengerBotBridge($pageToken)
    {
        return new MessengerBotBridge(
            $pageToken,
            $this->userId,
            $this->sendMsgFromCli
        );
    }

    protected function createMessengerRequest($requestData)
    {
        return (new MessengerRequest($requestData))
            ->processRequestData();
    }

    /**
     * @return AbstractBotRequest
     */
    public function getBotRequest()
    {
        return $this->botRequest;
    }

}
