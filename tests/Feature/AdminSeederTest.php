<?php

namespace Tests\Feature;

use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AdminSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_admin_from_environment_and_master_data(): void
    {
        config(['app.key' => 'base64:'.base64_encode(str_repeat('a', 32))]);
        config(['freelanceflow.admin_email' => 'owner@example.test', 'freelanceflow.admin_password' => 'local-development-password', 'freelanceflow.admin_name' => 'Owner']);

        $this->seed(DatabaseSeeder::class);

        $admin = User::where('email', 'owner@example.test')->firstOrFail();
        $this->assertTrue($admin->hasRole(Role::findByName('admin')));
        $this->assertDatabaseHas('sumber_order', ['nama_sumber' => 'Fastwork', 'persentase_potongan_default' => 15]);
        $this->assertDatabaseHas('master_status_proyek', ['nama_status' => 'Selesai / Lunas']);

    }

    public function test_seeder_requires_admin_credentials(): void
    {
        config(['freelanceflow.admin_email' => null, 'freelanceflow.admin_password' => null]);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Set ADMIN_EMAIL and ADMIN_PASSWORD in .env before seeding.');

        $this->seed(DatabaseSeeder::class);
    }
}
