<?php

namespace Tests\Feature;

use App\Models\MasterKategoriModal;
use App\Models\MasterStatusProyek;
use App\Models\SumberOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class MasterDataTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_create_update_and_soft_delete_master_records(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin']));

        $this->actingAs($admin)->post('/master/sumber-order', ['nama_sumber' => 'Direct', 'persentase_potongan_default' => 0, 'apakah_platform' => false, 'aktif' => true])->assertRedirect();
        $source = SumberOrder::firstOrFail();
        $this->actingAs($admin)->put('/master/sumber-order/'.$source->id, ['nama_sumber' => 'Direct WA', 'persentase_potongan_default' => 0, 'apakah_platform' => false, 'aktif' => true])->assertRedirect();
        $this->actingAs($admin)->delete('/master/sumber-order/'.$source->id)->assertRedirect();
        $this->assertSoftDeleted('sumber_order', ['id' => $source->id]);

        $this->actingAs($admin)->post('/master/kategori-modal', ['nama_kategori' => 'Hosting'])->assertRedirect();
        $category = MasterKategoriModal::firstOrFail();
        $this->actingAs($admin)->put('/master/kategori-modal/'.$category->id, ['nama_kategori' => 'Cloud hosting'])->assertRedirect();
        $this->actingAs($admin)->delete('/master/kategori-modal/'.$category->id)->assertRedirect();
        $this->assertSoftDeleted('master_kategori_modal', ['id' => $category->id]);

        $this->actingAs($admin)->post('/master/status-proyek', ['nama_status' => 'Custom', 'warna_label' => 'blue', 'urutan' => 7])->assertRedirect();
        $status = MasterStatusProyek::where('nama_status', 'Custom')->firstOrFail();
        $this->actingAs($admin)->put('/master/status-proyek/'.$status->id, ['nama_status' => 'Custom progress', 'warna_label' => 'amber', 'urutan' => 8])->assertRedirect();
        $this->actingAs($admin)->delete('/master/status-proyek/'.$status->id)->assertRedirect();
        $this->assertSoftDeleted('master_status_proyek', ['id' => $status->id]);
    }
}
