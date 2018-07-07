<?php

namespace Bbot\Bridge;

class MessengerBot implements Bot
{
    public function sendText($text, $recipient = null)
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function sendKeyboard($text, array $keyboard, $recipient = null)
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function hideKeyboard($text, $recipient = null)
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function sendImg($path, $caption = null, $recipient = null)
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function sendButtons(array $data, $recipient = null)
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function sendListItems(array $items, $recipient = null)
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function buildButtons(array $data)
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function buildItemWithButtons(array $data, array $buttons)
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function getTarget()
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }
}
