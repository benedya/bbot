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
    /** @var array */
    protected $controllers = [];
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
            \call_user_func_array($handlerData, [$request, $this->bot, $this->container]);
        } else {
            // todo ?
        }
        // todo handle request
    }

    public function setTextController(string $class): self
    {
        $this->controllers['text'] = $class;

        return $this;
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
            $container->register(new $provider());
        }

        $this->container = new PsrContainer($container);
    }

    protected function getController(Request $request)
    {
        if ($request->isText()) {
            if (isset($this->controllers['text'])) {
                return [new $this->controllers['text'], 'index'];
            } else {
                throw new \Error('Controller with `index` action for handling text requests not set.');
            }
        } else {
            if (Router::fromPostback($request)) {

            }
        }
    }
}
