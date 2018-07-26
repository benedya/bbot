<?php

namespace Bbot;

use Bbot\Bridge\Bot;
use Bbot\Request\Request;
use Psr\Container\ContainerInterface;
use Pimple\Psr11\Container as PsrContainer;
use Pimple\Container;

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

    public function __construct(array $serviceProviders, Bot $bot)
    {
        $this->serviceProviders = $serviceProviders;
        $this->bot = $bot;
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
            // todo ?
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
        $action = null;

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
                $action = 'index';
                $handlerData = $router->getTxtHandler();
            }

            if ($handlerData) {
                list($controller, $action) = $processHandlerData($handlerData);
            } else {
                if (!$controller = $this->container->get($controllerKey)) {
                    throw new \Error(sprintf("Controller '%s' not found.", $controllerKey));
                }
            }
        } else {
            if ($postback = $router->fromPostback($request)) {
                list($controller, $action) = $processHandlerData($postback);
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
                throw new \Error(sprintf('Method "%s" not found in class "%s".', $controller, $action));
            }

            return [$controller, $action];
        }
    }
}
