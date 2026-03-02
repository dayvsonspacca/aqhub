<?php

declare(strict_types=1);

namespace Tests\Unit\Casts\Monster;

use App\Casts\Monster\MonsterNameCast;
use AqwSocketClient\Objects\Names\MonsterName;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class MonsterNameCastTest extends TestCase
{
    private MonsterNameCast $cast;

    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast = new MonsterNameCast;
        $this->model = new class extends Model
        {
            protected $table = 'monsters';
        };
    }

    #[Test]
    public function it_returns_name_object_from_database_value(): void
    {
        $name = $this->cast->get($this->model, 'name', 'Red Dragon', []);

        $this->assertInstanceOf(MonsterName::class, $name);
        $this->assertSame('Red Dragon', $name->value);
    }

    #[Test]
    public function it_returns_null_when_value_is_null(): void
    {
        $name = $this->cast->get($this->model, 'name', null, []);

        $this->assertNull($name);
    }

    #[Test]
    public function it_accepts_name_object(): void
    {
        $nameObject = new MonsterName('Red Dragon');

        $result = $this->cast->set($this->model, 'name', $nameObject, []);

        $this->assertSame('Red Dragon', $result);
    }

    #[Test]
    public function it_validates_string_value(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cast->set($this->model, 'name', '', []);
    }
}
