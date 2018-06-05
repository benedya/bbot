<?php

namespace Bbot\Request;

interface Request
{
    function isText(): bool;

    function getData();

    function getPostback();

    static function fromArray(array $data): Request;

    function getPlatform(): string;
}
