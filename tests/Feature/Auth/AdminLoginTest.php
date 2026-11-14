<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminLoginTest extends TestCase
{
    use RefreshDatabase;

    public function test_non_admin_credentials_cannot_start_authenticated_session(): void
    {
        User::factory()->create(['email' => 'non-admin@example.test', 'password' => Hash::make('password')]);

        $this->post('/login', ['email' => 'non-admin@example.test', 'password' => 'password'])
            ->assertRedirect('/login');

        $this->assertGuest();
    }

    public function test_admin_can_login_and_reach_dashboard(): void
    {
        $user = User::factory()->create(['email' => 'admin@example.test', 'password' => Hash::make('password')]);
        $user->assignRole(Role::create(['name' => 'admin']));

        $this->post('/login', ['email' => 'admin@example.test', 'password' => 'password'])
            ->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($user);
    }
}
