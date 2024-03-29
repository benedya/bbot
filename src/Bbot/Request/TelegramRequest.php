<?php

namespace Bbot\Request;

class TelegramRequest implements Request
{
    /** @var array */
    protected $data;
    /** @var array */
    protected $parameters;

    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function isInlineQuery(): bool
    {
        return isset($this->data['inline_query']) ? true : false;
    }

    public function isText(): bool
    {
        return isset($this->data['message']['text']) ? true : false;
    }

    public function getLocation(): ?array
    {
        $location = $this->data['message']['location'] ?? [];

        if (!$location) {
            return null;
        }

        if (!isset($location['latitude']) || !isset($location['longitude'])) {
            throw new \OutOfBoundsException('Location data not found.');
        }

        return [
            'latitude' => $location['latitude'],
            'longitude' => $location['longitude'],
        ];
    }

    public function isCommand(): bool
    {
        $type = $this->data['message']['entities']['0']['type'] ?? null;

        return  'bot_command' == $type ? true : false;
    }

    public function isStartMessage(): bool
    {
        return $this->isCommand() && $this->getTextMessage() === '/start';
    }

    public function getTextMessage(): string
    {
        return $this->data['message']['text'] ?? '';
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

    public function getMessageId(): string
    {
        if ($this->getPostback()) {
            $messageId = $this->data['callback_query']['message']['message_id'] ?? '';
        } else {
            $messageId = $this->data['message']['message_id'] ?? '';
        }

        if (!$messageId) {
            throw new \Error(sprintf('Cant get id of the message from data "%s"', json_encode($this->data)));
        }

        return $messageId;
    }

    public function getChatId(): string
    {
        if ($this->getPostback()) {
            $chatId = $this->data['callback_query']['from']['id'] ?? '';
        } elseif ($this->isInlineQuery()) {
            $chatId = $this->data['inline_query']['id'] ?? '';
        } elseif (isset($this->data['pre_checkout_query'])) {
            $chatId = $this->data['pre_checkout_query']['id'];
        } elseif (isset($this->data['my_chat_member'])) {
            $chatId = $this->data['my_chat_member']['from']['id'] ?? '';
        } else {
            $chatId = $this->data['message']['chat']['id'] ?? '';
        }

        if (!$chatId) {
            throw new \Error(sprintf('Cant get id of the chat from data "%s"', json_encode($this->data)));
        }

        return $chatId;
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
