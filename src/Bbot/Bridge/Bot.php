<?php

namespace Bbot\Bridge;

interface Bot
{
    public const VIEW_TYPE_CAROUSEL = 'VIEW_TYPE_CAROUSEL';

    public function sendText($text, array $options = []);

    public function sendFlashMessage(string $text, array $options = []);

    public function sendKeyboard($text, array $keyboard, array $options = []);

    public function hideKeyboard($text, array $options = []);

    public function sendImg($path, $caption = null, $isAnimation = false);

    public function sendFile($path, $caption);

    public function sendButtons(array $data, array $options = []);

    public function sendListItems(array $items);

    public function buildButtons(array $data, int $countInRow = 1);

    public function buildItemWithButtons(array $data, array $buttons);

    public function getTarget();

    public function deleteMessage(array $data);

    public function sendQueryResults(array $data, array $options = []);
}
