<?php

declare(strict_types=1);

namespace Tests\Unit\Casts;

use App\Casts\HealthCast;
use AqwSocketClient\Objects\Health;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

final class HealthCastTest extends TestCase
{
    private HealthCast $cast;

    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast = new HealthCast;
        $this->model = new class extends Model
        {
            protected $table = 'monsters';
        };
    }

    #[Test]
    public function it_returns_health_object_from_database_value(): void
    {
        $health = $this->cast->get($this->model, 'health', 100, []);

        $this->assertInstanceOf(Health::class, $health);
        $this->assertSame(100, $health->value);
    }

    #[Test]
    public function it_returns_null_when_value_is_null(): void
    {
        $health = $this->cast->get($this->model, 'health', null, []);

        $this->assertNull($health);
    }

    #[Test]
    public function it_accepts_health_object(): void
    {
        $healthObject = new Health(100);

        $result = $this->cast->set($this->model, 'health', $healthObject, []);

        $this->assertSame(100, $result);
    }

    #[Test]
    public function it_validates_health(): void
    {
        $this->expectException(InvalidArgumentException::class);

        $this->cast->set($this->model, 'health', -2, []);
    }
}
