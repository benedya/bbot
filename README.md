## bot-framework

## how to use?
```
$botRequestBuilder = new BotRequestProcessBuilder(1111);
		$botRequestBuilder
			->initMessengerBotRequest('', BotRequestProcessBuilder::createTextRequest('hi, my friend!'), [
				'textHandler' => 'welcome',
				'textAction' => 'index',
			])
			->createBotApp()
			->handleRequest($botRequestBuilder->getBotRequest());
```
## to be continued...
