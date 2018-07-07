<?php

namespace Bbot\Provider;

use Bbot\Controller\TextController;
use Pimple\Container;

class AppProvider implements \Pimple\ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple['text_controller'] = function () {
            return new TextController();
        };

        $pimple['router'] = function () {
            return new \Bbot\Route\Router(
                new \Bbot\Route\Storage\ArrayStorage()
            );
        };
    }
}
