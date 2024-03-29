<?php

namespace Bbot\Request;

interface Request
{
    public function isText(): bool;

    public function getLocation(): ?array;

    public function isCommand(): bool;

    public function isStartMessage(): bool;

    public function isInlineQuery(): bool;

    public function get(string $key);

    public function getTextMessage(): string;

    public function getData();

    public function getPostback();

    public function getChatId(): string;

    public function getMessageId(): string;

    public static function fromArray(array $data): Request;

    public function getPlatform(): string;

    public function setParameters(array $parameters);
}
