<?php

require "../vendor/autoload.php";

$apiKey = getenv('TELEGRAM_API_KEY');

$bot = new \TelegramBot\Api\BotApi($apiKey);

$offset = 0;

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

            (new \Bbot\Builder\TelegramFactory($apiKey, $item['message']['chat']['id']))
                ->buildKernel()
                ->setTextController(\Bbot\Controller\TextController::class)
                ->handle(\Bbot\Request\TelegramRequest::fromArray($item))
            ;
        }
    }
    echo '.' . $offset;
    sleep(1);
}