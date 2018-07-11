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
        // todo handle request
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
        if ($request->isCommand() or $request->isText()) {
            if ($request->isCommand()) {
                $controllerKey = 'command_controller';
                $msg = $request->getTextMessage();
                $action = explode(' ', substr($msg, 1))['0'];
            } else {
                $controllerKey = 'text_controller';
                $action = 'index';
            }

            if (!$controller = $this->container->get($controllerKey)) {
                throw new \Error(sprintf("Controller '%s' not found.", $controllerKey));
            }

            if (!method_exists($controller, $action)) {
                throw new \Error(sprintf(
                    "Method '%s' not found in controller '%s'",
                    $action,
                    get_class($controller)
                ));
            }

            return [$controller, $action];
        } else {
            if ($postback = $this->container->get('router')->fromPostback($request)) {
                $class = $postback['class'] ?? '';
                $method = $postback['method'] ?? '';
                $parameters = $postback['parameters'] ?? [];

                if (!class_exists($class)) {
                    throw new \Error(sprintf('Class "%s" not found.', $class));
                }

                if (!method_exists($class, $method)) {
                    throw new \Error(sprintf('Method "%s" not found in class "%s".', $method, $class));
                }

                if ($parameters) {
                    $request->setParameters($parameters);
                }

                return [new $class(), $method];
            }
        }
    }
}
