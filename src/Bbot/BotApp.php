<?php

namespace Bbot;

use Bbot\Bridge\BotBridgeInterface;
use Bbot\Request\AbstractBotRequest;
use Psr\Log\LoggerInterface;

class BotApp extends Container
{
    const EVENT_TYPE_PRE_HANDLER = 1;
    const EVENT_TYPE_AFTER_HANDLER = 2;
    const EVENT_SIGNAL_INTERRUPT = 1;
    const EVENT_SIGNAL_LISTEN = 2;

    protected $events;
    /** @var LoggerInterface */
    protected $logger;

    public function __construct(BotBridgeInterface $botBridge, AbstractBotRequest $botRequest, LoggerInterface $logger)
    {
        $botBridge->setLogger($logger);
        $this->logger = $logger;
        $this['welcome'] = $this->share(function () {
            return new \Bbot\Handler\WelcomeHandler($this);
        });
        $this['commands'] = $this->share(function () {
            return new \Bbot\Handler\CommandsHandler($this);
        });
        $this['common'] = $this->share(function () {
            return new \Bbot\Handler\CommonHandler($this);
        });
        // service section
        $this['service.welcome'] = $this->share(function () use ($botBridge, $botRequest, $logger) {
            return new \Bbot\Service\WelcomeService($botBridge, $botRequest, $logger);
        });
        // register events
        $this
            ->addEvent(function () {
            }, self::EVENT_TYPE_PRE_HANDLER);
    }

    /**
     * @return \Bbot\Service\WelcomeService
     */
    public function getWelcomeService()
    {
        return $this['service.welcome'];
    }

    public function handleRequest(AbstractBotRequest $botRequest)
    {
        $handlerName = $botRequest->getHandler();
        if (!$this->offsetExists($handlerName)) {
            throw new \Exception('Handler "'.$handlerName.'" not found');
        }
        $handler = $this[$handlerName];
        $method = $botRequest->getAction().'Action';
        if (!method_exists($handler, $method)) {
            throw new \Exception('Method "'.$method.'" does not exists in class "'.get_class($handler).'"');
        }
        $signal = $this->dispatchEvent(self::EVENT_TYPE_PRE_HANDLER);
        if (self::EVENT_SIGNAL_INTERRUPT !== $signal) {
            $result = $handler->{$method}($botRequest);
            $this->dispatchEvent(self::EVENT_TYPE_AFTER_HANDLER);
            return $result;
        }
    }

    public function triggerHandler($handler, $action, AbstractBotRequest $botRequest)
    {
        $this->logger->info('TRIGGERED to handler "'
            .$handler.'" action "'
            .$action.'" request options '
            .print_r($botRequest->getRequestOptions(), true));
        $botRequest->setHandler($handler)->setAction($action)->setIsTriggered(true);
        return $this->handleRequest($botRequest);
    }

    protected function addEvent(\Closure $closure, $type)
    {
        if (!isset($this->events[$type])) {
            $this->events[$type] = [];
        }
        $this->events[$type][] = $closure;
        return $this;
    }

    protected function dispatchEvent($type)
    {
        if (isset($this->events[$type])) {
            foreach ($this->events[$type] as $event) {
                $signal = $event();
                if (self::EVENT_SIGNAL_INTERRUPT === $signal) {
                    return self::EVENT_SIGNAL_INTERRUPT;
                }
            }
        }
    }
}
