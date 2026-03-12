<?php

declare(strict_types=1);

namespace Tests\Unit\Casts\Quest;

use App\Casts\Quest\QuestNameCast;
use AqwSocketClient\Objects\Names\QuestName;
use Illuminate\Database\Eloquent\Model;
use PHPUnit\Framework\Attributes\Test;
use Psl\Type\Exception\AssertException;
use Tests\TestCase;

final class QuestNameCastTest extends TestCase
{
    private QuestNameCast $cast;

    private Model $model;

    protected function setUp(): void
    {
        parent::setUp();

        $this->cast = new QuestNameCast;
        $this->model = new class extends Model
        {
            protected $table = 'quests';
        };
    }

    #[Test]
    public function it_returns_quest_name_object_from_database_value(): void
    {
        $name = $this->cast->get($this->model, 'name', 'The Dragon Hunt', []);

        $this->assertInstanceOf(QuestName::class, $name);
        $this->assertSame('The Dragon Hunt', $name->value);
    }

    #[Test]
    public function it_returns_null_when_value_is_null(): void
    {
        $name = $this->cast->get($this->model, 'name', null, []);

        $this->assertNull($name);
    }

    #[Test]
    public function it_accepts_quest_name_object(): void
    {
        $nameObject = new QuestName('The Dragon Hunt');

        $result = $this->cast->set($this->model, 'name', $nameObject, []);

        $this->assertSame('The Dragon Hunt', $result);
    }

    #[Test]
    public function it_validates_string_value(): void
    {
        $this->expectException(AssertException::class);

        $this->cast->set($this->model, 'name', '', []);
    }
}
