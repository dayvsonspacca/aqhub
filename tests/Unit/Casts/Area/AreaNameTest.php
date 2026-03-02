<?php

declare(strict_types=1);

namespace Tests\Unit\Casts\Area;

use App\Casts\Area\AreaNameCast;
use AqwSocketClient\Objects\Names\AreaName;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class AreaNameTest extends TestCase
{
    private AreaNameCast $cast;

    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast = new AreaNameCast;
        $this->model = new class extends Model
        {
            protected $table = 'maps';
        };
    }

    #[Test]
    public function it_returns_name_object_from_database_value(): void
    {
        $name = $this->cast->get($this->model, 'name', 'battleon', []);

        $this->assertInstanceOf(AreaName::class, $name);
        $this->assertSame('battleon', $name->value);
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
        $nameObject = new AreaName('battleon');

        $result = $this->cast->set($this->model, 'name', $nameObject, []);

        $this->assertSame('battleon', $result);
    }

    #[Test]
    public function it_validates_string_value(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cast->set($this->model, 'name', '', []);
    }
}
