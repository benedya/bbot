<?php

namespace Bbot\AppBuilder;

use Bbot\Request\AbstractBotRequest;

abstract class AbstractFactory
{
    protected $sendMsgFromCli = false;

    /**
     * @param array $data
     *
     * @return \Bbot\Request\AbstractBotRequest
     */
    abstract public function getRequest(array $data);

    /**
     * @return \Bbot\Bridge\BotBridgeInterface
     */
    abstract public function getBridge(AbstractBotRequest $botRequest);

    /**
     * @param array $requestData
     *
     * @return \Bbot\BotApp|bool
     */
    public function handle(array $requestData)
    {
        $botRequest = $this->getRequest($requestData);
        $botRequest->processRequestData();
        if ($botRequest->canHandle()) {
            $botBridge = $this->getBridge($botRequest);
            $botApp = new \Bbot\BotApp($botBridge, $botRequest, $this->getLogger());
            $botApp->handleRequest($botRequest);
            return $botApp;
        }
        return false;
    }

    /**
     * Allows sending a message when app launched via cli.
     *
     * @return $this
     */
    public function allowSendMsgFromCli()
    {
        $this->sendMsgFromCli = true;
        return $this;
    }

    /**
     * @return \Psr\Log\LoggerInterface
     */
    protected function getLogger()
    {
        return new \Bbot\CliLogger();
    }
}
