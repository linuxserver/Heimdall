<?php

namespace Facade\IgnitionContracts;

class BaseSolution implements Solution
{
    protected $title;
    protected $description;
    protected $links = [];

    public static function create(string $title)
    {
        return new static($title);
    }

    public function __construct(string $title)
    {
        $this->title = $title;
    }

    public function getSolutionTitle(): string
    {
        return $this->title;
    }

    public function setSolutionTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getSolutionDescription(): string
    {
        return $this->description;
    }

    public function setSolutionDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getDocumentationLinks(): array
    {
        return $this->links;
    }

    public function setDocumentationLinks(array $links): self
    {
        $this->links = $links;

        return $this;
    }
}
