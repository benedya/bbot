<?php

require '../vendor/autoload.php';

$apiKey = getenv('TELEGRAM_API_KEY');

$bot = new \TelegramBot\Api\BotApi($apiKey);

$offset = 0;

function getChatId(array $message)
{
    $chatId = null;

    foreach ($message as $k => $v) {
        if ('chat' === $k) {
            $chatId = $v['id'];
            break;
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
                ->buildKernel([
                    new class() implements \Pimple\ServiceProviderInterface {
                        public function register(\Pimple\Container $pimple)
                        {
                            $fileStorage = new \Bbot\Route\Storage\FileStorage(
                                new \SplFileObject('../.data/storage.json', 'a+')
                            );

                            $pimple['router'] = function () use ($fileStorage) {
                                return new \Bbot\Route\Router($fileStorage);
                            };
//
//                          Overriding default command controller
//
//                          $pimple['command_controller'] = function () {
//                               return new \Bbot\Controller\CommandController();
//                          };
                        }
                    },
                ])
                ->handle(\Bbot\Request\TelegramRequest::fromArray($item))
            ;
        }
    }
    echo '.'.$offset;
    sleep(1);
}
