<?php

namespace Bbot\Bridge;

use Bbot\DTO\ButtonDTO;
use Bbot\DTO\CompositeButtonInterface;
use Bbot\DTO\ItemWithButtonsDTO;
use Viber\Client;
use Viber\Api\Sender;

class ViberBot implements Bot
{
    public const MAX_CAROUSEL_ITEMS = 6;

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

        foreach ($items as $k => $buttonData) {
            if (is_array($buttonData)) {
                $buttons = array_merge(
                    $buttons,
                    $this->buildKeyboard($buttonData, (int)($maxColumns / count($buttonData))),
                );
                continue;
            }


            $button = (new \Viber\Api\Keyboard\Button())
                ->setColumns($columns)
            ;

            if ($buttonData instanceof ButtonDTO) {
                if ($buttonData->isPhoneRequestType()) {
                    $button->setActionType('share-phone');
                }

                $button
                    ->setActionBody($buttonData->getName())
                    ->setText($buttonData->getName());
            } else {
                $button->setActionBody($buttonData)->setText($buttonData);
            }

            $button->setBgColor('#7C69E9');

            $buttons[] = $button;
        }

        return $buttons;
    }

    public function sendKeyboard($text, array $keyboard, array $options = [])
    {
        $countInRow = -1;

        if (count($keyboard) === 1 && !is_array($keyboard['0'])) {
            $countInRow = 1;
        }

        $this->client->sendMessage(
            (new \Viber\Api\Message\Text())
                ->setSender($this->getSender())
                ->setReceiver($this->senderId)
                ->setText(strip_tags($text))
                ->setKeyboard(
                    (new \Viber\Api\Keyboard())
                        ->setButtons($this->buildButtons($keyboard, $countInRow))
                )
            ->setMinApiVersion(3) // todo to make it depended on smth?
        );
    }

    public function hideKeyboard($text, array $options = [])
    {
        // not existed
    }

    public function sendImg($path, $caption = null)
    {
        $buttons = [];
        // checks if there are buttons
        if (is_array($caption)) {
            $data = $caption;
            $caption = $data['caption'];
            $buttons = $data['buttons'] ?? [];
            $buttons = $this->buildButtons($buttons);
        }

        if (!$caption) {
            throw new \BadMethodCallException('Caption can not be empty.');
        }

        $this->client->sendMessage(
            (new \Viber\Api\Message\Picture())
                ->setMedia($path)
                ->setSender($this->getSender())
                ->setReceiver($this->senderId)
                ->setText(strip_tags($caption)) // tags not supported
                ->setKeyboard(
                    (new \Viber\Api\Keyboard())->setButtons($buttons)
                )
                ->setMinApiVersion(3) // todo to make it depended on smth?
        );
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
        $caption = $data['caption'] ?? null;
        $buttons = $this->buildButtons($buttons);

        if ($caption) {
            $this->client->sendMessage(
                (new \Viber\Api\Message\Text())
                    ->setSender($this->getSender())
                    ->setReceiver($this->senderId)
                    ->setText(strip_tags($caption)) // tags not supported
                    ->setKeyboard(
                        (new \Viber\Api\Keyboard())->setButtons($buttons)
                    )
                    ->setMinApiVersion(3) // todo to make it depended on smth?
            );
        } else {
            $this->client->sendMessage(
                (new \Viber\Api\Message\CarouselContent())
                    ->setSender($this->getSender())
                    ->setReceiver($this->senderId)
                    ->setButtonsGroupRows(count($buttons) + 2)
                    ->setButtons($buttons)
            );
        }
    }

    public function sendListItems(array $items)
    {
        $readyToSendButtons = [];
        $captionRows = 2;
        $maxAllowedRows = 7;
        $maxAllowedColumns = 6;

        foreach ($items as $itemWithButtons) {
            if (!$itemWithButtons instanceof CompositeButtonInterface) {
                throw new \UnexpectedValueException('Unexpected button type.');
            }

            $usedRows = 0;
            $countButtons = count($itemWithButtons->getButtons());

            if ($countButtons + $captionRows > $maxAllowedRows) {
                throw new \Exception('Reached max allowed rows.');
            }

            if ($itemWithButtons->getImageUrl()) {
                $button = (new \Viber\Api\Keyboard\Button())
                    ->setRows($maxAllowedRows - $countButtons - $captionRows)
                    ->setActionType('none')
                    ->setImage($itemWithButtons->getImageUrl())
                ;

                $usedRows += $button->getRows();

                $readyToSendButtons[] = $button;
            }

            $text = $itemWithButtons->getParameter('viber_name') ?? $itemWithButtons->getName();

            if (!empty($text)) {
                $button = (new \Viber\Api\Keyboard\Button())
                    ->setRows($itemWithButtons->getImageUrl() ? $captionRows: $maxAllowedRows - $countButtons)
                    ->setActionType('none')
                    ->setTextHAlign('left')
                    ->setText($text)
                    ->setTextSize('medium')
                    ->setBgColor('#ffffff')
                ;
                $usedRows += $button->getRows();

                $readyToSendButtons[] = $button;
            }

            $readyToSendButtons = array_merge(
                $readyToSendButtons,
                $this->buildButtons(
                    $itemWithButtons->getButtons(),
                    1,
                    $maxAllowedRows - $usedRows,
                    )
            );
        }

        $this->client->sendMessage(
            (new \Viber\Api\Message\CarouselContent())
                ->setSender($this->getSender())
                ->setReceiver($this->senderId)
                ->setButtonsGroupColumns($maxAllowedColumns)
                ->setButtonsGroupRows($maxAllowedRows)
                ->setButtons($readyToSendButtons)
        );
    }

    public function buildButtons(array $data, int $countInRow = 1, int $availableRows = null, bool $isRecursion = false)
    {
        if ($countInRow !== -1) {
            $data = array_chunk($data, $countInRow);
        }

        $buttons = [];
        $maxButtonsInLine = 6;
        $countButtonsRows = count($data);
        $maxColumns = 6;
        foreach ($data as $line) {
            $countButtons = count($line);

            foreach ($line as $btn) {
                if ($btn instanceof ButtonDTO && $btn->hasButtons() && !$isRecursion) {

                    $buttons = array_merge(
                        $buttons,
                        $this->buildButtons(
                            array_merge([$btn], $btn->getButtons()),
                            (int)($maxColumns / $btn->getCountButtons()),
                            null,
                            true
                        ),
                    );


                    continue;
                }

                $isPostBack = false;
                $postBackData = [];
                $actionBody = null;
                $silent = false;

                if ($btn instanceof ButtonDTO) {
                    $actionType = $btn->getType();
                    $text = $btn->getName();
                    $isPostBack = $btn->isPostBackType();
                    $postBackData = $btn->getPostBackData();

                    if ($btn->isPhoneRequestType()) {
                        $actionType= 'share-phone';
                        $actionBody = $text;
                    }

                    if ($btn->isUrlType()) {
                        $actionType= 'open-url';
                        $actionBody = $btn->getUrl();
                        $silent = true;
                    }
                } else if (is_array($btn)) {
                    $actionType = $btn['type'] ?? 'reply';
                    $text = $btn['title'];
                    $isPostBack = $actionType === 'postback';
                    $postBackData = $btn['url'];
                } else {
                    $actionType = null;
                    $text = $btn;
                    $actionBody = $text;
                }

                $button = (new \Viber\Api\Keyboard\Button())
                    ->setColumns((int)($maxButtonsInLine / $countButtons))
                    ->setActionType($actionType)
                    ->setActionBody($actionBody)
                    ->setText('<font color=#ffffff>' .$text. '</font>')
                    ->setBgColor('#7C69E9')
                    ->setSilent($silent)
                    ;

                if ($btn instanceof ButtonDTO) {
                    if ($btn->getImageUrl()) {
                        $button->setImage($btn->getImageUrl());
                    }
                }

                if ($availableRows) {
                    $button->setRows((int)($availableRows / $countButtonsRows));
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
        // it is supported by viber?
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
