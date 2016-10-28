## Bot-framework
The goal of this framework is to separate business logic from different platforms of a bot. 
This framework supports messenger and telegram platforms for now.
## Install
Via Composer

1.Modify your `composer.json` and add to section `repositories` my fork of `telegram-bot/api`.
```php
"repositories": [
    {
      "type": "vcs",
      "url": "https://github.com/benedya/Api"
    }
  ]
```

2.Then run
```php
$ composer require benedya/bbot
```
## How to use (in your bot's hook)
Messenger bot
```php
<?php
include "./vendor/autoload.php";
$data = json_decode(file_get_contents("php://input"), true);
$pageToken = '';
$botApp = (new \Bbot\AppBuilder\MessengerFactory($pageToken))->handle($data);
```
Telegram bot
```php
<?php
include "./vendor/autoload.php";

// Default handler for commands places in commands `Bbot\Handler\CommandsHandler`
// Default handler for simple text places in commands `Bbot\Handler\CommonHandler`
$data = json_decode(file_get_contents("php://input"), true);
$apiKey = '';
$botApp = (new \Bbot\AppBuilder\TelegramFactory($apiKey))->handle($data);
```
Fast example (it is valid for all supported bot-platforms)
```php
$botBridge = $botApp->getWelcomeService()->getBotBridge();
$botBridge->sendText("Hi! I'm bot ;)");
```
## Cli debug
There is opportunity to launch a bot-app under cli. You have to set to `$data` a bot request (telegram or messenger accordingly) and launch app as it shown before. As result you will see all steps handling a request. Also you can add you own debug information.

to be continued...
