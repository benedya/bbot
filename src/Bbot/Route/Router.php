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

    public function fromPostback(Request $request): array
    {
        $result = [];

        if ($key = $request->getPostback()) {
            if (!$query = $this->storage->get($key)) {
                throw new  \Error(sprintf('Data by key "%s" not found in storage.', $key));
            }

            $result = $this->decodeQuery($query);
        }

        return $result;
    }

    public function setTxtHandler(string $controller, string $action)
    {
        $query = $query = $this->encodeQuery($controller, $action);

        $this->storage->set('txt_msg_handler', $query);
    }

    public function getTxtHandler(): array
    {
        $key = 'txt_msg_handler';
        $result = [];

        if ($query = $this->storage->get($key)) {
            $this->storage->remove($key);

            $result = $this->decodeQuery($query);
        }

        return $result;
    }

    protected function encodeQuery(string $controller, string $action, array $data = []): string
    {
        return $controller.$this->delimiter.$action.($data ? '?'.http_build_query($data) : '');
    }

    protected function decodeQuery(string $query): array
    {
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
