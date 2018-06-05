<?php

namespace Bbot\Request;

class TelegramRequest implements Request
{
    protected $data;

    function __construct(array $data)
    {
        $this->data = $data;
    }

    function isText(): bool
    {
        $msgData = end($this->data);
        reset($this->data);

        return isset($msgData['text']) && $msgData['text'];
    }

    function getData()
    {
        // todo
    }

    function getPostback()
    {
        // todo
    }

    static function fromArray(array $data): Request
    {
        $response = new static($data);

        return $response;
    }

    function getPlatform(): string
    {
        return 'telegram';
    }
}
