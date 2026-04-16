<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;

    public function test_create_user(): void
    {
        Role::create(['id' => 1, 'name' => 'ROLE_ADMIN']);
        Role::create(['id' => 2, 'name' => 'ROLE_USER']);

        $userData = [
            'firstname' => 'Lilian',
            'lastname'  => 'Hervias',
            'email'     => 'lilian@hervias.com',
            'mobile'    => '09123456789',
            'username'  => 'Lilian',
            'password'  => 'rey',
        ];

        $response = $this->withHeaders([
            'Accept' => 'application/json',
        ])->postJson('/api/register', $userData);


        $response->assertStatus(201)
             ->assertJsonStructure([
                'firstname',
                'lastname',
                'email',
                'mobile',
                'username'
             ]);

        $this->assertDatabaseHas('users', [
            'email' => 'lilian@hervias.com'
        ]);

        $user = User::where('email', 'lilian@hervias.com')->first();
        $user = User::where('username', 'Lilian')->first();
        $this->assertTrue(Hash::check('rey', $user->password));
    }
}
