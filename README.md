## bot-framework
The goal of this framework is to separate business logic from different platforms of the bot. 
This framework supports (architecture and main functions) of messenger and telegram platforms for now.
## how to use
```
# messenger bot
$data = json_decode(file_get_contents("php://input"), true);
$message = $data['entry'][0]['messaging'];
$botRequest = (new MessengerRequest($message))->processRequestData();
if($botRequest->canHandle()) {
	$userId = $message['sender']['id'];
	$pageToken = 'bot_messenger_page_token';
	$botBridge = new MessengerBotBridge($pageToken, $userId);
	$botApp = new BotApp($botBridge, $botRequest);
	$botApp->handleRequest($botRequest);
}

# telegram bot
// Default handler for commands places in commands `Bbot\Handler\CommandsHandler`
// Default handler for simple text places in commands `Bbot\Handler\CommonHandler`
$data = json_decode(file_get_contents("php://input"), true);
$request = new TelegramRequest($data);
$botRequest = $request->processRequestData();
if($botRequest->canHandle()) {
	$botBridge = new TelegramBotBridge($apiKey, $botRequest->getChatData()['id']);
	$botApp = new BotApp($botBridge, $botRequest);
	$botApp->handleRequest($botRequest);
}
```
to be continued...
