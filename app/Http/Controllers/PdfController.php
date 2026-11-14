<?php

namespace App\Http\Controllers;

use App\Models\ProfilFreelancer;
use App\Models\Proyek;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function cetakKontrak(Proyek $proyek)
    {
        $profile = ProfilFreelancer::first();
        abort_unless($profile, 422, 'Lengkapi profil freelancer terlebih dahulu.');

        if (! $proyek->nomor_kontrak) {
            $proyek->forceFill(['nomor_kontrak' => 'SPK/'.$proyek->kode_proyek.'/'.now()->format('Ymd')])->save();
        }

        $proyek->load(['klien', 'status']);

        return Pdf::loadView('pdf.kontrak', compact('proyek', 'profile'))
            ->download(str_replace('/', '-', $proyek->nomor_kontrak).'.pdf');
    }
}
