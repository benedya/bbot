<?php

namespace Bbot\DTO;

interface CompositeButtonInterface
{
    public function addButton(CompositeButtonInterface $button): self;

    public function getButtons(): iterable;

    public function setButtons(iterable $buttons): self;

    public function setParentButton(CompositeButtonInterface $button): self ;

    public function getParentButton(): ?CompositeButtonInterface;
}
