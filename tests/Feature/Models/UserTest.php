<?php

namespace Tests\Unit\Models;

use App\Models\User;
use Carbon\CarbonInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_has_fillable_attributes()
    {
        $fillable = ['name', 'email', 'password'];

        $user = new User;

        $this->assertEquals($fillable, $user->getFillable());
    }

    #[Test]
    public function it_hides_password_and_remember_token()
    {
        $hidden = ['password', 'remember_token'];

        $user = new User;

        $this->assertEquals($hidden, $user->getHidden());
    }

    #[Test]
    public function it_casts_attributes_correctly()
    {
        $user = User::factory()->create();

        $this->assertIsInt($user->id);
        $this->assertInstanceOf(CarbonInterface::class, $user->created_at);
        $this->assertInstanceOf(CarbonInterface::class, $user->updated_at);
    }

    #[Test]
    public function it_hashes_password_on_creation()
    {
        $user = User::factory()->create([
            'password' => 'plain-text-password',
        ]);

        $this->assertNotEquals('plain-text-password', $user->password);
        $this->assertTrue(Hash::check('plain-text-password', $user->password));
    }

    #[Test]
    public function it_can_be_created_with_valid_data()
    {
        $userData = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
            'password' => Hash::make('password123'),
        ];

        User::create($userData);

        $this->assertDatabaseHas('users', [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ]);
    }

    #[Test]
    public function email_verified_at_can_be_null()
    {
        $user = User::factory()->create([
            'email_verified_at' => null,
        ]);

        $this->assertNull($user->email_verified_at);
    }

    #[Test]
    public function email_verified_at_is_cast_to_datetime()
    {
        $user = User::factory()->create([
            'email_verified_at' => now(),
        ]);

        $this->assertInstanceOf(CarbonInterface::class, $user->email_verified_at);
    }
}
