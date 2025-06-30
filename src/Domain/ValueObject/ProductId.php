<?php

namespace App\Domain\ValueObject;

final readonly class ProductId
{
    private function __construct(
        private  string $id
    )
    {
    }
    public static function create(string $id): self
    {
        return new self($id);
    }
    public function __toString(): string
    {
        return $this->id;
    }

    public function equals(ProductId $id): bool
    {
        return $this->id === $id->id;
    }
}
