<?php

namespace Bbot\Provider;

use Bbot\Controller\CommandController;
use Bbot\Controller\DefaultController;
use Bbot\Controller\TextController;
use Pimple\Container;

class DefaultProvider implements \Pimple\ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['text_controller'] = function () {
            return new TextController();
        };

        $pimple['command_controller'] = function () {
            return new CommandController();
        };

        $pimple['default_controller'] = function () {
            return new DefaultController();
        };

        $pimple['router'] = function () {
            return new \Bbot\Route\Router(
                new \Bbot\Storage\ArrayStorage()
            );
        };
    }
}
