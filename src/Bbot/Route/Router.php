<?php

namespace Bbot\Route;

use Bbot\Request\Request;
use Bbot\Storage\Storage;

class Router
{
    /** @var string */
    protected $delimiter = '::';
    /** @var Storage */
    protected $storage;

    public function __construct(Storage $storage)
    {
        $this->storage = $storage;
    }

    public function toPostback(string $controller, string $action, array $data = [])
    {
        $query = $this->encodeQuery($controller, $action, $data);
        $key = md5($query);

        $this->storage->set($key, $query);

        return $key;
    }

    public function fromPostback(Request $request)
    {
        $result = [];

        if ($key = $request->getPostback()) {
            $result = $this->decodeQuery($key);
        }

        return $result;
    }

    protected function encodeQuery(string $controller, string $action, array $data = []): string
    {
        return $controller.$this->delimiter.$action.($data ? '?'.http_build_query($data) : '');
    }

    protected function decodeQuery(string $key): array
    {
        if (!$query = $this->storage->get($key)) {
            throw new  \Error(sprintf('Data by key "%s" not found in storage.', $key));
        }

        $data = explode('?', $query);
        $parameters = [];

        if (isset($data['1'])) {
            parse_str($data['1'], $parameters);
        }

        $handlerData = explode($this->delimiter, $data['0']);

        return [
            'class' => $handlerData['0'] ?? null,
            'method' => $handlerData['1'] ?? null,
            'parameters' => $parameters,
        ];
    }
}
