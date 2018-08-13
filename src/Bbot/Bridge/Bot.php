<?php

namespace Bbot\Bridge;

interface Bot
{
    public function sendText($text);

    public function sendKeyboard($text, array $keyboard);

    public function hideKeyboard($text);

    public function sendImg($path, $caption = null);

    public function sendFile($path, $caption);

    public function sendButtons(array $data);

    public function sendListItems(array $items);

    public function buildButtons(array $data);

    public function buildItemWithButtons(array $data, array $buttons);

    public function getTarget();
}
