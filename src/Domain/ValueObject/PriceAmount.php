<?php

namespace App\Domain\ValueObject;

final  readonly  class PriceAmount
{
    private function __construct(
        public float $amount,
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

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function equals(PriceAmount $priceAmount): bool
    {
        return $this->amount === $priceAmount->amount;
    }

}
