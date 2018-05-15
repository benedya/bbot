<?php

namespace Bbot\Provider;

use Bbot\Controller\TextController;
use Pimple\Container;

class AppProvider implements \Pimple\ServiceProviderInterface
{
    public function register(Container $pimple)
    {
        $pimple[TextController::class] = function () {
            return new TextController();
        };
    }
}