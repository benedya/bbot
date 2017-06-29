<?php

namespace Bbot\Bridge;

use Psr\Log\LoggerInterface;

interface BotBridgeInterface
{
    function setLogger(LoggerInterface $logger);
    function sendText($text, $recipient = null);
    function sendKeyboard($text, array $keyboard, $recipient = null);
    function hideKeyboard($text, $recipient = null);
    function sendImg($path, $caption = null, $recipient = null);
    function sendButtons(array $data, $recipient = null);
    function sendListItems(array $items, $recipient = null);
    function buildButtons(array $data);
    function buildItemWithButtons(array $data, array $buttons);
    function getUserProfile();
    function getUserId();
}
