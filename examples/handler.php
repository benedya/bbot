<?php

require '../vendor/autoload.php';

$apiKey = getenv('TELEGRAM_API_KEY');

$bot = new \TelegramBot\Api\BotApi($apiKey);

$offset = 0;

function getChatId(array $message)
{
    $chatId = null;

    foreach ($message as $k => $v) {
        if ('chat' == $k) {
            $chatId = $v['id'];
        } elseif (is_array($v)) {
            $chatId = getChatId($v);
        }
    }

    return $chatId;
}

while (true) {
    $data = $bot->call('getUpdates', [
        'offset' => $offset,
        'limit' => 100,
        'timeout' => 3,
    ]);

    if ($data) {
        foreach ($data as $item) {
            if ($offset == $item['update_id']) {
                continue;
            }

            $offset = $item['update_id'];
            $chatId = getChatId($item);

            (new \Bbot\Builder\TelegramFactory($apiKey, $chatId))
                ->buildKernel()
                ->setTextController(\Bbot\Controller\TextController::class)
                ->handle(\Bbot\Request\TelegramRequest::fromArray($item))
            ;
        }
    }
    echo '.'.$offset;
    sleep(1);
}
