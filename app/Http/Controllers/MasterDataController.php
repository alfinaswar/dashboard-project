<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreMasterCategoryRequest;
use App\Http\Requests\StoreMasterSourceRequest;
use App\Models\MasterKategoriModal;
use App\Models\MasterStatusProyek;
use App\Models\SumberOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Inertia\Inertia;
use Inertia\Response;

class MasterDataController extends Controller
{
    public function sources(): Response
    {
        return Inertia::render('Master/Sources', ['items' => SumberOrder::orderBy('nama_sumber')->get()]);
    }

    public function categories(): Response
    {
        return Inertia::render('Master/Categories', ['items' => MasterKategoriModal::orderBy('nama_kategori')->get()]);
    }

    public function statuses(): Response
    {
        return Inertia::render('Master/Statuses', ['items' => MasterStatusProyek::orderBy('urutan')->get()]);
    }

    public function storeSource(StoreMasterSourceRequest $request): RedirectResponse
    {
        SumberOrder::create($request->validated());

        return back()->with('success', 'Sumber order ditambahkan.');
    }

    public function updateSource(Request $request, SumberOrder $sumberOrder): RedirectResponse
    {
        $data = $request->validate([
            'nama_sumber' => ['required', 'string', 'max:100', Rule::unique('sumber_order')->ignore($sumberOrder->id)],
            'persentase_potongan_default' => ['required', 'numeric', 'between:0,100'],
            'apakah_platform' => ['required', 'boolean'], 'aktif' => ['required', 'boolean'],
        ]);

        if ($sumberOrder->proyek()->exists()) {
            $data['persentase_potongan_default'] = $sumberOrder->persentase_potongan_default;
            $data['apakah_platform'] = $sumberOrder->apakah_platform;
        }

        $sumberOrder->update($data);

        return back()->with('success', 'Sumber order diperbarui.');
    }

    public function deleteSource(SumberOrder $sumberOrder): RedirectResponse
    {
        abort_if($sumberOrder->proyek()->exists(), 409, 'Sumber order masih digunakan proyek.');
        $sumberOrder->delete();

        return back()->with('success', 'Sumber order diarsipkan.');
    }

    public function storeCategory(StoreMasterCategoryRequest $request): RedirectResponse
    {
        MasterKategoriModal::create($request->validated());

        return back()->with('success', 'Kategori modal ditambahkan.');
    }

    public function updateCategory(Request $request, MasterKategoriModal $masterKategoriModal): RedirectResponse
    {
        $masterKategoriModal->update($request->validate([
            'nama_kategori' => ['required', 'string', 'max:100', Rule::unique('master_kategori_modal')->ignore($masterKategoriModal->id)],
            'keterangan' => ['nullable', 'string'],
        ]));

        return back()->with('success', 'Kategori modal diperbarui.');
    }

    public function deleteCategory(MasterKategoriModal $masterKategoriModal): RedirectResponse
    {
        abort_if($masterKategoriModal->modalProyek()->exists(), 409, 'Kategori modal masih digunakan.');
        $masterKategoriModal->delete();

        return back()->with('success', 'Kategori modal diarsipkan.');
    }

    public function storeStatus(Request $request): RedirectResponse
    {
        MasterStatusProyek::create($request->validate(['nama_status' => ['required', 'string', 'max:100', 'unique:master_status_proyek,nama_status'], 'warna_label' => ['required', 'string', 'max:50'], 'urutan' => ['required', 'integer', 'min:1']]));

        return back()->with('success', 'Status proyek ditambahkan.');
    }

    public function updateStatus(Request $request, MasterStatusProyek $masterStatusProyek): RedirectResponse
    {
        $masterStatusProyek->update($request->validate([
            'nama_status' => ['required', 'string', 'max:100', Rule::unique('master_status_proyek')->ignore($masterStatusProyek->id)],
            'warna_label' => ['required', 'string', 'max:50'],
            'urutan' => ['required', 'integer', 'min:1'],
        ]));

        return back()->with('success', 'Status proyek diperbarui.');
    }

    public function deleteStatus(MasterStatusProyek $masterStatusProyek): RedirectResponse
    {
        abort_if($masterStatusProyek->proyek()->exists(), 409, 'Status masih digunakan proyek.');
        $masterStatusProyek->delete();

        return back()->with('success', 'Status proyek diarsipkan.');
    }
}
