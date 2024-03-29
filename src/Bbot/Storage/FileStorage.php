<?php

namespace Bbot\Storage;

class FileStorage implements Storage
{
    /** @var \SplFileObject */
    protected $fileObject;

    public function __construct(\SplFileObject $fileObject)
    {
        $this->fileObject = $fileObject;
    }

    public function set(string $key, string $value)
    {
        $contains = $this->getContainsAsArray();
        $contains[$key] = $value;

        $this->fileObject->ftruncate(0);
        $this->fileObject->fwrite(json_encode($contains));
    }

    public function get(string $key)
    {
        $contains = $this->getContainsAsArray();

        return $contains[$key] ?? null;
    }

    public function remove(string $key)
    {
        $contains = $this->getContainsAsArray();

        if (isset($contains[$key])) {
            unset($contains[$key]);
        }

        $this->fileObject->ftruncate(0);
        $this->fileObject->fwrite(json_encode($contains));
    }

    protected function getContainsAsArray(): array
    {
        $fileObject = $this->fileObject;

        $fileObject->rewind();

        $bytes = $fileObject->fstat()['size'] ?? null;

        if (!$bytes) {
            $bytes = 100;
        }

        $contains = $fileObject->fread($bytes);

        if ($contains) {
            $contains = json_decode($contains, true);
        } else {
            $contains = [];
        }

        return $contains;
    }
}
