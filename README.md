## Simple bot-framework

The goal of the framework is to make development telegram-bot easier. 
Here you will find ready-for-use architecture where you have to put only your logic and enjoy working telegram-bot. 

Using it you can:
- handling text-requests
- handling commands
- set an action for a button click (postback-request)
- set a certain action for text-request


Example handling telegram request
```php
<?php

include "./vendor/autoload.php";

$chatId = '<chat-id>';
$requestItem = ['<request-item>'];

(new \Bbot\Builder\TelegramFactory($apiKey, $chatId))
    ->buildKernel()
    ->handle(\Bbot\Request\TelegramRequest::fromArray($requestItem))
;
```

Plz see `example` folder for more use cases.
