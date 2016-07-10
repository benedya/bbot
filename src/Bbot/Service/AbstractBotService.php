<?php

namespace Bbot\Service;

use Bbot\Bridge\BotBridgeInterface;
use Bbot\Request\AbstractBotRequest;
use Bbot\CliLoggerTrait;

abstract class AbstractBotService
{
	use CliLoggerTrait;
	/** @var  BotBridgeInterface */
	protected $botBridge;
	/** @var  AbstractBotRequest */
	protected $botRequest;

	public function __construct(BotBridgeInterface $botBridge, AbstractBotRequest $botRequest)
	{
		$this->botBridge = $botBridge;
		$this->botRequest = $botRequest;
	}

	/**
	 * @return AbstractBotRequest
	 */
	public function getBotRequest()
	{
		return $this->botRequest;
	}

	/**
	 * @param $botBridge
	 * @return $this
	 */
	public function setBotBridge(BotBridgeInterface $botBridge)
	{
		$this->botBridge = $botBridge;

		return $this;
	}
}
