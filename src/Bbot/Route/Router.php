<?php

namespace Bbot\Route;

use Bbot\Request\Request;
use Bbot\Route\Storage\RouterStorage;

class Router
{
    /** @var string */
    protected $delimiter = '::';
    /** @var RouterStorage */
    protected $routerStorage;

    public function __construct(RouterStorage $routerStorage)
    {
        $this->routerStorage = $routerStorage;
    }

    public function toPostback(string $controller, string $action, array $data = [])
    {
        $query = $controller.$this->delimiter.$action.($data ? '?'.http_build_query($data) : '');
        $key = md5($query);

        $this->routerStorage->set($key, $query);

        return $key;
    }

    public function fromPostback(Request $request)
    {
        $result = [];

        if ($key = $request->getPostback()) {
            if (!$postback = $this->routerStorage->get($key)) {
                throw new  \Exception(sprintf('Data by key "%s" not found in storage.', $key));
            }

            $postback = explode('?', $postback);
            $postbackParameters = [];

            if (isset($postback['1'])) {
                parse_str($postback['1'], $postbackParameters);
            }

            $postback = explode($this->delimiter, $postback['0']);

            return [
                'class' => $postback['0'] ?? null,
                'method' => $postback['1'] ?? null,
                'parameters' => $postbackParameters,
            ];
        }

        return $result;
    }
}
