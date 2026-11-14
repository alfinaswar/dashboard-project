# Product Requirements Document (PRD)

## Sistem Manajemen Proyek Freelance & Keuangan (`FreelanceFlow Pro`)

### 1. Ringkasan Eksekutif (Executive Summary)

Sistem aplikasi web modern berbasis **Laravel 11/12 + Inertia.js + Vue 3 (atau React) + Tailwind CSS** yang dirancang untuk mengelola siklus hidup proyek freelance—mulai dari sumber order (WhatsApp langsung maupun platform freelance seperti Fiverr/Upwork/Fastwork), pencatatan nilai kontrak kotor, potongan fee platform, modal operasional (langganan AI, VPS, domain, aset), termin pembayaran dari klien, kalkulasi laba bersih secara otomatis (`Net Profit`), hingga pengelolaan dokumen legal dan penagihan (**Surat Perjanjian Kerja Sama** dan **Hybrid Invoice: Generate PDF Otomatis atau Upload Invoice Bawaan Platform**). Dilengkapi dengan fitur **Soft Deletes**, perlindungan transaksi selesai, dan antarmuka **Clean White Modern SaaS** layaknya aplikasi *Single Page Application* (SPA) yang cepat, bersih, dan elegan.

### 2. Tujuan & Target Pengguna (Objectives & Target Users)

* **Tujuan Utama:** Menghilangkan perhitungan manual yang rumit terkait potongan komisi platform dan modal operasional agar freelancer dapat melihat laba bersih riil per proyek secara *real-time*, serta menyederhanakan administrasi kontrak kerja dan invoice (baik untuk klien langsung maupun klien platform) dalam antarmuka modern bertema putih bersih.

* **Pengguna Utama:** Full-Stack Developer / Freelancer independen yang menangani klien multikanal (WA dan platform freelance).

### 3. Batasan Teknis & Tech Stack (Technical Stack)

* **Backend Framework:** Laravel 11 / 12 (PHP 8.2+)

* **Bridge / Full-Stack Adapter:** **Inertia.js** (menghubungkan Laravel langsung ke frontend modern tanpa perlu membangun REST API terpisah).

* **Frontend Framework:** **Vue 3** (`<script setup>` Composition API) *atau* **React 18+** (TypeScript/JavaScript).

* **UI & Styling System:** **Tailwind CSS v3/v4** dipadukan dengan **Shadcn UI** (`shadcn-vue` atau `shadcn/ui`) serta **Lucide Icons** dengan estetika **Clean White SaaS** (terinspirasi dari Stripe, Linear, dan Vercel).

* **Database:** **MySQL 8.0+** (Engine InnoDB, Charset `utf8mb4`, Collation `utf8mb4_unicode_ci`). Konvensi nama tabel & kolom menggunakan Bahasa Indonesia (*PascalCase*).

* **Fitur Keamanan & Audit Data:**

  * Seluruh tabel transaksional dan master wajib menggunakan `SoftDeletes` (`deleted_at`).

  * Seluruh route dilindungi middleware autentikasi (`auth` via Laravel Breeze / Fortify).

  * Penguncian data finansial (`IsLocked` / validasi status) jika proyek sudah berstatus **Selesai / Lunas**.

* **Grafik & Visualisasi:** **ApexCharts** (`vue3-apexcharts` / `react-apexcharts`) untuk grafik arus kas bulanan dan komposisi sumber order.

* **PDF Generator:** `barryvdh/laravel-dompdf` (untuk generate Surat Kontrak Kerja Sama & Invoice otomatis).

* **File Storage:** Laravel Filesystem (`public` / S3-compatible seperti Cloudflare R2) untuk penyimpanan file upload invoice platform, bukti nota modal, dan bukti transfer.

### 4. Panduan Desain UI/UX (Clean White Modern Aesthetic)

AI Agent wajib menerapkan aturan desain visual berikut pada seluruh halaman aplikasi:

* **Tema Utama (Clean White):** Menggunakan latar belakang putih bersih (`bg-white` dan `bg-slate-50/70` untuk area kanvas utama) agar tampilan terlihat terang, lega, dan profesional.

* **Kartu & Kontainer:** Menggunakan latar putih murni (`bg-white`), sudut membulat modern (`rounded-2xl`), garis tepi halus (`border border-slate-200/80`), dan bayangan tipis (`shadow-2xs`).

* **Tipografi & Hierarki:** Font `Inter` / `Plus Jakarta Sans` dengan teks utama gelap tajam (`text-slate-900`) dan label sekunder abu-abu lembut (`text-slate-500`), serta penggunaan `font-mono` untuk angka nominal uang dan kode proyek.

* **Palet Warna Aksen Finansial (Soft Pastel Badges):**
  * **Net Profit (Cuan Bersih):** Emerald (`bg-emerald-50 text-emerald-700 border-emerald-200`)
  * **Nilai Kontrak (Gross):** Blue (`bg-blue-50 text-blue-700 border-blue-200`)
  * **Modal Keluar (AI/Hosting):** Amber (`bg-amber-50 text-amber-700 border-amber-200`)
  * **Piutang / Sisa Tagihan:** Violet (`bg-violet-50 text-violet-700 border-violet-200`)
  * **Potongan Platform:** Rose (`bg-rose-50 text-rose-700 border-rose-200`)

### 5. Struktur Basis Data MySQL (Database Schema & Relations)

Sistem ini terdiri dari 9 tabel utama pada database MySQL:

1. **`ProfilFreelancer`** (Pengaturan Identitas & Rekening Pemilik Sistem)

   * `Id` (`BIGINT UNSIGNED`, Auto Increment, PK)

   * `NamaLengkap` (`VARCHAR(150)`)

   * `NomorKtp` (`VARCHAR(50)`, Nullable)

   * `Alamat` (`TEXT`, Nullable)

   * `Email` (`VARCHAR(150)`)

   * `NomorWhatsapp` (`VARCHAR(30)`)

   * `NamaBank` (`VARCHAR(100)`)

   * `NomorRekening` (`VARCHAR(50)`)

   * `AtasNamaRekening` (`VARCHAR(150)`)

   * `TandaTanganPath` (`VARCHAR(255)`, Nullable — gambar scan TTD untuk PDF)

   * `created_at`, `updated_at` (`TIMESTAMP`)

2. **`SumberOrder`** (Master)

   * `Id` (`BIGINT UNSIGNED`, Auto Increment, PK)

   * `NamaSumber` (`VARCHAR(100)`: *WhatsApp Direct, Fiverr, Upwork, Fastwork, Projects.co.id*)

   * `PersentasePotonganDefault` (`DECIMAL(5,2)`: default `0.00`)

   * `ApakahPlatform` (`TINYINT(1)` / Boolean: `1` jika platform, `0` jika direct)

   * `Aktif` (`TINYINT(1)` / Boolean)

   * `created_at`, `updated_at`, `deleted_at` (`TIMESTAMP`)

3. **`MasterKategoriModal`** (Master)

   * `Id` (`BIGINT UNSIGNED`, Auto Increment, PK)

   * `NamaKategori` (`VARCHAR(100)`: *AI Tools & API Token, Hosting & Domain, Plugin/Asset, Subkontraktor, Operasional*)

   * `Keterangan` (`TEXT`, Nullable)

   * `created_at`, `updated_at`, `deleted_at` (`TIMESTAMP`)

4. **`MasterStatusProyek`** (Master)

   * `Id` (`BIGINT UNSIGNED`, Auto Increment, PK)

   * `NamaStatus` (`VARCHAR(100)`: *Negosiasi / Lead, DP Masuk, On Progress, Revisi Klien, Selesai / Lunas, Batal*)

   * `WarnaLabel` (`VARCHAR(50)`: Tailwind badge variant/hex color, misal `emerald`, `amber`, `blue`, `rose`)

   * `Urutan` (`INT`)

   * `created_at`, `updated_at`, `deleted_at` (`TIMESTAMP`)

5. **`Klien`** (Data Klien)

   * `Id` (`BIGINT UNSIGNED`, Auto Increment, PK)

   * `NamaKlien` (`VARCHAR(150)`)

   * `NamaPerusahaan` (`VARCHAR(150)`, Nullable)

   * `NomorWhatsapp` (`VARCHAR(30)`, Nullable)

   * `Email` (`VARCHAR(150)`, Nullable)

   * `Alamat` (`TEXT`, Nullable — dibutuhkan untuk kop Surat Kontrak & Invoice)

   * `Catatan` (`TEXT`, Nullable)

   * `created_at`, `updated_at`, `deleted_at` (`TIMESTAMP`)

6. **`Proyek`** (Tabel Utama Transaksi)

   * `Id` (`BIGINT UNSIGNED`, Auto Increment, PK)

   * `KodeProyek` (`VARCHAR(50)`, Unique: misal `PRJ-202611-001`)

   * `KlienId` (`BIGINT UNSIGNED`, FK -> `Klien.Id`, `ON DELETE CASCADE`)

   * `SumberOrderId` (`BIGINT UNSIGNED`, FK -> `SumberOrder.Id`)

   * `StatusId` (`BIGINT UNSIGNED`, FK -> `MasterStatusProyek.Id`)

   * `JudulProyek` (`VARCHAR(200)`)

   * `Deskripsi` (`TEXT`, Nullable — ruang lingkup pekerjaan untuk isi pasal kontrak)

   * `TechStack` (`VARCHAR(255)`, Nullable: misal *Laravel, Vue, Tailwind, MySQL*)

   * `UrlProduksi` (`VARCHAR(255)`, Nullable)

   * `UrlRepository` (`VARCHAR(255)`, Nullable)

   * `TanggalMulai` (`DATE`)

   * `TanggalDeadline` (`DATE`, Nullable)

   * `TanggalSelesai` (`DATE`, Nullable)

   * `MataUang` (`VARCHAR(10)`, Default `'IDR'`)

   * `NilaiKotor` (`DECIMAL(15,2)`: Harga deal kontrak awal)

   * `PersentasePotonganPlatform` (`DECIMAL(5,2)`)

   * `NominalPotonganPlatform` (`DECIMAL(15,2)`)

   * `NilaiBersihKontrak` (`DECIMAL(15,2)`: `NilaiKotor - NominalPotonganPlatform`)

   * `NomorKontrak` (`VARCHAR(100)`, Unique, Nullable — nomor permanen surat kontrak)

   * `created_at`, `updated_at`, `deleted_at` (`TIMESTAMP`)

7. **`InvoiceProyek`** (Manajemen Dokumen Invoice: Otomatis Sistem atau Upload dari Platform)

   * `Id` (`BIGINT UNSIGNED`, Auto Increment, PK)

   * `ProyekId` (`BIGINT UNSIGNED`, FK -> `Proyek.Id`, `ON DELETE CASCADE`)

   * `TipeInvoice` (`ENUM('OTOMATIS', 'UPLOAD_PLATFORM')` — membedakan invoice generate sistem vs invoice bawaan Fastwork/Upwork)

   * `NomorInvoice` (`VARCHAR(100)`, Unique — nomor permanen sistem misal `INV/PRJ-202611-001/01` atau nomor invoice dari Fastwork)

   * `TanggalTerbit` (`DATE`)

   * `TanggalJatuhTempo` (`DATE`, Nullable)

   * `NominalTagihan` (`DECIMAL(15,2)`)

   * `FileInvoicePath` (`VARCHAR(255)`, Nullable — wajib terisi jika `TipeInvoice = 'UPLOAD_PLATFORM'`, format PDF/JPG/PNG)

   * `Catatan` (`TEXT`, Nullable)

   * `created_at`, `updated_at`, `deleted_at` (`TIMESTAMP`)

8. **`ModalProyek`** (Pencatatan Pengeluaran / Modal per Proyek)

   * `Id` (`BIGINT UNSIGNED`, Auto Increment, PK)

   * `ProyekId` (`BIGINT UNSIGNED`, FK -> `Proyek.Id`, `ON DELETE CASCADE`)

   * `KategoriModalId` (`BIGINT UNSIGNED`, FK -> `MasterKategoriModal.Id`)

   * `NamaItem` (`VARCHAR(200)`: misal *Langganan Claude Pro 1 Bulan*, *VPS DigitalOcean*)

   * `JumlahBiaya` (`DECIMAL(15,2)`)

   * `TanggalPengeluaran` (`DATE`)

   * `BuktiNotaPath` (`VARCHAR(255)`, Nullable)

   * `Catatan` (`TEXT`, Nullable)

   * `created_at`, `updated_at`, `deleted_at` (`TIMESTAMP`)

9. **`PembayaranProyek`** (Pencatatan Termin Pembayaran dari Klien)

   * `Id` (`BIGINT UNSIGNED`, Auto Increment, PK)

   * `ProyekId` (`BIGINT UNSIGNED`, FK -> `Proyek.Id`, `ON DELETE CASCADE`)

   * `TahapanPembayaran` (`VARCHAR(100)`: *DP 50%, Termin 1, Pelunasan*)

   * `JumlahBayar` (`DECIMAL(15,2)`)

   * `TanggalBayar` (`DATE`)

   * `MetodePembayaran` (`VARCHAR(100)`, Nullable: *Transfer BCA, Saldo Fastwork, Wise*)

   * `BuktiTransferPath` (`VARCHAR(255)`, Nullable)

   * `created_at`, `updated_at`, `deleted_at` (`TIMESTAMP`)

### 6. Logika Bisnis & Kalkulasi Otomatis (Business Logic & Accessors)

AI Agent harus mengimplementasikan perhitungan finansial di dalam **Model `Proyek`** menggunakan Laravel Accessor dan menyertakannya dalam `$appends` agar otomatis terkirim sebagai *props* JSON ke komponen Vue/React via Inertia:

* **Total Modal (`total_modal`):** `SUM(ModalProyek.JumlahBiaya)` untuk proyek terkait.

* **Laba Bersih (`laba_bersih`):** `NilaiBersihKontrak - total_modal`.

* **Margin Keuntungan (`margin_keuntungan`):** `(laba_bersih / NilaiKotor) * 100` (dalam bentuk persentase).

* **Total Dibayar (`total_dibayar`):** `SUM(PembayaranProyek.JumlahBayar)`.

* **Sisa Tagihan / Piutang (`sisa_tagihan`):** `MAX(0, NilaiBersihKontrak - total_dibayar)`.

* **Kunci Audit (`apakah_terkunci`):** Mengembalikan `true` jika `status.NamaStatus === 'Selesai / Lunas'` untuk mencegah perubahan nominal kontrak secara tidak sengaja (kecuali oleh admin).

### 7. Kebutuhan Fitur Utama & Antarmuka SPA (Functional & UI/UX Requirements)

1. **Layout & Navigasi SPA Modern (Clean White):**

   * Menggunakan *Sidebar* putih bersih (`bg-white border-r border-slate-200`) yang dapat di-*collapse*, *Top Header* dengan efek *glassmorphism* (`bg-white/80 backdrop-blur-md`), serta *Command Palette* (`Cmd+K` / `Ctrl+K`) untuk pencarian cepat proyek/klien.

   * Notifikasi menggunakan *Toast* modern (Sonner / Vue Toastification) saat aksi simpan, update, atau *soft delete* berhasil.

2. **Dashboard Utama (`/dashboard`):**

   * **4 Kartu KPI Putih Minimalis:** Net Profit (Cuan Bersih), Total Kontrak Kotor vs Potongan Platform, Total Modal Keluar (AI/Hosting), Sisa Piutang Klien.

   * **Grafik Arus Kas (ApexCharts Area):** Perbandingan bulanan antara Pendapatan Bersih, Modal, dan Net Profit dengan kurva halus dan tema warna terang.

   * **Grafik Komposisi (ApexCharts Donut):** Perbandingan sumber order (WhatsApp vs Platform Freelance).

   * **Tabel Proyek Aktif:** Menampilkan progress pembayaran, margin keuntungan, dan indikator deadline.

3. **Form Input & Detail Proyek (`/proyek/create` & `/proyek/{id}`):**

   * **Kalkulator Potongan Reaktif (Vue/React State):** Menggunakan *reactive computed state*—saat opsi *Sumber Order* dipilih (misal: *Fastwork* `15%` atau *Fiverr* `20%`), nilai persentase potongan otomatis terisi, dan nilai bersih kontrak terhitung secara *real-time* tanpa reload halaman.

   * **Tab Detail Proyek (Shadcn Tabs):** Memuat tab interaktif untuk:

     * **Dokumen & Invoice:** Mengelola Surat Kontrak dan Daftar Invoice (Otomatis maupun Upload via *Drag-and-Drop* file).

     * **Daftar Modal:** Pencatatan pengeluaran AI/Hosting/Domain beserta preview bukti nota dalam Modal/Dialog.

     * **Riwayat Pembayaran:** Pencatatan termin masuk dari klien.

4. **Modul Hybrid Invoice & Kontrak Kerja Sama:**

   * **Surat Perjanjian Kerja Sama (SPK) (`/proyek/{id}/kontrak-pdf`):**

     * Nomor surat disimpan permanen di kolom `NomorKontrak` saat pertama kali dibuat.

     * Mengambil data otomatis dari `Proyek`, `Klien`, dan `ProfilFreelancer`, lengkap dengan pasal lingkup pekerjaan, biaya, timeline, dan kolom tanda tangan.

   * **Manajemen Invoice Hybrid (`/proyek/{id}/invoice`):**

     * **Opsi A — Generate Otomatis Sistem (`TipeInvoice = 'OTOMATIS'`):** Digunakan untuk order *WhatsApp Direct*. Sistem membuat nomor invoice permanen, menyimpan record di tabel `InvoiceProyek`, dan menyediakan tombol cetak/unduh PDF via `dompdf` lengkap dengan rekening bank freelancer.

     * **Opsi B — Upload Invoice Bawaan Platform (`TipeInvoice = 'UPLOAD_PLATFORM'`):** Digunakan untuk order dari platform seperti *Fastwork* atau *Upwork* yang sudah menerbitkan invoice resmi. User dapat mengunggah file PDF/gambar invoice dari platform tersebut, menginput nomor invoice & nominalnya agar tetap tercatat rapi dalam arsip proyek.

5. **Modul Data Master & Pengaturan Profil:**

   * CRUD Data Master (Sumber Order, Kategori Modal, Status Proyek, Klien) menggunakan *Modal Dialog / Slide-over Drawer* agar alur kerja cepat tanpa berpindah banyak halaman.

   * Halaman Pengaturan Profil Freelancer (`/pengaturan/profil`) untuk mengisi identitas, nomor rekening bank, dan upload gambar tanda tangan digital.

### 8. Struktur Routing yang Diharapkan (`routes/web.php`)

Seluruh rute dibungkus dalam `Route::middleware(['auth'])->group(...)` dan merender komponen Inertia (`Inertia::render(...)`):

* `GET /dashboard` -> `DashboardController@index`

* `Resource /proyek` -> `ProyekController`

* `POST /proyek/{id}/invoice/generate` -> `InvoiceProyekController@generateOtomatis`

* `POST /proyek/{id}/invoice/upload` -> `InvoiceProyekController@uploadPlatform`

* `GET /invoice/{invoiceId}/download` -> `InvoiceProyekController@downloadPdf`

* `GET /proyek/{id}/kontrak-pdf` -> `PdfController@cetakKontrak`

* `POST /proyek/{id}/modal` -> `ModalProyekController@store`

* `POST /proyek/{id}/pembayaran` -> `PembayaranProyekController@store`

* `Resource /master/sumber-order`, `/master/kategori-modal`, `/master/status-proyek`, `/klien`

* `GET /pengaturan/profil` & `PUT /pengaturan/profil` -> `ProfilFreelancerController`