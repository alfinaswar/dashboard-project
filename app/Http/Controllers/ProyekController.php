<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProyekRequest;
use App\Models\Klien;
use App\Models\MasterStatusProyek;
use App\Models\Proyek;
use App\Models\SumberOrder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class ProyekController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Proyek/Index', [
            'projects' => Proyek::with(['klien', 'status', 'sumberOrder'])->withSum('modals', 'jumlah_biaya')->withSum('pembayaran', 'jumlah_bayar')->latest()->paginate(12)
                ->through(function (Proyek $project) {
                    $project->setAttribute('total_modal_raw', $project->modals_sum_jumlah_biaya ?? 0);
                    $project->setAttribute('total_dibayar_raw', $project->pembayaran_sum_jumlah_bayar ?? 0);

                    return $project;
                }),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('Proyek/Form', $this->formOptions());
    }

    public function store(StoreProyekRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $source = SumberOrder::findOrFail($data['sumber_order_id']);
        $project = DB::transaction(function () use ($data, $source) {
            $data['kode_proyek'] = $this->nextCode();
            $data['persentase_potongan_platform'] = $source->persentase_potongan_default;
            $data['nominal_potongan_platform'] = round($data['nilai_kotor'] * $data['persentase_potongan_platform'] / 100, 2);
            $data['nilai_bersih_kontrak'] = round($data['nilai_kotor'] - $data['nominal_potongan_platform'], 2);

            return Proyek::create($data);
        });

        return to_route('proyek.show', $project)->with('success', 'Proyek berhasil dibuat.');
    }

    public function show(Proyek $proyek): Response
    {
        abort_if($proyek->apakah_terkunci, 403, 'Proyek selesai dan terkunci.');
        $proyek->load(['klien', 'status', 'sumberOrder', 'invoices', 'modals.kategori', 'pembayaran']);
        $proyek->setAttribute('total_modal_raw', $proyek->modals->sum('jumlah_biaya'));
        $proyek->setAttribute('total_dibayar_raw', $proyek->pembayaran->sum('jumlah_bayar'));

        return Inertia::render('Proyek/Show', [
            'project' => $proyek,
            ...$this->formOptions(),
        ]);
    }

    public function edit(Proyek $proyek): Response
    {
        abort_if($proyek->apakah_terkunci, 403, 'Proyek selesai dan terkunci.');

        return Inertia::render('Proyek/Form', ['project' => $proyek, ...$this->formOptions()]);
    }

    public function update(StoreProyekRequest $request, Proyek $proyek): RedirectResponse
    {
        abort_if($proyek->apakah_terkunci, 403, 'Proyek selesai dan terkunci.');
        $data = $request->validated();
        $newFeePercent = $data['sumber_order_id'] === $proyek->sumber_order_id
            ? (float) $proyek->persentase_potongan_platform
            : (float) SumberOrder::findOrFail($data['sumber_order_id'])->persentase_potongan_default;
        $data['persentase_potongan_platform'] = $newFeePercent;
        $data['nominal_potongan_platform'] = round($data['nilai_kotor'] * $data['persentase_potongan_platform'] / 100, 2);
        $data['nilai_bersih_kontrak'] = round($data['nilai_kotor'] - $data['nominal_potongan_platform'], 2);
        $proyek->update($data);

        return to_route('proyek.show', $proyek)->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Proyek $proyek): RedirectResponse
    {
        abort_if($proyek->apakah_terkunci, 403, 'Proyek selesai dan terkunci.');
        $proyek->delete();

        return to_route('proyek.index')->with('success', 'Proyek dipindahkan ke arsip.');
    }

    private function formOptions(): array
    {
        return [
            'clients' => Klien::orderBy('nama_klien')->get(['id', 'nama_klien']),
            'sources' => SumberOrder::where('aktif', true)->orderBy('nama_sumber')->get(['id', 'nama_sumber', 'persentase_potongan_default']),
            'statuses' => MasterStatusProyek::orderBy('urutan')->get(['id', 'nama_status']),
        ];
    }

    private function nextCode(): string
    {
        $prefix = 'PRJ-'.now()->format('Ym').'-';
        $max = Proyek::withTrashed()->where('kode_proyek', 'like', $prefix.'%')->lockForUpdate()->pluck('kode_proyek')
            ->map(fn ($code) => (int) Str::afterLast($code, '-'))->max() ?? 0;

        return $prefix.str_pad((string) ($max + 1), 3, '0', STR_PAD_LEFT);
    }
}
