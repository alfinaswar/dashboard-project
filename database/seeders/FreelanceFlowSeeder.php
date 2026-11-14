<?php

namespace Database\Seeders;

use App\Models\MasterKategoriModal;
use App\Models\MasterStatusProyek;
use App\Models\SumberOrder;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use RuntimeException;
use Spatie\Permission\Models\Role;

class FreelanceFlowSeeder extends Seeder
{
    public function run(): void
    {
        $email = config('freelanceflow.admin_email');
        $password = config('freelanceflow.admin_password');

        if (! $email || ! $password) {
            throw new RuntimeException('Set ADMIN_EMAIL and ADMIN_PASSWORD in .env before seeding.');
        }

        $role = Role::findOrCreate('admin', 'web');
        $admin = User::firstOrNew(['email' => $email]);
        $admin->name = config('freelanceflow.admin_name', 'Admin');
        $admin->password = Hash::make($password);
        $admin->save();
        $admin->syncRoles([$role]);

        foreach ([['WhatsApp Direct', 0, false], ['Fiverr', 20, true], ['Upwork', 10, true], ['Fastwork', 15, true], ['Projects.co.id', 12, true]] as [$name, $fee, $platform]) {
            SumberOrder::firstOrCreate(['nama_sumber' => $name], [
                'persentase_potongan_default' => $fee,
                'apakah_platform' => $platform,
                'aktif' => true,
            ]);
        }

        foreach (['AI Tools & API Token', 'Hosting & Domain', 'Plugin/Asset', 'Subkontraktor', 'Operasional'] as $name) {
            MasterKategoriModal::firstOrCreate(['nama_kategori' => $name]);
        }

        foreach ([
            ['Negosiasi / Lead', 'slate', 1], ['DP Masuk', 'amber', 2], ['On Progress', 'blue', 3],
            ['Revisi Klien', 'violet', 4], ['Selesai / Lunas', 'emerald', 5], ['Batal', 'rose', 6],
        ] as [$name, $color, $order]) {
            MasterStatusProyek::firstOrCreate(['nama_status' => $name], ['warna_label' => $color, 'urutan' => $order]);
        }

    }
}
