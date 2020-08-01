<?php

namespace Bbot\Request;

class ViberRequest implements Request
{
    private array $data;

    private array $parameters = [];

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function isInlineQuery(): bool
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function isText(): bool
    {
        return $this->data['message']['type'] === 'text';
    }

    public function isCommand(): bool
    {
        return false;
    }

    public function getTextMessage(): string
    {
        return $this->data['message']['text'];
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
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function getMessageId(): string
    {
        return $this->data['message_token'];
    }

    public function getChatId(): string
    {
        return $this->data['sender']['id'];
    }

    public static function fromArray(array $data): Request
    {
        return new static($data);
    }

    public function getPlatform(): string
    {
        return 'viber';
    }

    public function setParameters(array $parameters)
    {
        $this->parameters = $parameters;
    }
}
