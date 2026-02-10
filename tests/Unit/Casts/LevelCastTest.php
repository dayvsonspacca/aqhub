<?php

declare(strict_types=1);

namespace Tests\Unit\Casts;

use App\Casts\LevelCast;
use App\ValueObjects\Level;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class LevelCastTest extends TestCase
{
    private LevelCast $cast;

    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast = new LevelCast;
        $this->model = new class extends Model
        {
            protected $table = 'enemies';
        };
    }

    #[Test]
    public function it_returns_level_object_from_database_value(): void
    {
        $level = $this->cast->get($this->model, 'level', 50, []);

        $this->assertInstanceOf(Level::class, $level);
        $this->assertSame(50, $level->value);
    }

    #[Test]
    public function it_returns_null_when_value_is_null(): void
    {
        $level = $this->cast->get($this->model, 'level', null, []);

        $this->assertNull($level);
    }

    #[Test]
    public function it_converts_string_to_level(): void
    {
        $level = $this->cast->get($this->model, 'level', '75', []);

        $this->assertInstanceOf(Level::class, $level);
        $this->assertSame(75, $level->value);
    }

    #[Test]
    public function it_accepts_level_object(): void
    {
        $levelObject = Level::from(80);

        $result = $this->cast->set($this->model, 'level', $levelObject, []);

        $this->assertSame(80, $result);
    }

    #[Test]
    public function it_accepts_integer(): void
    {
        $result = $this->cast->set($this->model, 'level', 60, []);

        $this->assertSame(60, $result);
    }

    #[Test]
    public function it_validates_integer_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Level cannot exceed 100');

        $this->cast->set($this->model, 'level', 150, []);
    }

    #[Test]
    public function it_throws_exception_for_invalid_low_value(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Level must be at least 1');

        $this->cast->set($this->model, 'level', 0, []);
    }

    #[Test]
    public function it_preserves_value_in_roundtrip_with_level_object(): void
    {
        $originalLevel = Level::from(42);
        $dbValue = $this->cast->set($this->model, 'level', $originalLevel, []);

        $retrievedLevel = $this->cast->get($this->model, 'level', $dbValue, []);

        $this->assertInstanceOf(Level::class, $retrievedLevel);
        $this->assertSame(42, $retrievedLevel->value);
    }

    #[Test]
    public function it_preserves_value_in_roundtrip_with_integer(): void
    {
        $dbValue = $this->cast->set($this->model, 'level', 33, []);

        $retrievedLevel = $this->cast->get($this->model, 'level', $dbValue, []);

        $this->assertInstanceOf(Level::class, $retrievedLevel);
        $this->assertSame(33, $retrievedLevel->value);
    }
}
