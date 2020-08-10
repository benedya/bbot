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
        return false;
    }

    public function isText(): bool
    {
        if (isset($this->data['silent']) && $this->data['silent']) {
            return false;
        }

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
        if ($this->isShareContact()) {
            return null;
        }

        return $this->data['message']['text'] ?? null;
    }

    private function isShareContact(): bool
    {
        return $this->data['message']['type'] === 'contact';
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
