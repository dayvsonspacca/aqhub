<?php

declare(strict_types=1);

namespace Tests\Unit\Casts\Faction;

use App\Casts\Faction\FactionNameCast;
use AqwSocketClient\Objects\Names\FactionName;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\Test;
use Psl\Type\Exception\AssertException;
use Tests\TestCase;

final class FactionNameCastTest extends TestCase
{
    private FactionNameCast $cast;

    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast = new FactionNameCast;
        $this->model = new class extends Model
        {
            protected $table = 'factions';
        };
    }

    #[Test]
    public function it_returns_faction_name_object_from_database_value(): void
    {
        $name = $this->cast->get($this->model, 'name', 'Doomwood', []);

        $this->assertInstanceOf(FactionName::class, $name);
        $this->assertSame('Doomwood', $name->value);
    }

    #[Test]
    public function it_returns_null_when_value_is_null(): void
    {
        $name = $this->cast->get($this->model, 'name', null, []);

        $this->assertNull($name);
    }

    #[Test]
    public function it_accepts_faction_name_object(): void
    {
        $nameObject = new FactionName('Doomwood');

        $result = $this->cast->set($this->model, 'name', $nameObject, []);

        $this->assertSame('Doomwood', $result);
    }

    #[Test]
    public function it_validates_string_value(): void
    {
        $this->expectException(AssertException::class);

        $this->cast->set($this->model, 'name', '', []);
    }
}
