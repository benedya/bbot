<?php

namespace Bbot\DTO;

class ItemWithButtonsDTO
{
    private string $name;

    private array $parameters = [];

    private ?string $imageUrl = null;

    /** @var ButtonDTO[] */
    private array $buttons = [];

    public function __construct(string $name)
    {
        $this->name = $name;
    }

    public function toButtons(): array
    {
        $button = new ButtonDTO($this->name);

    }

    public function addButton(ButtonDTO $buttonDTO): self
    {
        $this->buttons[] = $buttonDTO;

        return $this;
    }

    public function getButtons(): array
    {
        return $this->buttons;
    }

    public function setButtons(array $buttons): self
    {
        $this->buttons = $buttons;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setImageUrl(?string $imageUrl): self
    {
        $this->imageUrl = $imageUrl;

        return $this;
    }

    public function getImageUrl(): ?string
    {
        return $this->imageUrl;
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
