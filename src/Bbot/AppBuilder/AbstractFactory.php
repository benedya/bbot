<?php

namespace Bbot\AppBuilder;

use Bbot\Request\AbstractBotRequest;

abstract class AbstractFactory
{
    /**
     * @param array $data
     * @return \Bbot\Request\AbstractBotRequest
     */
    abstract function getRequest(array $data);
    /**
     * @return \Bbot\Bridge\BotBridgeInterface
     */
    abstract function getBridge(AbstractBotRequest $botRequest);

    /**
     * @param array $requestData
     * @return \Bbot\BotApp|bool
     */
    public function handle(array $requestData)
    {
        $botRequest = $this->getRequest($requestData);
        $botRequest->processRequestData();
        if($botRequest->canHandle()) {
            $botBridge = $this->getBridge($botRequest);
            $botApp = new \Bbot\BotApp($botBridge, $botRequest, $this->getLogger());
            $botApp->handleRequest($botRequest);
            return $botApp;
        }
        return false;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    protected function getLogger()
    {
        return new \Bbot\CliLogger();
    }
}
