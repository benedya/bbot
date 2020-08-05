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
        // html is not supported
        $text = strip_tags($text);

        $this->client->sendMessage(
            (new \Viber\Api\Message\Text())
                ->setSender($this->getSender())
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

    private function buildKeyboard(array $items, int $columns = 6): array
    {
        $buttons = [];
        $maxColumns = 6;

        foreach ($items as $k => $button) {
            if (is_array($button)) {
                $buttons = array_merge(
                    $buttons,
                    $this->buildKeyboard($button, (int)($maxColumns / count($button))),
                );
                continue;
            }

            $buttons[] = (new \Viber\Api\Keyboard\Button())
                    ->setColumns($columns)
                    ->setActionBody($button)
                    ->setText($button);
        }

        return $buttons;
    }

    public function sendKeyboard($text, array $keyboard, array $options = [])
    {
        $this->client->sendMessage(
            (new \Viber\Api\Message\Text())
                ->setSender($this->getSender())
                ->setReceiver($this->senderId)
                ->setText($text)
                ->setKeyboard(
                    (new \Viber\Api\Keyboard())->setButtons($this->buildKeyboard($keyboard))
                )
        );
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
        $captionArea = (new \Viber\Api\Keyboard\Button())
            ->setRows(2)
            ->setActionType('none')
            ->setText($data['caption'])
            ->setBgColor('#ffffff');

        $buttons = $this->buildButtons($data['buttons']);

        $this->client->sendMessage(
            (new \Viber\Api\Message\CarouselContent())
                ->setSender($this->getSender())
                ->setReceiver($this->senderId)
                ->setButtonsGroupRows(count($buttons) + 2)
                ->setButtons(array_merge([$captionArea], $buttons))
        );
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
        if ($countInRow !== -1) {
            $data = array_chunk($data, $countInRow);
        }

        $buttons = [];
        $maxButtonsInLine = 6;

        foreach ($data as $line) {
            $countButtons = count($line);

            foreach ($line as $btn) {
                $buttons [] = (new \Viber\Api\Keyboard\Button())
                    ->setColumns((int)($maxButtonsInLine / $countButtons))
                    ->setActionType($btn['type'] ?? 'reply')
                    ->setActionBody('https://www.google.com')
                    ->setText(sprintf(
                        '<span style="color: %s;">%s</span>',
                        $btn['text_color'] ?? '#ffffff',
                        $btn['title']
                    ))
                    ->setBgColor($btn['bg_color'] ?? '#7C69E9')
                    ;
            }
        }

        return $buttons;
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

    private function getSender(): Sender
    {
        return new Sender([
            'name' => $this->senderName,
        ]);
    }
}
