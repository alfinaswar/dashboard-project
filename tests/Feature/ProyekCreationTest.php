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

class ProyekCreationTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_uses_master_fee_and_calculates_net_contract(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'admin']));
        $client = Klien::create(['nama_klien' => 'Client']);
        $source = SumberOrder::create(['nama_sumber' => 'Fastwork', 'persentase_potongan_default' => 15, 'apakah_platform' => true]);
        $status = MasterStatusProyek::create(['nama_status' => 'Negosiasi / Lead', 'warna_label' => 'amber', 'urutan' => 1]);

        $this->actingAs($user)->post('/proyek', [
            'klien_id' => $client->id,
            'sumber_order_id' => $source->id,
            'status_id' => $status->id,
            'judul_proyek' => 'Website',
            'tanggal_mulai' => '2026-11-14',
            'mata_uang' => 'IDR',
            'nilai_kotor' => 1000000,
            'persentase_potongan_platform' => 0,
        ])->assertRedirect('/proyek/1');

        $this->assertDatabaseHas('proyek', [
            'kode_proyek' => 'PRJ-202611-001',
            'persentase_potongan_platform' => 15,
            'nominal_potongan_platform' => 150000,
            'nilai_bersih_kontrak' => 850000,
        ]);
    }

    public function test_locked_project_rejects_financial_update(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'admin']));
        $client = Klien::create(['nama_klien' => 'Client']);
        $source = SumberOrder::create(['nama_sumber' => 'Direct']);
        $status = MasterStatusProyek::create(['nama_status' => 'Selesai / Lunas', 'warna_label' => 'emerald', 'urutan' => 5]);
        $project = Proyek::create([
            'kode_proyek' => 'PRJ-202611-002', 'klien_id' => $client->id, 'sumber_order_id' => $source->id,
            'status_id' => $status->id, 'judul_proyek' => 'Done', 'tanggal_mulai' => '2026-11-14',
            'mata_uang' => 'IDR', 'nilai_kotor' => 1000, 'nilai_bersih_kontrak' => 1000,
        ]);

        $this->actingAs($user)->put('/proyek/'.$project->id, [
            'klien_id' => $client->id, 'sumber_order_id' => $source->id, 'status_id' => $status->id,
            'judul_proyek' => 'Changed', 'tanggal_mulai' => '2026-11-14', 'mata_uang' => 'IDR',
            'nilai_kotor' => 2000, 'persentase_potongan_platform' => 0,
        ])->assertForbidden();

        $this->assertDatabaseHas('proyek', ['id' => $project->id, 'nilai_kotor' => 1000]);
    }

    public function test_changing_order_source_recalculates_fee_using_new_default(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'admin']));
        $client = Klien::create(['nama_klien' => 'Client']);
        $oldSource = SumberOrder::create(['nama_sumber' => 'Direct', 'persentase_potongan_default' => 0]);
        $newSource = SumberOrder::create(['nama_sumber' => 'Fastwork', 'persentase_potongan_default' => 15]);
        $status = MasterStatusProyek::create(['nama_status' => 'On Progress', 'warna_label' => 'blue', 'urutan' => 1]);
        $project = Proyek::create([
            'kode_proyek' => 'PRJ-202611-003', 'klien_id' => $client->id, 'sumber_order_id' => $oldSource->id,
            'status_id' => $status->id, 'judul_proyek' => 'Change source', 'tanggal_mulai' => '2026-11-14',
            'mata_uang' => 'IDR', 'nilai_kotor' => 1000000, 'nilai_bersih_kontrak' => 1000000,
        ]);

        $this->actingAs($user)->put('/proyek/'.$project->id, [
            'klien_id' => $client->id, 'sumber_order_id' => $newSource->id, 'status_id' => $status->id,
            'judul_proyek' => 'Change source', 'tanggal_mulai' => '2026-11-14', 'mata_uang' => 'IDR',
            'nilai_kotor' => 1000000, 'persentase_potongan_platform' => 0,
        ])->assertRedirect('/proyek/'.$project->id);

        $this->assertDatabaseHas('proyek', [
            'id' => $project->id,
            'persentase_potongan_platform' => 15,
            'nominal_potongan_platform' => 150000,
            'nilai_bersih_kontrak' => 850000,
        ]);
    }

    public function test_source_fee_edit_does_not_change_existing_project_snapshot(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'admin']));
        $client = Klien::create(['nama_klien' => 'Client']);
        $source = SumberOrder::create(['nama_sumber' => 'Fastwork', 'persentase_potongan_default' => 15, 'apakah_platform' => true]);
        $status = MasterStatusProyek::create(['nama_status' => 'On Progress', 'warna_label' => 'blue', 'urutan' => 1]);
        $project = \App\Models\Proyek::create([
            'kode_proyek' => 'PRJ-202611-004', 'klien_id' => $client->id, 'sumber_order_id' => $source->id,
            'status_id' => $status->id, 'judul_proyek' => 'Fee snapshot', 'tanggal_mulai' => '2026-11-14',
            'mata_uang' => 'IDR', 'nilai_kotor' => 1000000, 'persentase_potongan_platform' => 15,
            'nominal_potongan_platform' => 150000, 'nilai_bersih_kontrak' => 850000,
        ]);

        $this->actingAs($user)->put('/master/sumber-order/'.$source->id, [
            'nama_sumber' => 'Fastwork', 'persentase_potongan_default' => 20, 'apakah_platform' => true, 'aktif' => true,
        ])->assertRedirect();

        $this->assertDatabaseHas('sumber_order', ['id' => $source->id, 'persentase_potongan_default' => 15]);
        $this->assertDatabaseHas('proyek', ['id' => $project->id, 'persentase_potongan_platform' => 15]);
    }
}
