<?php

namespace Bbot\DTO;

class ButtonDTO implements CompositeButtonInterface, \JsonSerializable
{
    private const TYPE_POST_BACK = 'TYPE_POST_BACK';

    private const TYPE_URL = 'TYPE_URL';

    private const TYPE_PHONE_REQUEST = 'TYPE_PHONE_REQUEST';

    private const TYPES = [self::TYPE_POST_BACK, self::TYPE_URL, self::TYPE_PHONE_REQUEST];

    private string $name;

    private ?string $type = null;

    private ?string $postBackData = null;

    private ?string $imageUrl = null;

    private array $buttons = [];

    private array $parameters = [];

    private ?CompositeButtonInterface $parentButton = null;

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    /**
     * @inheritDoc
     */
    public function jsonSerialize(): array
    {
        return array_filter([
            'name' => $this->name,
            'type' => $this->type,
            'postBackData' => $this->postBackData,
            'imageUrl' => $this->imageUrl,
            'buttons' => $this->buttons,
            'parameters' => $this->parameters,
        ], fn ($item) => !empty($item));
    }

    public static function fromArray(array $data): self
    {
        $self = new self('');

        foreach ($data as $field => $value) {
            $setter = 'set' . ucfirst($field);

            if (!\method_exists($self, $setter)) {
                throw new \BadMethodCallException(sprintf(
                    'Method "%s" not exists.',
                    get_class($self) . ':' . $setter
                ));
            }

            $self->{$setter}($value);
        }

        return $self;
    }

    public function setParentButton(CompositeButtonInterface $button): self
    {
        $this->parentButton = $button;

        return $this;
    }

    public function getParentButton(): ?CompositeButtonInterface
    {
        return $this->parentButton;
    }

    public function addButton(CompositeButtonInterface $button): self
    {
        $button->setParentButton($this);

        $this->buttons[] = $button;

        return $this;
    }

    public function getButtons(): iterable
    {
        return $this->buttons;
    }

    public function setButtons(iterable $buttons): self
    {
        foreach ($buttons as $button) {
            $this->addButton($button);
        }

        return $this;
    }

    public function getCountButtons(): int
    {
        return count($this->buttons);
    }

    public function hasButtons(): bool
    {
        return $this->getCountButtons() > 0;
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

    public function setImageUrl(?string $imageUrl): ButtonDTO
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
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

    public function addParameter(string $key, string $value): self
    {
        $this->parameters[$key] = $value;

        return $this;
    }

    public function getParameter(string $key): ?string
    {
        return $this->parameters[$key] ?? null;
    }
}
