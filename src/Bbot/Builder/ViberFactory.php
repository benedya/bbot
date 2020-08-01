<?php

namespace Bbot\Builder;

use Bbot\Bridge\Bot;
use Bbot\Request\Request;

class ViberFactory extends Factory
{
    protected string $apiKey;

    private string $senderName;

    private string $senderId;

    public function __construct(string $apiKey, string $senderId, string $senderName)
    {
        $this->apiKey = $apiKey;
        $this->senderName = $senderName;
        $this->senderId = $senderId;
    }

    /**
     * {@inheritdoc}
     */
    public function getRequest(array $data): Request
    {
        return new \Bbot\Request\ViberRequest($data);
    }

    /**
     * {@inheritdoc}
     */
    public function getBot(): Bot
    {
        return new \Bbot\Bridge\ViberBot(
            $this->apiKey,
            $this->senderId,
            $this->senderName
        );
    }
}
