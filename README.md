## bot-framework
The goal of this framework is to separate business logic from different platforms of a bot. 
This framework supports messenger and telegram platforms for now.
## how to use
```php
# messenger bot
$data = json_decode(file_get_contents("php://input"), true);
$message = $data['entry'][0]['messaging'];
$botRequest = (new MessengerRequest($message))->processRequestData();
if($botRequest->canHandle()) {
	$userId = $message['sender']['id'];
	$pageToken = 'bot_messenger_page_token';
	$botBridge = new MessengerBotBridge($pageToken, $userId);
	$botApp = new BotApp($botBridge, $botRequest, new CliLogger());
	$botApp->handleRequest($botRequest);
}
```

```php
# telegram bot
// Default handler for commands places in commands `Bbot\Handler\CommandsHandler`
// Default handler for simple text places in commands `Bbot\Handler\CommonHandler`
$data = json_decode(file_get_contents("php://input"), true);
$request = new TelegramRequest($data);
$botRequest = $request->processRequestData();
if($botRequest->canHandle()) {
	$botBridge = new TelegramBotBridge($apiKey, $botRequest->getChatData()['id']);
	$botApp = new BotApp($botBridge, $botRequest, new CliLogger());
	$botApp->handleRequest($botRequest);
}
```

## cli debug
There is opportunity to launch a bot-app under cli. You have to set to `$data` a bot request (telegram or messenger accordingly) and launch app as it shown before. As result you will see all steps handling a request. Also you can add you own debug information.

to be continued...
