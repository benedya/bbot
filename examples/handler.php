<?php

require "../vendor/autoload.php";

$bot = new \TelegramBot\Api\BotApi(getenv('telegram_api_key'));

$offset = 0;

while (true) {
    $data = $bot->call('getUpdates', [
        'offset' => $offset,
        'limit' => 100,
        'timeout' => 3,
    ]);

    if ($data) {
        foreach ($data as $item) {
            $message = $item['message'];
            
            if ($offset == $item['update_id']) {
                continue;
            }

            $offset = $item['update_id'];

            (new \Bbot\Builder\TelegramFactory(getenv('telegram_api_key'), $message['chat']['id']))
                ->buildKernel()
                ->handle(\Bbot\Request\TelegramRequest::fromArray($message))
            ;
        }
    }
}