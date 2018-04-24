<?php

namespace Bbot\AppBuilder;

use Bbot\Bridge\MessengerBotBridge;
use Bbot\Request\AbstractBotRequest;

class TelegramFactory extends AbstractFactory
{
    protected $apiKey;

    function __construct($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(array $data)
    {
        return new \Bbot\Request\TelegramRequest($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getBridge(AbstractBotRequest $botRequest)
    {
        $bridge = new \Bbot\Bridge\TelegramBotBridge(
            $this->apiKey,
            $botRequest->getUserData(),
            $this->getLogger(),
            $this->sendMsgFromCli
        );

        return $bridge;
    }
}
