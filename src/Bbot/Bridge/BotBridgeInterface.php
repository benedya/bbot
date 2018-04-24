<?php

namespace Bbot\Bridge;

interface BotBridgeInterface
{
    public function sendText($text, $recipient = null);

    public function sendKeyboard($text, array $keyboard, $recipient = null);

    public function hideKeyboard($text, $recipient = null);

    public function sendImg($path, $caption = null, $recipient = null);

    public function sendButtons(array $data, $recipient = null);

    public function sendListItems(array $items, $recipient = null);

    public function buildButtons(array $data);

    public function buildItemWithButtons(array $data, array $buttons);

    public function getUserProfile();

    public function getUserId();
}
