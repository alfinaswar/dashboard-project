<?php

namespace App\Http\Controllers;

use App\Models\MasterKategoriModal;
use App\Models\MasterStatusProyek;
use App\Models\ModalProyek;
use App\Models\PembayaranProyek;
use App\Models\Proyek;
use App\Models\SumberOrder;
use Inertia\Inertia;
use Inertia\Response;

class DashboardController extends Controller
{
    public function index(): Response
    {
        $all = Proyek::query();
        $gross = (float) (clone $all)->sum('nilai_kotor');
        $fees = (float) (clone $all)->sum('nominal_potongan_platform');
        $costs = (float) ModalProyek::query()->sum('jumlah_biaya');
        $paid = (float) PembayaranProyek::query()->sum('jumlah_bayar');
        $net = (float) (clone $all)->sum('nilai_bersih_kontrak');

        $start = now()->startOfMonth()->subMonths(5);
        $incomeByMonth = PembayaranProyek::query()->where('tanggal_bayar', '>=', $start->toDateString())
            ->get(['tanggal_bayar', 'jumlah_bayar'])->groupBy(fn ($row) => substr($row->tanggal_bayar, 0, 7))
            ->map(fn ($rows) => $rows->sum('jumlah_bayar'));
        $costByMonth = ModalProyek::query()->where('tanggal_pengeluaran', '>=', $start->toDateString())
            ->get(['tanggal_pengeluaran', 'jumlah_biaya'])->groupBy(fn ($row) => substr($row->tanggal_pengeluaran, 0, 7))
            ->map(fn ($rows) => $rows->sum('jumlah_biaya'));
        $monthly = collect(range(0, 5))->map(function (int $month) use ($start, $incomeByMonth, $costByMonth) {
            $date = $start->copy()->addMonths($month);
            $key = $date->format('Y-m');
            $income = (float) ($incomeByMonth[$key] ?? 0);
            $cost = (float) ($costByMonth[$key] ?? 0);

            return ['label' => $date->translatedFormat('M'), 'pendapatan' => $income, 'modal' => $cost, 'laba' => $income - $cost];
        });

        $activeProjects = Proyek::query()->with(['klien', 'status'])->withSum('pembayaran', 'jumlah_bayar')
            ->whereHas('status', fn ($query) => $query->whereNotIn('nama_status', ['Selesai / Lunas', 'Batal']))
            ->latest()->limit(8)->get()->map(fn ($project) => [
                'code' => $project->kode_proyek,
                'title' => $project->judul_proyek,
                'client' => $project->klien->nama_klien,
                'status' => $project->status->nama_status,
                'paid' => (float) ($project->pembayaran_sum_jumlah_bayar ?? 0),
                'net' => (float) $project->nilai_bersih_kontrak,
                'deadline' => $project->tanggal_deadline?->toDateString(),
            ]);

        return Inertia::render('Dashboard', [
            'metrics' => [
                ['label' => 'Laba bersih', 'value' => $net - $costs],
                ['label' => 'Nilai kontrak kotor', 'value' => $gross, 'detail' => 'Potongan platform Rp '.number_format($fees, 0, ',', '.')],
                ['label' => 'Total modal keluar', 'value' => $costs],
                ['label' => 'Sisa piutang', 'value' => max(0, $net - $paid)],
            ],
            'cashFlow' => $monthly,
            'sources' => SumberOrder::query()->withCount('proyek')->whereHas('proyek')->get(['id', 'nama_sumber'])->map(fn ($source) => ['name' => $source->nama_sumber, 'count' => $source->proyek_count]),
            'activeProjects' => $activeProjects,
            'masters' => [
                'sources' => SumberOrder::orderBy('nama_sumber')->get(['id', 'nama_sumber', 'persentase_potongan_default', 'apakah_platform', 'aktif']),
                'categories' => MasterKategoriModal::orderBy('nama_kategori')->get(['id', 'nama_kategori', 'keterangan']),
                'statuses' => MasterStatusProyek::orderBy('urutan')->get(['id', 'nama_status', 'warna_label', 'urutan']),
            ],
            'costCategories' => MasterKategoriModal::orderBy('nama_kategori')->get(['id', 'nama_kategori']),
        ]);
    }
}
