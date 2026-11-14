<?php

namespace Tests\Feature;

use App\Models\Klien;
use App\Models\MasterKategoriModal;
use App\Models\MasterStatusProyek;
use App\Models\ModalProyek;
use App\Models\PembayaranProyek;
use App\Models\Proyek;
use App\Models\SumberOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Inertia\Testing\AssertableInertia as Assert;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_supplies_empty_state_data(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'admin']));

        $this->actingAs($user)->get('/dashboard')->assertInertia(fn (Assert $page) => $page
            ->component('Dashboard')
            ->has('metrics', 4)
            ->has('cashFlow', 6)
            ->has('sources', 0)
            ->has('activeProjects', 0));
    }

    public function test_dashboard_monthly_aggregation_supports_sqlite(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin']));
        PembayaranProyek::create(['proyek_id' => $this->project()->id, 'tahapan_pembayaran' => 'DP', 'jumlah_bayar' => 12, 'tanggal_bayar' => now()->toDateString()]);

        $this->actingAs($admin)->get('/dashboard')->assertOk();
    }

    public function test_dashboard_totals_match_project_transactions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin']));
        $client = Klien::create(['nama_klien' => 'Client']);
        $source = SumberOrder::create(['nama_sumber' => 'Direct']);
        $status = MasterStatusProyek::create(['nama_status' => 'On Progress', 'warna_label' => 'blue', 'urutan' => 1]);
        $project = Proyek::create([
            'kode_proyek' => 'PRJ-202611-099', 'klien_id' => $client->id, 'sumber_order_id' => $source->id,
            'status_id' => $status->id, 'judul_proyek' => 'Contract', 'tanggal_mulai' => now()->toDateString(),
            'mata_uang' => 'IDR', 'nilai_kotor' => 1000, 'nominal_potongan_platform' => 100,
            'nilai_bersih_kontrak' => 900,
        ]);
        ModalProyek::create([
            'proyek_id' => $project->id, 'kategori_modal_id' => MasterKategoriModal::create(['nama_kategori' => 'Tools'])->id,
            'nama_item' => 'Tools', 'jumlah_biaya' => 100, 'tanggal_pengeluaran' => now()->toDateString(),
        ]);
        PembayaranProyek::create([
            'proyek_id' => $project->id, 'tahapan_pembayaran' => 'DP', 'jumlah_bayar' => 200, 'tanggal_bayar' => now()->toDateString(),
        ]);

        $this->actingAs($admin)->get('/dashboard')->assertInertia(fn (Assert $page) => $page
            ->where('metrics.0.value', 800)
            ->where('metrics.1.value', 1000)
            ->where('metrics.2.value', 100)
            ->where('metrics.3.value', 700)
            ->has('activeProjects', 1));
    }

    private function project(): Proyek
    {
        $client = Klien::create(['nama_klien' => 'Monthly client']);
        $source = SumberOrder::create(['nama_sumber' => 'Monthly source']);
        $status = MasterStatusProyek::create(['nama_status' => 'Monthly status', 'warna_label' => 'blue', 'urutan' => 1]);

        return Proyek::create([
            'kode_proyek' => 'PRJ-202611-098', 'klien_id' => $client->id, 'sumber_order_id' => $source->id,
            'status_id' => $status->id, 'judul_proyek' => 'Monthly project', 'tanggal_mulai' => now()->toDateString(),
            'mata_uang' => 'IDR', 'nilai_kotor' => 12, 'nilai_bersih_kontrak' => 12,
        ]);
    }
}
