<?php

namespace Bbot\AppBuilder;

use Bbot\Bridge\MessengerBotBridge;
use Bbot\Request\AbstractBotRequest;

class MessengerFactory extends AbstractFactory
{
    protected $pageToken;

    function __construct($pageToken)
    {
        $this->pageToken = $pageToken;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(array $data)
    {
        // todo it needs improve
        $message = $data['entry'][0]['messaging']['0'];
        return new \Bbot\Request\MessengerRequest($message);
    }

    /**
     * {@inheritdoc}
     */
    public function getBridge(AbstractBotRequest $botRequest)
    {
        return new \Bbot\Bridge\MessengerBotBridge(
            $this->pageToken,
            $botRequest->getUserData()['id'],
            true
        );
    }
}
