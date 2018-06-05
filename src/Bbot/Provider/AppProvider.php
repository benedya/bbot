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
    }
}
