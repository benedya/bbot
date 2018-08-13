<?php

namespace Bbot\Builder;

use Bbot\Bridge\Bot;
use Bbot\Request\Request;

class TelegramFactory extends Factory
{
    /** @var string */
    protected $apiKey;
    /** @var int */
    protected $chatId;

    public function __construct(string $apiKey, int $chatId)
    {
        $this->apiKey = $apiKey;
        $this->chatId = $chatId;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(array $data): Request
    {
        return new \Bbot\Request\TelegramRequest($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getBot(): Bot
    {
        return new \Bbot\Bridge\TelegramBot(
            $this->apiKey,
            $this->chatId
        );
    }
}
