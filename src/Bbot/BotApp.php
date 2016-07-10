<?php

namespace BBot;

use Bbot\Bridge\BotBridgeInterface;
use Bbot\Request\AbstractBotRequest;

class BotApp extends Container
{
    use CliLoggerTrait;
    const EVENT_TYPE_PRE_HANDLER = 1;
    const EVENT_TYPE_AFTER_HANDLER = 2;
    const EVENT_SIGNAL_INTERRUPT = 1;
    const EVENT_SIGNAL_LISTEN = 2;

    protected $events;

    function __construct(BotBridgeInterface $botBridge, AbstractBotRequest $botRequest)
    {
        $this['welcome'] = $this->share(function() {
            return new \Application\Bot\Handler\WelcomeHandler($this);
        });
        // service section
        $this['service.welcome'] = $this->share(function() use($botBridge, $botRequest) {
            return new \Bbot\Service\WelcomeService($botBridge, $botRequest);
        });
        // register events
        $this
            ->addEvent(function() {

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
        if(!$this->offsetExists($handlerName)) {
            throw new \Exception('Handler "'. $handlerName .'" not found');
        }
        $handler = $this[$handlerName];
        $method = $botRequest->getAction() . 'Action';
        if(!method_exists($handler, $method)) {
            throw new \Exception('Method "'.$method.'" does not exists in class "'. get_class($handler) .'"');
        }
        $signal = $this->dispatchEvent(self::EVENT_TYPE_PRE_HANDLER);
        if($signal !== self::EVENT_SIGNAL_INTERRUPT) {
            $result = $handler->{$method}($botRequest);
            $this->dispatchEvent(self::EVENT_TYPE_AFTER_HANDLER);
            return $result;
        }
    }

    public function triggerHandler($handler, $action, AbstractBotRequest $botRequest)
    {
        $this->cliLog('TRIGGERED to handler "'
            . $handler . '" action "'
            . $action . '" request options '
            . print_r($botRequest->getRequestOptions(), true));
        $botRequest->setHandler($handler)->setAction($action)->setIsTriggered(true);
        return $this->handleRequest($botRequest);
    }

    protected function addEvent(\Closure $closure, $type)
    {
        if(!isset($this->events[$type])) {
            $this->events[$type] = [];
        }
        $this->events[$type][] = $closure;
        return $this;
    }

    protected function dispatchEvent($type)
    {
        if(isset($this->events[$type])) {
            foreach($this->events[$type] as $event) {
                $signal = $event();
                if($signal === self::EVENT_SIGNAL_INTERRUPT) {
                    return self::EVENT_SIGNAL_INTERRUPT;
                }
            }
        }
    }
}
