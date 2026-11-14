<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreInvoiceRequest;
use App\Models\InvoiceProyek;
use App\Models\ProfilFreelancer;
use App\Models\Proyek;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class InvoiceProyekController extends Controller
{
    public function generateOtomatis(Proyek $proyek): RedirectResponse
    {
        abort_if($proyek->apakah_terkunci, 403);
        $number = 'INV/'.$proyek->kode_proyek.'/'.str_pad((string) ($proyek->invoices()->withTrashed()->count() + 1), 2, '0', STR_PAD_LEFT);
        $proyek->invoices()->create([
            'tipe_invoice' => 'OTOMATIS', 'nomor_invoice' => $number,
            'tanggal_terbit' => now()->toDateString(), 'nominal_tagihan' => $proyek->sisa_tagihan,
        ]);

        return back()->with('success', 'Invoice berhasil dibuat.')->with('invoice_number', $number);
    }

    public function uploadPlatform(StoreInvoiceRequest $request, Proyek $proyek): RedirectResponse
    {
        abort_if($proyek->apakah_terkunci, 403);
        $data = $request->validated();
        $data['tipe_invoice'] = 'UPLOAD_PLATFORM';
        $data['file_invoice_path'] = $request->file('file_invoice')->store('invoice-platform', 'local');
        unset($data['file_invoice']);
        $proyek->invoices()->create($data);

        return back()->with('success', 'Invoice platform diarsipkan.');
    }

    public function downloadPdf(InvoiceProyek $invoice)
    {
        abort_if($invoice->tipe_invoice !== 'OTOMATIS', 404);
        $invoice->load('proyek.klien');

        return Pdf::loadView('pdf.invoice', ['invoice' => $invoice, 'profile' => ProfilFreelancer::first()])
            ->download(str_replace('/', '-', $invoice->nomor_invoice).'.pdf');
    }

    public function downloadPlatform(InvoiceProyek $invoice)
    {
        abort_unless($invoice->tipe_invoice === 'UPLOAD_PLATFORM' && $invoice->file_invoice_path, 404);

        return Storage::disk('local')->download($invoice->file_invoice_path);
    }

    public function destroy(InvoiceProyek $invoice): RedirectResponse
    {
        abort_if($invoice->proyek->apakah_terkunci, 403);
        $invoice->delete();

        return back()->with('success', 'Invoice dipindahkan ke arsip.');
    }
}
