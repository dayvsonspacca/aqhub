<?php

declare(strict_types=1);

namespace App\ValueObjects;

use InvalidArgumentException;

final readonly class Level
{
    public const MIN = 1;
    public const MAX = 100;
    
    private function __construct(
        public int $value
    ) {
        $this->validate();
    }
    
    public static function from(int $value): self
    {
        return new self($value);
    }
    
    private function validate(): void
    {
        if ($this->value < self::MIN) {
            throw new InvalidArgumentException(
                sprintf('Level must be at least %d', self::MIN)
            );
        }
        
        if ($this->value > self::MAX) {
            throw new InvalidArgumentException(
                sprintf('Level cannot exceed %d', self::MAX)
            );
        }
    }
    
    public function isMin(): bool
    {
        return $this->value === self::MIN;
    }
    
    public function isMax(): bool
    {
        return $this->value === self::MAX;
    }
}