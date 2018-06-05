<?php

namespace Bbot\Controller;

use Bbot\Request\Request;
use Psr\Container\ContainerInterface;

class TextController
{
    public function index(Request $request, ContainerInterface $container)
    {
        echo 'Hey from text controller';
    }
}
