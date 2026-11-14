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
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProjectTransactionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_add_payment_and_private_transfer_evidence(): void
    {
        Storage::fake('local');
        [$admin, $project] = $this->adminProject();

        $this->actingAs($admin)->post('/proyek/'.$project->id.'/pembayaran', [
            'tahapan_pembayaran' => 'DP 50%', 'jumlah_bayar' => 500,
            'tanggal_bayar' => now()->toDateString(), 'metode_pembayaran' => 'Transfer',
            'bukti_transfer' => UploadedFile::fake()->image('transfer.png'),
        ])->assertRedirect();

        $payment = PembayaranProyek::firstOrFail();
        $this->assertSame('500.00', $project->fresh()->total_dibayar);
        Storage::disk('local')->assertExists($payment->bukti_transfer_path);
        $this->actingAs($admin)->get('/pembayaran/'.$payment->id.'/bukti')->assertOk();
    }

    public function test_admin_can_add_cost_and_private_receipt(): void
    {
        Storage::fake('local');
        [$admin, $project] = $this->adminProject();
        $category = MasterKategoriModal::create(['nama_kategori' => 'Hosting']);

        $this->actingAs($admin)->post('/proyek/'.$project->id.'/modal', [
            'kategori_modal_id' => $category->id, 'nama_item' => 'VPS', 'jumlah_biaya' => 125,
            'tanggal_pengeluaran' => now()->toDateString(), 'bukti_nota' => UploadedFile::fake()->create('receipt.pdf', 20, 'application/pdf'),
        ])->assertRedirect();

        $cost = ModalProyek::firstOrFail();
        $this->assertSame('125.00', $project->fresh()->total_modal);
        Storage::disk('local')->assertExists($cost->bukti_nota_path);
        $this->actingAs($admin)->get('/modal/'.$cost->id.'/bukti')->assertOk();
    }

    public function test_locked_project_rejects_new_payment_and_cost(): void
    {
        [$admin, $project] = $this->adminProject('Selesai / Lunas');
        $category = MasterKategoriModal::create(['nama_kategori' => 'Hosting']);

        $this->actingAs($admin)->post('/proyek/'.$project->id.'/pembayaran', [
            'tahapan_pembayaran' => 'Pelunasan', 'jumlah_bayar' => 500, 'tanggal_bayar' => now()->toDateString(),
        ])->assertForbidden();

        $this->actingAs($admin)->post('/proyek/'.$project->id.'/modal', [
            'kategori_modal_id' => $category->id, 'nama_item' => 'VPS', 'jumlah_biaya' => 100,
            'tanggal_pengeluaran' => now()->toDateString(),
        ])->assertForbidden();

        $this->assertDatabaseCount('pembayaran_proyek', 0);
        $this->assertDatabaseCount('modal_proyek', 0);
    }

    private function adminProject(string $statusName = 'On Progress'): array
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin']));
        $client = Klien::create(['nama_klien' => 'Client']);
        $source = SumberOrder::create(['nama_sumber' => 'Direct']);
        $status = MasterStatusProyek::create(['nama_status' => $statusName, 'warna_label' => 'blue', 'urutan' => 1]);
        $project = Proyek::create([
            'kode_proyek' => 'PRJ-202611-'.str_pad((string) random_int(1, 999), 3, '0', STR_PAD_LEFT),
            'klien_id' => $client->id, 'sumber_order_id' => $source->id, 'status_id' => $status->id,
            'judul_proyek' => 'Test project', 'tanggal_mulai' => now()->toDateString(),
            'mata_uang' => 'IDR', 'nilai_kotor' => 1000, 'nilai_bersih_kontrak' => 1000,
        ]);

        return [$admin, $project];
    }
}
