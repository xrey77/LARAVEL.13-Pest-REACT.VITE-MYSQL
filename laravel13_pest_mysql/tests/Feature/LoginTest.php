<?php

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use function Pest\Laravel\{postJson};
use Tests\TestCase;

uses(RefreshDatabase::class);

beforeEach(function () {
    app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
});

class LoginTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function user_can_login_with_correct_credentials()
    {

    $role = Role::firstOrCreate(['name' => 'ROLE_USER']);

    $user = User::factory()->create([
        'username' => 'testuser',
        'password' => bcrypt('password123')
    ]);

    $user->assignRole($role);

    $response = $this->withHeaders([
        'Accept' => 'application/json',
    ])->postJson('/api/register', [
        'username' => 'testuser',
        'password' => 'password123'
    ]);


    $response->assertStatus(200)
        ->assertJsonPath('username', 'testuser')
        ->assertJsonFragment(['name' => 'ROLE_USER']);

    $response->assertStatus(200)
        ->assertJsonStructure([
            'message', 'id', 'firstname', 'lastname', 'email', 
            'mobile', 'username', 'isactivated', 'isblocked', 
            'mailtoken', 'roles', 'profilepic', 'qrcodeurl', 'token'
        ])
        ->assertJsonPath('message', 'Login successful.')
        ->assertJsonPath('username', 'testuser');
    
    expect($user->tokens)->not->toBeEmpty();

    }
}




// test('user cannot login with invalid credentials', function () {
//     // 1. Arrange: Create a user
//     User::factory()->create([
//         'username' => 'testuser',
//         'password' => bcrypt('password123'),
//     ]);

//     // 2. Act: Attempt login with the wrong password
//     $response = postJson(route('login'), [
//         'username' => 'testuser',
//         'password' => 'wrongpassword',
//     ]);

//     // 3. Assert: Expect a 401 Unauthorized response
//     $response->assertStatus(401)
//         ->assertJson(['message' => 'Invalid credentials.']);
// });

// test('login requires username and password', function () {
//     // Act & Assert: Validate that empty fields return 422 Unprocessable Entity
//     postJson(route('login'), [])
//         ->assertStatus(422)
//         ->assertJsonValidationErrors(['username', 'password']);
// }); 
