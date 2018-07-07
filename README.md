## Simple bot-framework

The goal of the framework is to separate business logic from different bot's platforms. 

Supported platforms: `telegram`

Using it you can:
- handling text-requests
- set an action for a button click
- ...


Example handling telegram requests
```php
<?php

include "./vendor/autoload.php";

$chatId = '<chat-id>';
$requestItem = [];

(new \Bbot\Builder\TelegramFactory($apiKey, $chatId))
    ->buildKernel()
    ->setTextController(\Bbot\Controller\TextController::class)
    ->handle(\Bbot\Request\TelegramRequest::fromArray($requestItem))
;
```

Plz see `example` folder for more use cases.