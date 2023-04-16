<?php

namespace Common\Database;

class File
{
    public const IMAGE = 1;
    public const CODE = 2;

    private string $name;
    private string $location;
    private int $type;

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getLocation(): string
    {
        return $this->location;
    }

    public function setLocation(string $location): self
    {
        $this->location = $location;
        return $this;
    }

    public function getType(): int
    {
        return $this->type;
    }

    public function setType(int $type): self
    {
        $this->type = $type;
        return $this;
    }

    public static function fetchById(int $id): self
    {
        // TODO: Implement fetchById() method.
        return new self();
    }
}
