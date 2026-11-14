<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateProfilFreelancerRequest;
use App\Models\ProfilFreelancer;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProfilFreelancerController extends Controller
{
    public function edit(): Response
    {
        return Inertia::render('Pengaturan/Profil', ['profile' => ProfilFreelancer::first()]);
    }

    public function update(UpdateProfilFreelancerRequest $request): RedirectResponse
    {
        $profile = ProfilFreelancer::first() ?? new ProfilFreelancer;
        $data = $request->safe()->except('tanda_tangan');
        $data['tanda_tangan_path'] = $request->file('tanda_tangan')?->store('tanda-tangan', 'local') ?? $profile->tanda_tangan_path;
        $profile->fill($data)->save();

        return back()->with('success', 'Profil freelancer disimpan.');
    }
}
