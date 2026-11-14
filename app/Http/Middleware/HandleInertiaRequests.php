<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use App\Models\Klien;
use App\Models\Proyek;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        return [
            ...parent::share($request),
            'auth' => [
                'user' => $request->user(),
            ],
            'flash' => [
                'success' => fn () => $request->session()->get('success'),
            ],
            'searchItems' => fn () => $request->user()?->hasRole('admin')
                ? Proyek::query()->select('id', 'kode_proyek', 'judul_proyek')->limit(100)->get()
                    ->map(fn ($project) => ['label' => $project->judul_proyek, 'meta' => $project->kode_proyek, 'url' => '/proyek/'.$project->id])
                    ->concat(Klien::query()->select('id', 'nama_klien')->limit(100)->get()
                        ->map(fn ($client) => ['label' => $client->nama_klien, 'meta' => 'Klien', 'url' => '/klien/'.$client->id]))
                : [],
        ];
    }
}
