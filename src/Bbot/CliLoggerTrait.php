<?php

namespace Bbot;

trait CliLoggerTrait
{
    protected function cliLog($msg)
    {
        // shows msg only in case when script was launched from clie
        if ($this->isCli()) {
            echo "\n " . date('d.m.Y H:i:s', time()) . " - " . $msg;
        }
    }

    protected function isCli()
    {
        return php_sapi_name() == "cli";
    }

    protected function printArray(array $data)
    {
        echo "\n---- start print array ----\n";
        foreach($data as $k => $v) {
            echo "\n -> " . $k;
            if(is_array($v)) {
                foreach($v as $v2) {
                    echo " : " . $v2 . "; ";
                }
            } else {
                echo ": " . $v . " \n";
            }
        }
        echo "\n---- end print array ----\n";
    }
}
