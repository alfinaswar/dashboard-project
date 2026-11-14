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

class ProyekFinanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_project_financial_accessors_use_active_costs_and_payments(): void
    {
        $project = $this->project();
        $cost = ModalProyek::create([
            'proyek_id' => $project->id,
            'kategori_modal_id' => MasterKategoriModal::create(['nama_kategori' => 'Tools'])->id,
            'nama_item' => 'Tool',
            'jumlah_biaya' => 100,
            'tanggal_pengeluaran' => now()->toDateString(),
        ]);
        ModalProyek::create([
            'proyek_id' => $project->id,
            'kategori_modal_id' => $cost->kategori_modal_id,
            'nama_item' => 'Deleted tool',
            'jumlah_biaya' => 900,
            'tanggal_pengeluaran' => now()->toDateString(),
        ])->delete();
        PembayaranProyek::create([
            'proyek_id' => $project->id,
            'tahapan_pembayaran' => 'DP',
            'jumlah_bayar' => 1200,
            'tanggal_bayar' => now()->toDateString(),
        ]);

        $this->assertSame('100.00', $project->fresh()->total_modal);
        $this->assertSame('800.00', $project->fresh()->laba_bersih);
        $this->assertSame('80.00', $project->fresh()->margin_keuntungan);
        $this->assertSame('1200.00', $project->fresh()->total_dibayar);
        $this->assertSame('0.00', $project->fresh()->sisa_tagihan);
    }

    public function test_admin_dashboard_returns_zero_totals_when_no_projects_exist(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'admin']));

        $this->actingAs($user)->get('/dashboard')
            ->assertInertia(fn (Assert $page) => $page->component('Dashboard')->has('metrics', 4));
    }

    public function test_zero_gross_contract_has_zero_margin(): void
    {
        $project = $this->project(['nilai_kotor' => 0, 'nilai_bersih_kontrak' => 0]);

        $this->assertSame('0.00', $project->margin_keuntungan);
    }

    public function test_completed_project_reports_locked_state(): void
    {
        $completed = MasterStatusProyek::create(['nama_status' => 'Selesai / Lunas', 'warna_label' => 'emerald', 'urutan' => 5]);
        $project = $this->project(['status_id' => $completed->id]);

        $this->assertTrue($project->apakah_terkunci);
    }

    private function project(array $overrides = []): Proyek
    {
        $client = Klien::create(['nama_klien' => 'Test Client']);
        $source = SumberOrder::create(['nama_sumber' => 'Direct', 'persentase_potongan_default' => 0]);
        $status = MasterStatusProyek::create(['nama_status' => 'On Progress', 'warna_label' => 'blue', 'urutan' => 1]);

        return Proyek::create(array_merge([
            'kode_proyek' => 'PRJ-202611-001',
            'klien_id' => $client->id,
            'sumber_order_id' => $source->id,
            'status_id' => $status->id,
            'judul_proyek' => 'Test project',
            'tanggal_mulai' => now()->toDateString(),
            'nilai_kotor' => 1000,
            'persentase_potongan_platform' => 20,
            'nominal_potongan_platform' => 200,
            'nilai_bersih_kontrak' => 900,
        ], $overrides));
    }
}
