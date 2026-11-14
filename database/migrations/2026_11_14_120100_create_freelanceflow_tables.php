<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function getConnection(): ?string
    {
        return config('database.default');
    }

    public function up(): void
    {
        Schema::create('profil_freelancer', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap', 150);
            $table->string('nomor_ktp', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->string('email', 150);
            $table->string('nomor_whatsapp', 30);
            $table->string('nama_bank', 100)->nullable();
            $table->string('nomor_rekening', 50)->nullable();
            $table->string('atas_nama_rekening', 150)->nullable();
            $table->string('tanda_tangan_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('sumber_order', function (Blueprint $table) {
            $table->id();
            $table->string('nama_sumber', 100)->unique();
            $table->decimal('persentase_potongan_default', 5, 2)->default(0);
            $table->boolean('apakah_platform')->default(false);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('master_kategori_modal', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kategori', 100)->unique();
            $table->text('keterangan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('master_status_proyek', function (Blueprint $table) {
            $table->id();
            $table->string('nama_status', 100)->unique();
            $table->string('warna_label', 50);
            $table->unsignedInteger('urutan');
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('klien', function (Blueprint $table) {
            $table->id();
            $table->string('nama_klien', 150);
            $table->string('nama_perusahaan', 150)->nullable();
            $table->string('nomor_whatsapp', 30)->nullable();
            $table->string('email', 150)->nullable();
            $table->text('alamat')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('proyek', function (Blueprint $table) {
            $table->id();
            $table->string('kode_proyek', 50)->unique();
            $table->foreignId('klien_id')->constrained('klien')->restrictOnDelete();
            $table->foreignId('sumber_order_id')->constrained('sumber_order')->restrictOnDelete();
            $table->foreignId('status_id')->constrained('master_status_proyek')->restrictOnDelete();
            $table->string('judul_proyek', 200);
            $table->text('deskripsi')->nullable();
            $table->string('tech_stack')->nullable();
            $table->string('url_produksi')->nullable();
            $table->string('url_repository')->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_deadline')->nullable();
            $table->date('tanggal_selesai')->nullable();
            $table->string('mata_uang', 10)->default('IDR');
            $table->decimal('nilai_kotor', 15, 2);
            $table->decimal('persentase_potongan_platform', 5, 2)->default(0);
            $table->decimal('nominal_potongan_platform', 15, 2)->default(0);
            $table->decimal('nilai_bersih_kontrak', 15, 2)->default(0);
            $table->string('nomor_kontrak', 100)->nullable()->unique();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('invoice_proyek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyek')->cascadeOnDelete();
            $table->enum('tipe_invoice', ['OTOMATIS', 'UPLOAD_PLATFORM']);
            $table->string('nomor_invoice', 100)->unique();
            $table->date('tanggal_terbit');
            $table->date('tanggal_jatuh_tempo')->nullable();
            $table->decimal('nominal_tagihan', 15, 2);
            $table->string('file_invoice_path')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('modal_proyek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyek')->cascadeOnDelete();
            $table->foreignId('kategori_modal_id')->constrained('master_kategori_modal')->restrictOnDelete();
            $table->string('nama_item', 200);
            $table->decimal('jumlah_biaya', 15, 2);
            $table->date('tanggal_pengeluaran');
            $table->string('bukti_nota_path')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('pembayaran_proyek', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proyek_id')->constrained('proyek')->cascadeOnDelete();
            $table->string('tahapan_pembayaran', 100);
            $table->decimal('jumlah_bayar', 15, 2);
            $table->date('tanggal_bayar');
            $table->string('metode_pembayaran', 100)->nullable();
            $table->string('bukti_transfer_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pembayaran_proyek');
        Schema::dropIfExists('modal_proyek');
        Schema::dropIfExists('invoice_proyek');
        Schema::dropIfExists('proyek');
        Schema::dropIfExists('klien');
        Schema::dropIfExists('master_status_proyek');
        Schema::dropIfExists('master_kategori_modal');
        Schema::dropIfExists('sumber_order');
        Schema::dropIfExists('profil_freelancer');
    }
};
