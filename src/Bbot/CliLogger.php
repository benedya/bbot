<?php

namespace Bbot;

use Psr\Log\AbstractLogger;

class CliLogger extends AbstractLogger
{
    public function log($level, $message, array $context = array())
    {
        // shows msg only in case when script was launched from clie
        if ($this->isCli()) {
            echo "\n ".date('d.m.Y H:i:s', time())." ($level): ".$this->interpolate($message, $context);
        }
    }

    protected function interpolate($message, array $context = array())
    {
        $replace = [];
        foreach ($context as $key => $val) {
            $replace['{'.$key.'}'] = $val;
        }
        return strtr($message, $replace);
    }

    protected function isCli()
    {
        return 'cli' === php_sapi_name();
    }
}
