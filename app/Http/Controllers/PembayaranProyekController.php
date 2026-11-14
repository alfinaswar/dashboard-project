<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePembayaranProyekRequest;
use App\Models\PembayaranProyek;
use App\Models\Proyek;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class PembayaranProyekController extends Controller
{
    public function store(StorePembayaranProyekRequest $request, Proyek $proyek): RedirectResponse
    {
        abort_if($proyek->apakah_terkunci, 403);
        $data = $request->safe()->except('bukti_transfer');
        $data['bukti_transfer_path'] = $request->file('bukti_transfer')?->store('bukti-transfer', 'local');
        $proyek->pembayaran()->create($data);

        return back()->with('success', 'Pembayaran proyek dicatat.');
    }

    public function download(PembayaranProyek $pembayaranProyek)
    {
        abort_unless($pembayaranProyek->bukti_transfer_path, 404);

        return Storage::disk('local')->download($pembayaranProyek->bukti_transfer_path);
    }

    public function destroy(PembayaranProyek $pembayaranProyek): RedirectResponse
    {
        abort_if($pembayaranProyek->proyek->apakah_terkunci, 403);
        $pembayaranProyek->delete();

        return back()->with('success', 'Pembayaran dipindahkan ke arsip.');
    }
}
