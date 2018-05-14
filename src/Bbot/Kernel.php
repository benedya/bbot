<?php

namespace Bbot;

use Bbot\Request\Request;
use Psr\Container\ContainerInterface;

class Kernel
{
    protected $booted = false;
    /** @var ContainerInterface */
    protected $container;

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
        // todo implement that
    }

    protected function getController(Request $request): callable
    {
        // todo implement that
    }
}