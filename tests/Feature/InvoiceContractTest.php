<?php

namespace Tests\Feature;

use App\Models\InvoiceProyek;
use App\Models\Klien;
use App\Models\MasterStatusProyek;
use App\Models\ProfilFreelancer;
use App\Models\Proyek;
use App\Models\SumberOrder;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class InvoiceContractTest extends TestCase
{
    use RefreshDatabase;

    public function test_direct_project_generates_persistent_invoice_pdf(): void
    {
        [$admin, $project] = $this->adminProject('WhatsApp Direct');

        $this->actingAs($admin)->post('/proyek/'.$project->id.'/invoice/generate')->assertRedirect();

        $invoice = InvoiceProyek::firstOrFail();
        $this->assertSame('INV/'.$project->kode_proyek.'/01', $invoice->nomor_invoice);
        $this->actingAs($admin)->get('/invoice/'.$invoice->id.'/download')->assertOk()->assertHeader('content-type', 'application/pdf');
    }

    public function test_platform_invoice_requires_file_and_keeps_upload_private(): void
    {
        Storage::fake('local');
        [$admin, $project] = $this->adminProject('Fastwork');

        $this->actingAs($admin)->post('/proyek/'.$project->id.'/invoice/upload', [
            'nomor_invoice' => 'FW-123', 'tanggal_terbit' => now()->toDateString(), 'nominal_tagihan' => 250,
        ])->assertSessionHasErrors('file_invoice');

        $this->actingAs($admin)->post('/proyek/'.$project->id.'/invoice/upload', [
            'nomor_invoice' => 'FW-123', 'tanggal_terbit' => now()->toDateString(), 'nominal_tagihan' => 250,
            'file_invoice' => UploadedFile::fake()->create('invoice.pdf', 24, 'application/pdf'),
        ])->assertRedirect();

        $invoice = InvoiceProyek::firstOrFail();
        Storage::disk('local')->assertExists($invoice->file_invoice_path);
        $this->actingAs($admin)->get('/invoice/'.$invoice->id.'/file')->assertOk();
    }

    public function test_contract_number_is_saved_and_reused_for_pdf(): void
    {
        [$admin, $project] = $this->adminProject('WhatsApp Direct');
        ProfilFreelancer::create(['nama_lengkap' => 'Owner', 'email' => 'owner@example.test', 'nomor_whatsapp' => '0812']);

        $this->actingAs($admin)->get('/proyek/'.$project->id.'/kontrak-pdf')->assertOk()->assertHeader('content-type', 'application/pdf');
        $number = $project->fresh()->nomor_kontrak;
        $this->assertNotEmpty($number);
        $this->actingAs($admin)->get('/proyek/'.$project->id.'/kontrak-pdf')->assertOk();
        $this->assertSame($number, $project->fresh()->nomor_kontrak);
    }

    public function test_platform_invoice_upload_is_rejected_when_project_is_locked(): void
    {
        [$admin, $project] = $this->adminProject('Fastwork', 'Selesai / Lunas');

        $this->actingAs($admin)->post('/proyek/'.$project->id.'/invoice/upload', [
            'nomor_invoice' => 'FW-LOCKED', 'tanggal_terbit' => now()->toDateString(), 'nominal_tagihan' => 100,
            'file_invoice' => UploadedFile::fake()->create('invoice.pdf', 24, 'application/pdf'),
        ])->assertForbidden();
    }

    private function adminProject(string $sourceName, string $statusName = 'On Progress'): array
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin']));
        $client = Klien::create(['nama_klien' => 'Client', 'alamat' => 'Client address']);
        $source = SumberOrder::create(['nama_sumber' => $sourceName]);
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
