<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminRoleOnlyTest extends TestCase
{
    use RefreshDatabase;

    public function test_every_privileged_page_rejects_authenticated_non_admin(): void
    {
        $user = User::factory()->create();

        foreach (['/dashboard', '/proyek', '/klien', '/master/sumber-order', '/master/kategori-modal', '/master/status-proyek', '/pengaturan/profil'] as $path) {
            $this->actingAs($user)->get($path)->assertForbidden();
        }
    }

    public function test_admin_can_open_every_privileged_page(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'admin']));

        foreach (['/dashboard', '/proyek', '/klien', '/master/sumber-order', '/master/kategori-modal', '/master/status-proyek', '/pengaturan/profil'] as $path) {
            $this->actingAs($user)->get($path)->assertOk();
        }
    }
}
