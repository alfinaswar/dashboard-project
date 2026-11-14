<?php

namespace Tests\Feature;

use App\Models\ProfilFreelancer;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class ProfileFreelancerTest extends TestCase
{
    use RefreshDatabase;

    public function test_admin_can_save_freelancer_profile_and_sensitive_fields_are_encrypted(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::create(['name' => 'admin']));

        $this->actingAs($admin)->put('/pengaturan/profil', [
            'nama_lengkap' => 'Owner', 'email' => 'owner@example.test', 'nomor_whatsapp' => '081234',
            'nomor_ktp' => '1234567890123456', 'alamat' => 'Jakarta', 'nama_bank' => 'BCA',
            'nomor_rekening' => '987654321', 'atas_nama_rekening' => 'Owner',
        ])->assertRedirect();

        $profile = ProfilFreelancer::firstOrFail();
        $this->assertSame('1234567890123456', $profile->nomor_ktp);
        $this->assertSame('987654321', $profile->nomor_rekening);
        $this->assertStringNotContainsString('987654321', $profile->getRawOriginal('nomor_rekening'));
    }
}
