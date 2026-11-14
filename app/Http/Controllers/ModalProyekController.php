<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreModalProyekRequest;
use App\Models\ModalProyek;
use App\Models\Proyek;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Storage;

class ModalProyekController extends Controller
{
    public function store(StoreModalProyekRequest $request, Proyek $proyek): RedirectResponse
    {
        abort_if($proyek->apakah_terkunci, 403);
        $data = $request->safe()->except('bukti_nota');
        $data['bukti_nota_path'] = $request->file('bukti_nota')?->store('bukti-nota', 'local');
        $proyek->modals()->create($data);

        return back()->with('success', 'Modal proyek dicatat.');
    }

    public function download(ModalProyek $modalProyek)
    {
        abort_unless($modalProyek->bukti_nota_path, 404);

        return Storage::disk('local')->download($modalProyek->bukti_nota_path);
    }

    public function destroy(ModalProyek $modalProyek): RedirectResponse
    {
        abort_if($modalProyek->proyek->apakah_terkunci, 403);
        $modalProyek->delete();

        return back()->with('success', 'Modal dipindahkan ke arsip.');
    }
}
