<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreKlienRequest;
use App\Models\Klien;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class KlienController extends Controller
{
    public function index(): Response
    {
        return Inertia::render('Klien/Index', ['clients' => Klien::withCount('proyek')->orderBy('nama_klien')->paginate(15)]);
    }

    public function create(): Response
    {
        return Inertia::render('Klien/Form');
    }

    public function store(StoreKlienRequest $request): RedirectResponse
    {
        Klien::create($request->validated());

        return to_route('klien.index')->with('success', 'Klien berhasil dibuat.');
    }

    public function show(Klien $klien): Response
    {
        return Inertia::render('Klien/Show', ['client' => $klien->load('proyek.status')]);
    }

    public function edit(Klien $klien): Response
    {
        return Inertia::render('Klien/Form', ['client' => $klien]);
    }

    public function update(StoreKlienRequest $request, Klien $klien): RedirectResponse
    {
        $klien->update($request->validated());

        return to_route('klien.show', $klien)->with('success', 'Klien berhasil diperbarui.');
    }

    public function destroy(Klien $klien): RedirectResponse
    {
        abort_if($klien->proyek()->exists(), 409, 'Klien masih terhubung dengan proyek.');
        $klien->delete();

        return to_route('klien.index')->with('success', 'Klien dipindahkan ke arsip.');
    }
}
