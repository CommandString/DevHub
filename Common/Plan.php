<?php

namespace Common;

class Plan
{
    private int $price;
    private string $name;
    private string $description;
    private array $perks;
    private string $color;
    private int $id;

    public function __construct(
        int $id,
        int $price,
        string $name,
        string $description,
        string $color,
        array $perks = []
    ) {
        $this->id = $id;
        $this->price = $price;
        $this->name = $name;
        $this->description = $description;
        $this->perks = $perks;
        $this->color = $color;
    }

    public function getPrice(): int
    {
        return $this->price;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getColor(): string
    {
        return $this->color;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2);
    }

    public function setPrice(int $price): void
    {
        $this->price = $price;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function setColor(string $color): void
    {
        $this->color = $color;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }

    public function getPerks(): array
    {
        return $this->perks;
    }

    public function setPerks(array $perks): self
    {
        $this->perks = $perks;
        return $this;
    }

    public function addPerk(string $perk): self
    {
        $this->perks[] = $perk;
        return $this;
    }
}
