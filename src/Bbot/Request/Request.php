<?php

namespace Bbot\Request;

interface Request
{
    public function isText(): bool;

    public function get(string $key);

    public function getData();

    public function getPostback();

    public static function fromArray(array $data): Request;

    public function getPlatform(): string;
}
