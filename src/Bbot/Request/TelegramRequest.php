<?php

namespace Bbot\Request;

class TelegramRequest implements Request
{
    protected $data;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function isText(): bool
    {
        $msgData = end($this->data);
        reset($this->data);

        return isset($msgData['text']) && $msgData['text'];
    }

    public function getData()
    {
        // todo
    }

    public function getPostback()
    {
        // todo
    }

    public static function fromArray(array $data): Request
    {
        $response = new static($data);

        return $response;
    }

    public function getPlatform(): string
    {
        return 'telegram';
    }
}
