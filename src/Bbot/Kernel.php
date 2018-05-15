<?php

namespace Bbot;

use Bbot\Request\Request;
use Psr\Container\ContainerInterface;
use Pimple\Psr11\Container as PsrContainer;
use Pimple\Container;
class Kernel
{
    protected $booted = false;
    /** @var ContainerInterface */
    protected $container;
    protected $serviceProviders;

    function __construct(array $serviceProviders)
    {
        $this->serviceProviders = $serviceProviders;
    }

    public function handle(Request $request)
    {
        $this->boot();

        if ($controller = $this->getController($request)) {
            \call_user_func_array($controller,[$request, $this->container]);
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
            $container->register( new $provider);
        }

        $this->container = new PsrContainer($container);
    }

    protected function getController(Request $request): callable
    {
        if ($request->isText()) {
            // todo TextController
        } else {
            if (Router::fromPostback($request)) {
                //
            }
        }
    }
}