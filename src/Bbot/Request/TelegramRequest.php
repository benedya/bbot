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
        return isset($this->data['text']) && $this->data['text'];
    }

    public function getData()
    {
        return $this->data;
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
