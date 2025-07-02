<?php

namespace App\Domain\ValueObject;

final readonly class ProductId
{
    private function __construct(
        private  int  $value
    )
    {
    }
    public static function create(int $id): self
    {
        if ($id <= 0) {
            throw new \InvalidArgumentException('Product ID must be a positive integer');
        }
        return new self($id);
    }
    public function getValue(): int
    {
        return $this->value;
    }

    public function equals(ProductId $id): bool
    {
        return $this->value === $id->value;
    }
}
