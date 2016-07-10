<?php

namespace BBot\Bridge;

interface BotBridgeInterface
{
    function sendText($recipient, $text);
    function sendButtons($recipient, array $data);
    function sendListItems($recipient, array $items);
    function buildButtons(array $data);
    function buildItemWithButtons(array $data, array $buttons);
    function getUserProfile();
    function getUserId();
}
