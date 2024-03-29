<?php

namespace Bbot\Builder;

use Bbot\Bridge\Bot;
use Bbot\Kernel;
use Bbot\Provider\DefaultProvider;
use Bbot\Request\Request;

abstract class Factory
{
    abstract public function getRequest(array $data): Request;

    abstract public function getBot(): Bot;

    public function buildKernel(array $providers = []): Kernel
    {
        return new Kernel(array_merge([new DefaultProvider()], $providers), $this->getBot());
    }

    protected function getLogger()
    {
        return new \Bbot\CliLogger();
    }
}
