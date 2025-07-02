<?php

namespace App\Domain\ValueObject;

final  readonly  class PriceAmount
{
    private function __construct(
        public float $value,
    )
    {
    }

    public static function create(float $value): self
    {
        $rounded = round($value, 2);

        if ($rounded < 0) {
            throw new \InvalidArgumentException('Price must be non-negative');
        }

        return new self($rounded);
    }

    public function getValue(): float
    {
        return $this->value;
    }

    public function equals(PriceAmount $priceAmount): bool
    {
        return $this->value === $priceAmount->value;
    }
}
