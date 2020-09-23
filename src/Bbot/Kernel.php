<?php

namespace Bbot;

use Bbot\Bridge\Bot;
use Bbot\Request\Request;
use Psr\Container\ContainerInterface;
use Pimple\Psr11\Container as PsrContainer;
use Pimple\Container;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

class Kernel
{
    /** @var bool */
    protected $booted = false;
    /** @var ContainerInterface */
    protected $container;
    /** @var array */
    protected $serviceProviders;
    /** @var Bot */
    protected $bot;

    private LoggerInterface $logger;

    public function __construct(array $serviceProviders, Bot $bot, LoggerInterface $logger = null)
    {
        $this->serviceProviders = $serviceProviders;
        $this->bot = $bot;
        $this->logger = $logger ?? new NullLogger();
    }

    public function handle(Request $request)
    {
        $this->boot();

        if ($handlerData = $this->getController($request)) {
            \call_user_func_array($handlerData, [
                $request,
                $this->bot,
                $this->container->get('router'),
                $this->container,
            ]);
        } else {
            throw new \Error(sprintf(
                'Handler not found for request data %s',
                json_encode($request->getData(), true)
            ));
        }
    }

    protected function boot()
    {
        if (!$this->booted) {
            $this->initContainer();

            $this->booted = true;
        }
    }

    protected function initContainer()
    {
        $container = new Container();

        foreach ($this->serviceProviders as $provider) {
            $container->register($provider);
        }

        $this->container = new PsrContainer($container);
    }

    protected function getController(Request $request)
    {
        /** @var \Bbot\Route\Router $router */
        $router = $this->container->get('router');
        $controller = null;
        $defaultAction = 'defaultAction';
        $action = $defaultAction;
        $specifiedTextHandler = $router->getTxtHandler();

        $processHandlerData = function (array $handlerData) use (&$request) {
            $class = $handlerData['class'] ?? '';
            $method = $handlerData['method'] ?? '';
            $parameters = $handlerData['parameters'] ?? [];

            if ($parameters) {
                $request->setParameters($parameters);
            }

            return [$class, $method];
        };

        if ($request->isCommand() or $request->isText()) {
            $handlerData = null;

            if ($request->isCommand()) {
                $controllerKey = 'command_controller';
                $msg = $request->getTextMessage();
                $action = explode(' ', substr($msg, 1))['0'];
            } else {
                $controllerKey = 'text_controller';

                if ($specifiedTextHandler) {
                    $handlerData = $specifiedTextHandler;
                }
            }

            if ($handlerData) {
                [$controller, $action] = $processHandlerData($handlerData);
            } else {
                if (!$controller = $this->container->get($controllerKey)) {
                    throw new \Error(sprintf("Controller '%s' not found.", $controllerKey));
                }
            }
        } else {
            if ($postback = $router->fromPostback($request)) {
                [$controller, $action] = $processHandlerData($postback);

                if ($request->get('_removeable')) {
                    try {
                        $this->bot->deleteMessage([
                            'chatId' => $request->getChatId(),
                            'messageId' => $request->getMessageId(),
                        ]);
                    } catch (\Exception $exception) {
                        $this->logger->error(sprintf(
                            'Can not remove the message. %s',
                            $exception->getTraceAsString()
                        ));
                    }
                }
            } elseif ($this->container->has('default_controller')) {
                $controller = $this->container->get('default_controller');
            }
        }

        if ($controller and $action) {
            if (is_string($controller)) {
                if (!class_exists($controller)) {
                    throw new \Error(sprintf('Class "%s" not found.', $controller));
                }

                $controller = new $controller();
            }

            if (!method_exists($controller, $action)) {
                if ($request->isText() or $request->isCommand()) {
                    $action = $defaultAction;
                }

                if (!method_exists($controller, $action)) {
                    throw new \Error(sprintf(
                        'Method "%s" not found in class "%s".',
                        $action,
                        get_class($controller)
                    ));
                }
            }

            return [$controller, $action];
        }
    }

    public function setLogger(LoggerInterface $logger): self
    {
        $this->logger = $logger;

        return $this;
    }
}
