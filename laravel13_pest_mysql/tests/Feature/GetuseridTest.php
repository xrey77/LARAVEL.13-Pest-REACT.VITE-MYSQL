<?php

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class GetuseridTest extends TestCase
{
    use RefreshDatabase;
    public function test_get_user_by_id(): void
    {
        $user = User::factory()->create();
        $user->id = 1;
        $response = $this->actingAs($user, 'sanctum')
            ->withHeaders(['Accept' => 'application/json'])
            ->getJson("/api/getuserid/{$user->id}");

        $response->assertStatus(200)
            ->assertJsonStructure([
                'user' => [
                'firstname',
                'lastname',
                'email'
                ]
            ]);

    }

    
}