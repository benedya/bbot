<?php

namespace Bbot\Bridge;

use Viber\Client;
use Viber\Api\Sender;

class ViberBot implements Bot
{
    private Client $client;

    private string $senderName;

    private string $senderId;

    private string $apiKey;

    public function __construct(string $apiKey, string $senderId, string $senderName)
    {
        $this->client = new Client(['token' => $apiKey]);
        $this->senderName = $senderName;
        $this->senderId = $senderId;
        $this->apiKey = $apiKey;
    }

    public function sendText($text, array $options = [])
    {
        $botSender = new Sender([
            'name' => $this->senderName,
        ]);

        $this->client->sendMessage(
            (new \Viber\Api\Message\Text())
                ->setSender($botSender)
                ->setReceiver($this->senderId)
                ->setText($text)
        );
    }

    public function sendFlashMessage(string $text, array $options = [])
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function sendKeyboard($text, array $keyboard, array $options = [])
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function hideKeyboard($text, array $options = [])
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function sendImg($path, $caption = null)
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function sendFile($path, $caption = null)
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function sendButtons(array $data, array $options = [])
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function sendListItems(array $items)
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function buildButtons(array $data, int $countInRow = 1)
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

    public function deleteMessage(array $data)
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function sendQueryResults(array $data, array $options = [])
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }
}
