<?php

declare(strict_types=1);

namespace Tests\Unit\ValueObjects;

use App\ValueObjects\Level;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LevelTest extends TestCase
{
    #[Test]
    public function it_create_level_with_valid_value(): void
    {
        $level = Level::from(50);

        $this->assertSame(50, $level->value);
    }

    #[Test]
    public function it_can_create_level_with_min_value(): void
    {
        $level = Level::from(Level::MIN);

        $this->assertSame(1, $level->value);
        $this->assertTrue($level->isMin());
    }

    #[Test]
    public function it_can_create_level_with_max_value(): void
    {
        $level = Level::from(Level::MAX);

        $this->assertSame(100, $level->value);
        $this->assertTrue($level->isMax());
    }

    #[Test]
    public function it_throws_exception_when_level_is_below_min(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Level must be at least 1');

        Level::from(0);
    }

    #[Test]
    public function it_throws_exception_when_level_is_negative(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Level must be at least 1');

        Level::from(-5);
    }

    #[Test]
    public function it_throws_exception_when_level_exceeds_max(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Level cannot exceed 100');

        Level::from(101);
    }

    #[Test]
    public function it_returns_true_only_for_min_value(): void
    {
        $this->assertTrue(Level::from(1)->isMin());
        $this->assertFalse(Level::from(2)->isMin());
        $this->assertFalse(Level::from(50)->isMin());
        $this->assertFalse(Level::from(100)->isMin());
    }

    #[Test]
    public function it_returns_true_only_for_max_value(): void
    {
        $this->assertTrue(Level::from(100)->isMax());
        $this->assertFalse(Level::from(99)->isMax());
        $this->assertFalse(Level::from(50)->isMax());
        $this->assertFalse(Level::from(1)->isMax());
    }
}
