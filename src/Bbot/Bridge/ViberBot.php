<?php

namespace Bbot\Bridge;

use Bbot\DTO\ButtonDTO;
use Bbot\DTO\ItemWithButtonsDTO;
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
        $viewType = $options['view_type'] ?? '';
        $buttons = $data['buttons'];

        if ($viewType === self::VIEW_TYPE_CAROUSEL) {
            $readyToSendButtons = [];
            $captionRows = 2;
            $maxAllowedRows = 7;
            $maxAllowedColumns = 6;

            foreach ($buttons as $itemWithButtons) {
                if (!$itemWithButtons instanceof ItemWithButtonsDTO) {
                    throw new \UnexpectedValueException('Unexpected button type.');
                }

                $countButtons = count($itemWithButtons->getButtons());

                if ($countButtons + $captionRows > $maxAllowedRows) {
                    throw new \Exception('Reached max allowed rows.');
                }

                if ($itemWithButtons->getImageUrl()) {
                    $readyToSendButtons[] = (new \Viber\Api\Keyboard\Button())
                        ->setRows($maxAllowedRows - $countButtons - $captionRows)
                        ->setActionType('none')
                        ->setImage($itemWithButtons->getImageUrl())
                    ;
                }

                $text = $itemWithButtons->getParameter('viber_name') ?? $itemWithButtons->getName();

                $readyToSendButtons[] = (new \Viber\Api\Keyboard\Button())
                    ->setRows($itemWithButtons->getImageUrl() ? $captionRows: $maxAllowedRows - $countButtons)
                    ->setActionType('none')
                    ->setTextHAlign('left')
//                    ->setTextHAlign('bottom')
                    ->setText($text)
                    ->setTextSize('medium')
                    ->setBgColor('#ffffff')
                ;

                $readyToSendButtons = array_merge(
                    $readyToSendButtons,
                    $this->buildButtons(
                        $itemWithButtons->getButtons()
                    )
                );
            }

//            throw new \Exception(print_r($readyToSendButtons, true));

            $this->client->sendMessage(
                (new \Viber\Api\Message\CarouselContent())
                    ->setSender($this->getSender())
                    ->setReceiver($this->senderId)
                    ->setButtonsGroupColumns($maxAllowedColumns)
                    ->setButtonsGroupRows($maxAllowedRows)
                    ->setButtons($readyToSendButtons)
            );
        } else {
            $captionArea = [];

            if (isset($data['caption'])) {
                $captionArea = (new \Viber\Api\Keyboard\Button())
                    ->setRows(2)
                    ->setActionType('none')
                    ->setText($data['caption'])
                    ->setBgColor('#ffffff');
            }

            $buttons = $this->buildButtons($buttons);

            $this->client->sendMessage(
                (new \Viber\Api\Message\CarouselContent())
                    ->setSender($this->getSender())
                    ->setReceiver($this->senderId)
                    ->setButtonsGroupRows(count($buttons) + 2)
                    ->setButtons(
                        $captionArea ? array_merge([$captionArea], $buttons) : $buttons
                    )
            );
        }
    }

    public function sendListItems(array $items)
    {
        throw new \Error(sprintf(
            'Method "%s::%s" is not implemented yet.',
            get_class($this),
            __METHOD__
        ));
    }

    public function buildButtons(array $data, int $countInRow = 1, int $availableRows = null)
    {
        if ($countInRow !== -1) {
            $data = array_chunk($data, $countInRow);
        }

        $buttons = [];
        $maxButtonsInLine = 6;

        foreach ($data as $line) {
            $countButtons = count($line);

            foreach ($line as $btn) {
                if ($btn instanceof ButtonDTO) {
                    $actionType = $btn->getType();
                    $text = $btn->getName();
                    $isPostBack = $btn->isPostBackType();
                    $postBackData = $btn->getPostBackData();
                } else {
                    $actionType = $btn['type'] ?? 'reply';
                    $text = $btn['title'];
                    $isPostBack = $actionType === 'postback';
                    $postBackData = $btn['url'];
                }

                $button = (new \Viber\Api\Keyboard\Button())
                    ->setColumns((int)($maxButtonsInLine / $countButtons))

                    ->setActionType($actionType)
                    ->setText(sprintf(
                        '<span style="color: %s;">%s</span>',
                        '#ffffff',
                        $text
                    ))
                    ->setBgColor('#7C69E9')
                    ;

                if ($availableRows) {
                    $button->setRows((int)($availableRows / $countButtons));
                }

                if ($isPostBack) {
                    $button
                        ->setActionType('reply')
                        ->setActionBody($postBackData)
                        ->setSilent(true);
                }

                $buttons[] = $button;
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
