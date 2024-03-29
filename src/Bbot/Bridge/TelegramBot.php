<?php

namespace Bbot\Bridge;

use Bbot\DTO\ButtonDTO;
use TelegramBot\Api\Types\Inline\InlineKeyboardMarkup;
use TelegramBot\Api\Types\Inline\InputMessageContent\Text;
use TelegramBot\Api\Types\Inline\QueryResult\Article;
use TelegramBot\Api\Types\ReplyKeyboardHide;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;

class TelegramBot implements Bot
{
    /** @var int */
    private $chatId;
    /** @var \TelegramBot\Api\BotApi */
    private $bot;

    public function __construct(string $apiKey, int $chatId)
    {
        $this->chatId = $chatId;
        $this->bot = new \TelegramBot\Api\BotApi($apiKey);
    }

    public function sendText($text, array $options = [])
    {
        $splitIntoChunks = function (string $msg) {
            $result = [];
            $maxCharacters = 4050;

            if (mb_strlen($msg) > $maxCharacters) {
                $arr = explode('.', $msg);
                $arrContains = [];

                foreach ($arr as $line) {
                    if (mb_strlen(join('.', array_merge($arrContains, [$line]))) > $maxCharacters) {
                        $result[] = join('.', $arrContains);
                        $arrContains = [$line];
                    } else {
                        $arrContains[] = $line;
                    }
                }

                if ($arrContains) {
                    $result[] = join('.', $arrContains);
                }
            } else {
                $result = [$msg];
            }

            return $result;
        };

        $chunks = $splitIntoChunks($text);

        foreach ($chunks as $chunk) {
            $this->bot->sendMessage($this->chatId, $chunk, $options['parseMode'] ?? null);
        }
    }

    public function sendFlashMessage(string $text, array $options = [])
    {
        $callbackQueryId = $options['callbackQueryId'] ?? null;

        if (!$callbackQueryId) {
            throw new \InvalidArgumentException('Field "callbackQueryId" is required.');
        }

        $this->bot->answerCallbackQuery(
            $callbackQueryId,
            $text,
            $options['showAlert'] ?? false
        );
    }

    public function sendKeyboard($text, array $keyboard, array $options = [])
    {
        $preparedKeyboard = [];

        foreach ($keyboard as $item) {
            if ($item instanceof ButtonDTO) {
                if ($item->isPhoneRequestType()) {
                    $preparedKeyboard[] = [['text' => $item->getName(), 'request_contact' => true]];
                } elseif ($item->isLocationRequestType()) {
                        $preparedKeyboard[] = [['text' => $item->getName(), 'request_location' => true]];
                } else {
                    throw new \UnexpectedValueException(sprintf('Unsupported type.'));
                }
            } else {
                $preparedKeyboard[] = $item;
            }
        }

        $item = new ReplyKeyboardMarkup($preparedKeyboard, false, true);

        $this->bot->sendMessage(
            $this->chatId,
            $text,
            $options['parseMode'] ?? null,
            false,
            null,
            $item
        );
    }

    public function hideKeyboard($text, array $options = [])
    {
        $item = new ReplyKeyboardHide(true);

        $this->bot->sendMessage(
            $this->chatId,
            $text,
            $options['parseMode'] ?? null,
            false,
            null,
            $item
        );
    }

    public function sendImg($path, $caption = null, $isAnimation = false)
    {
        $tmpFile = false;
        // checks if there isset a local file using server variable
        if (!is_file($path) and isset($_SERVER['DOCUMENT_ROOT']) and $_SERVER['DOCUMENT_ROOT']) {
            $arr = parse_url($path);
            $serverPath = $_SERVER['DOCUMENT_ROOT'].$arr['path'];

            if (is_file($serverPath)) {
                $path = $serverPath;
            }
        }
        // if there is no file - download it and create a tmp file
        if (!is_file($path)) {
            if (getimagesize($path)) {
                $ext = pathinfo($path, PATHINFO_EXTENSION);
                $tmpFile = sys_get_temp_dir().'/'.md5($path).'.'.$ext;
                file_put_contents($tmpFile, file_get_contents($path));
                $path = $tmpFile;
            } else {
                throw new \Error('File "'.$path.'" not found.');
            }
        }

        $data = [];
        $buttons = [];
        // checks if there are buttons
        if (is_array($caption)) {
            $data = $caption;
            $caption = $data['caption'];
            $buttons = $data['buttons'] ?? [];
            // if buttons were not built yet - build it now
            if (is_array($buttons)) {
                $buttons = $this->buildButtons($buttons, $data['countInRow'] ?? 1);
            }
        }

        if ($isAnimation) {
            $this->bot->sendAnimation(
                $this->chatId,
                new \CURLFile($path),
                null,
                $caption,
                $data['replyToMessageId'] ?? false,
                ($buttons instanceof InlineKeyboardMarkup) ? $buttons : null,
                $data['disableNotification'] ?? false,
                $data['parseMode'] ?? null
            );
        } else {
            $this->bot->sendPhoto(
                $this->chatId,
                new \CURLFile($path),
                $caption,
                $data['replyToMessageId'] ?? false,
                ($buttons instanceof InlineKeyboardMarkup) ? $buttons : null,
                $data['disableNotification'] ?? false,
                $data['parseMode'] ?? null
            );
        }

        if ($tmpFile) {
            unlink($tmpFile);
        }
    }

    public function sendFile($path, $caption = null)
    {
        if (!is_file($path)) {
            throw new \Error(sprintf('File "%s" not found.', $path));
        }

        $this->bot->sendDocument($this->chatId, new \CURLFile($path), $caption);
    }

    public function sendButtons(array $data, array $options = [])
    {
        $editMessageId = $data['editMessageId'] ?? null;

        if ($editMessageId) {
            return $this->bot->editMessageReplyMarkup(
                $this->chatId,
                $editMessageId,
                $this->buildButtons($data['buttons'], $data['countInRow'] ?? 1)
            );
        } else {
            return $this->bot->sendMessage(
                $this->chatId,
                $data['caption'],
                $options['parseMode'] ?? null,
                false,
                $options['replyToMessageId'] ?? null,
                $this->buildButtons($data['buttons'], $data['countInRow'] ?? 1)
            );
        }
    }

    public function buildButtons(array $data, int $countInRow = 1)
    {
        if ($countInRow !== -1) {
            $data = array_chunk($data, $countInRow);
        }
        
        $buttons = [];

        foreach ($data as $line) {
            $listBtns = [];

            foreach ($line as $btn) {
                if ($btn instanceof ButtonDTO) {
                    $btnItem = $this->createButtonFromDTO($btn);
                } else {
                    $btnItem = $this->createButtonFromArray($btn);
                }

                $listBtns[] = $btnItem;
            }

            $buttons[] = $listBtns;
        }

        return new InlineKeyboardMarkup($buttons);
    }

    private function createButtonFromDTO(ButtonDTO $buttonDTO): array
    {
        $btnItem = [
            'text' => $buttonDTO->getName(),
        ];

        if ($buttonDTO->isPostBackType()) {
            $btnItem['callback_data'] = $buttonDTO->getPostBackData();
        }

        return $btnItem;
    }

    private function createButtonFromArray(array $btn): array
    {
        $btnItem = [
            'text' => $btn['title'],
        ];

        if (isset($btn['switch_inline_query_current_chat'])) {
            $btnItem['switch_inline_query_current_chat'] = $btn['switch_inline_query_current_chat'];
        } elseif (isset($btn['type'])) {
            $type = ('postback' === $btn['type']) ? 'callback_data' : 'url';
            $btnItem[$type] = $btn['url'];
        }

        if (isset($btn['options'])) {
            $btnItem = array_merge($btnItem, $btn['options']);
        }

        return $btnItem;
    }

    public function buildItemWithButtons(array $data, array $buttons = [])
    {
        // todo implement msg with img
        return [
            'text' => '<b>'.$data['title']."</b>\r\n".$data['subtitle'],
            'simpleText' => $data['title']."\r\n".$data['subtitle'],
            'image' => (isset($data['image']) ? $data['image'] : null),
            'parseMode' => 'HTML',
            'buttons' => $this->buildButtons($buttons),
        ];
    }

    public function sendListItems(array $items)
    {
        foreach ($items as $item) {
            if ($item['image']) {
                $this->sendImg($item['image'], [
                    'caption' => $item['simpleText'],
                    'buttons' => $item['buttons'],
                ]);
            } else {
                $this->bot->sendMessage(
                    $this->chatId,
                    $item['text'],
                    $item['parseMode'] ?? null,
                    false,
                    null,
                    $item['buttons']
                );
            }
        }
    }

    public function getTarget()
    {
        return $this->chatId;
    }

    public function deleteMessage(array $data)
    {
        if (!isset($data['chatId']) or !isset($data['messageId'])) {
            throw new \Error(sprintf('"chatId" and "messageId" are required.'));
        }

        $this->bot->deleteMessage($data['chatId'], $data['messageId']);
    }

    public function setBot(\TelegramBot\Api\BotApi $bot): void
    {
        $this->bot = $bot;
    }

    public function sendQueryResults(array $data, array $options = [])
    {
        return $this->bot->answerInlineQuery(
            $this->chatId,
            $this->buildQueryResults($data, $options['type'] ?? 'article'),
            $options['cacheTime'] ?? 300,
            $options['isPersonal'] ?? false,
            $options['nextOffset'] ?? '',
            $options['switchPmText'] ?? '',
            $options['switchPmParameter'] ?? '',
        );
    }

    private function buildQueryResults(array $items, string $type): array
    {
        switch ($type) {
            case 'article':
                return $this->buildArticleQueryResults($items);
                break;
            default:
                throw new \RuntimeException(sprintf('Unsupported type "%s"', $type));
        }
    }

    private function buildArticleQueryResults(array $items)
    {
        $result = [];

        foreach ($items as $item) {
            $buttons = $item['buttons'] ?? null;
            $inputMessageContent = null;

            if (isset($item['extendedDescription'])) {
                $inputMessageContent =  new Text(
                    $item['extendedDescription'],
                    $item['parseMode'] ?? null,
                    $item['disableWebPagePreview'] ?? null,
                );
            }

            $result[] = new Article(
                $item['id'],
                $item['title'],
                $item['description'],
                $item['thumbUrl'] ?? null,
                $item['thumbWidth'] ?? null,
                $item['thumbHeight'] ?? null,
                $inputMessageContent,
                $buttons ? $this->buildButtons($buttons, $item['countInRow'] ?? 1) : null,
            );
        }

        return $result;
    }
}
