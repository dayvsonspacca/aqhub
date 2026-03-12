<?php

declare(strict_types=1);

namespace Tests\Unit\Casts\Monster;

use App\Casts\Monster\MonsterLevelCast;
use AqwSocketClient\Objects\Levels\MonsterLevel;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\Test;
use Psl\Exception\InvariantViolationException;
use Psl\Type\Exception\AssertException;
use Tests\TestCase;

final class MonsterLevelCastTest extends TestCase
{
    private MonsterLevelCast $cast;

    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast = new MonsterLevelCast;
        $this->model = new class extends Model
        {
            protected $table = 'monsters';
        };
    }

    #[Test]
    public function it_returns_level_object_from_database_value(): void
    {
        $level = $this->cast->get($this->model, 'level', 50, []);

        $this->assertInstanceOf(MonsterLevel::class, $level);
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

        $this->assertInstanceOf(MonsterLevel::class, $level);
        $this->assertSame(75, $level->value);
    }

    #[Test]
    public function it_accepts_level_object(): void
    {
        $levelObject = new MonsterLevel(80);

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
        $this->expectException(InvariantViolationException::class);

        $this->cast->set($this->model, 'level', 256, []);
    }

    #[Test]
    public function it_throws_exception_for_invalid_low_value(): void
    {
        $this->expectException(AssertException::class);

        $this->cast->set($this->model, 'level', 0, []);
    }

    #[Test]
    public function it_preserves_value_in_roundtrip_with_level_object(): void
    {
        $originalLevel = new MonsterLevel(42);
        $dbValue = $this->cast->set($this->model, 'level', $originalLevel, []);

        $retrievedLevel = $this->cast->get($this->model, 'level', $dbValue, []);

        $this->assertInstanceOf(MonsterLevel::class, $retrievedLevel);
        $this->assertSame(42, $retrievedLevel->value);
    }

    #[Test]
    public function it_preserves_value_in_roundtrip_with_integer(): void
    {
        $dbValue = $this->cast->set($this->model, 'level', 33, []);

        $retrievedLevel = $this->cast->get($this->model, 'level', $dbValue, []);

        $this->assertInstanceOf(MonsterLevel::class, $retrievedLevel);
        $this->assertSame(33, $retrievedLevel->value);
    }
}
