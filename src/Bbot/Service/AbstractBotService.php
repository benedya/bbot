<?php

namespace Bbot\Service;

use Bbot\Bridge\BotBridgeInterface;
use Bbot\Request\AbstractBotRequest;
use Psr\Log\LoggerInterface;

abstract class AbstractBotService
{
    /** @var BotBridgeInterface */
    protected $botBridge;
    /** @var AbstractBotRequest */
    protected $botRequest;
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(BotBridgeInterface $botBridge, AbstractBotRequest $botRequest, LoggerInterface $logger)
    {
        $this->botBridge = $botBridge;
        $this->botRequest = $botRequest;
        $this->logger = $logger;
    }

    /**
     * @return AbstractBotRequest
     */
    public function getBotRequest()
    {
        return $this->botRequest;
    }

    /**
     * @param $botBridge
     *
     * @return $this
     */
    public function setBotBridge(BotBridgeInterface $botBridge)
    {
        $this->botBridge = $botBridge;

        return $this;
    }

    /**
     * @return BotBridgeInterface
     */
    public function getBotBridge()
    {
        return $this->botBridge;
    }
}
