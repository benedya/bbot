<?php

namespace Bbot\Request;

interface Request
{
    public function getPlatform();
    public function isText();
    public function getData();
    public function getPostback();
}
