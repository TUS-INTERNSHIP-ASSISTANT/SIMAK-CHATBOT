<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_login_and_get_token()
    {
        $user = \App\Models\User::factory()->create([
            'email' => 'staff@simak.com',
            'password' => \Illuminate\Support\Facades\Hash::make('password'),
            'role' => 'staff',
        ]);

        $response = $this->postJson('/api/login', [
            'email' => 'staff@simak.com',
            'password' => 'password',
        ]);

        $response->assertStatus(200)
                 ->assertJsonStructure(['access_token', 'user']);
    }

    public function test_staff_can_access_dashboard()
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'staff',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/staff/dashboard');

        $response->assertStatus(200);
    }

    public function test_student_cannot_access_dashboard()
    {
        $user = \App\Models\User::factory()->create([
            'role' => 'student',
        ]);

        $response = $this->actingAs($user, 'sanctum')->getJson('/api/staff/dashboard');

        $response->assertStatus(403);
    }
}
