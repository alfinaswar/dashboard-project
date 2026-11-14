<?php

namespace Tests\Feature;

use App\Models\Klien;
use App\Models\MasterStatusProyek;
use App\Models\Proyek;
use App\Models\SumberOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class KlienTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_and_soft_delete_client(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin']));

        $this->actingAs($admin)->post('/klien', ['nama_klien' => 'Acme', 'email' => 'hello@example.test'])
            ->assertRedirect('/klien');

        $client = Klien::firstOrFail();
        $this->actingAs($admin)->delete('/klien/'.$client->id)->assertRedirect('/klien');
        $this->assertSoftDeleted('klien', ['id' => $client->id]);
    }

    public function test_non_admin_cannot_create_client(): void
    {
        $this->actingAs(User::factory()->create())->post('/klien', ['nama_klien' => 'Denied'])->assertForbidden();
    }

    public function test_client_in_use_by_project_cannot_be_deleted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin']));
        $client = Klien::create(['nama_klien' => 'Linked']);
        $source = SumberOrder::create(['nama_sumber' => 'Source']);
        $status = MasterStatusProyek::create(['nama_status' => 'On Progress', 'warna_label' => 'blue', 'urutan' => 1]);
        Proyek::create([
            'kode_proyek' => 'PRJ-202611-091', 'klien_id' => $client->id, 'sumber_order_id' => $source->id,
            'status_id' => $status->id, 'judul_proyek' => 'Linked project', 'tanggal_mulai' => now()->toDateString(),
            'mata_uang' => 'IDR', 'nilai_kotor' => 100,
        ]);

        $this->actingAs($admin)->delete('/klien/'.$client->id)->assertStatus(409);
        $this->assertDatabaseHas('klien', ['id' => $client->id, 'deleted_at' => null]);
    }
}
