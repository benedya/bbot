<?php

namespace Bbot\Request;

class TelegramRequest implements Request
{
    protected $data;
    protected $parameters;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function isText(): bool
    {
        return isset($this->data['message']['text']) ? true : false;
    }

    public function get(string $key)
    {
        return $this->parameters[$key] ?? null;
    }

    public function getData()
    {
        return $this->data;
    }

    public function getPostback()
    {
        return $this->data['callback_query']['data'] ?? null;
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

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }
}
