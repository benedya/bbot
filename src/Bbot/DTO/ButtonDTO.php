<?php

namespace Bbot\DTO;

class ButtonDTO
{
    private const TYPE_POST_BACK = 'TYPE_POST_BACK';

    private const TYPE_URL = 'TYPE_URL';

    private const TYPE_PHONE_REQUEST = 'TYPE_PHONE_REQUEST';

    private const TYPES = [self::TYPE_POST_BACK, self::TYPE_URL, self::TYPE_PHONE_REQUEST];

    private string $name;

    private ?string $type = null;

    private ?string $postBackData = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public static function createPostBack(string $name, string $postBackData): self
    {
        $self = new static($name);

        $self->setType(self::TYPE_POST_BACK);
        $self->setPostBackData($postBackData);

        return $self;
    }

    public static function createPhoneRequest(string $name): self
    {
        $self = new static($name);

        $self->setType(self::TYPE_PHONE_REQUEST);

        return $self;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setType(?string $type): self
    {
        $this->checkType($type);

        $this->type = $type;

        return $this;
    }

    private function checkType(?string $type): void
    {
        if ($type && !in_array($type, self::TYPES)) {
            throw new \InvalidArgumentException(sprintf(
                'Type "%s" is unknown. Allowed types "%s".',
                $type,
                implode(', ', self::TYPES)
            ));
        }
    }

    public function setPostBackData(?string $postBackData): self
    {
        $this->postBackData = $postBackData;

        return $this;
    }

    public function getPostBackData(): ?string
    {
        return $this->postBackData;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function isPostBackType(): bool
    {
        return $this->type === self::TYPE_POST_BACK;
    }

    public function isPhoneRequestType(): bool
    {
        return $this->type === self::TYPE_PHONE_REQUEST;
    }
}
