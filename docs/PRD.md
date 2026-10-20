# Product Requirement Document (PRD)
## Sistem Informasi Manajemen Aset & Pemeliharaan Terpadu (SIM Aset)

---

## 1. Ringkasan Dokumen (Executive Summary)

### 1.1 Latar Belakang
Pengelolaan aset fisik dalam suatu institusi atau organisasi sering menghadapi kendala pencatatan manual, sulitnya memantau mutasi lokasi aset, keterlambatan penanganan pemeliharaan (maintenance), serta minimnya riwayat audit penggunaan aset. Untuk mengatasi kendala tersebut, dibangun **Sistem Informasi Manajemen Aset & Pemeliharaan Terpadu** yang memfasilitasi siklus hidup aset (asset lifecycle) mulai dari registrasi aset, peminjaman, mutasi lokasi dengan mekanisme persetujuan (approval), pemeliharaan berkala, hingga pelaporan dan analisis prediktif berbasis AI.

### 1.2 Tujuan Produk
1. **Sentralisasi Data Aset**: Menyediakan satu sumber kebenaran (*single source of truth*) untuk seluruh aset organisasi, lokasi, kategori, dan vendor.
2. **Akuntabilitas & Audit Trail**: Merekam setiap perpindahan, peminjaman, dan tindakan pemeliharaan secara transparan dan otomatis.
3. **Efisiensi Alur Kerja (Workflow)**: Menerapkan alur persetujuan digital untuk mutasi aset antara Staff dan Pimpinan.
4. **Chatbot Asisten Pintar yang Ringan & Cepat**: Mengintegrasikan chatbot AI berbasis **Gemini Flash 3.8 Low melalui Antigravity 9router API**, menggantikan infrastruktur lokal terdahulu (Ollama & Agno) guna menghemat konsumsi memori (RAM), mempercepat inferensi, dan mempermudah deployment tanpa perlu menjalankan microservice Python tambahan di server.

---

## 2. Arsitektur Sistem & Perubahan Strategis

### 2.1 Tech Stack Terkini
- **Backend Framework**: Laravel 11.x (PHP 8.2+)
- **Database Engine**: PostgreSQL 14+
- **Frontend / UI**: Laravel Blade Templates, Tailwind CSS / Vanilla UI Components, Chart.js / SVG Visualizer, Vite Asset Bundler
- **Session & Queue**: Database Driver (`sessions`, `jobs`)
- **LLM Provider & Router**: **Antigravity 9router API** dengan model **`gemini-3.8-flash-low`** (menggantikan Ollama `llama3.2:3b` & Python Agno microservice)

### 2.2 Perubahan Desain Arsitektur AI: Dari Ollama/Agno ke 9router (Gemini Flash 3.8 Low)

```
[ ARSITEKTUR SEBELUMNYA (DEPRECATED) ]
User Browser ---> Laravel (PHP) ---> HTTP (port 8001) ---> FastAPI / Python Agno ---> Ollama Local Daemon (Heavy RAM, Slow)

[ ARSITEKTUR BARU (PRD INI) ]
User Browser ---> Laravel Controller (PHP) ---> Direct HTTPS ---> 9router Gateway API ---> Gemini Flash 3.8 Low (Ultra-fast, Zero Server RAM Overhead)
```

#### Alasan & Keuntungan Transisi:
1. **Zero Server Memory Overhead**: Menghilangkan kebutuhan menjalankan daemon Ollama lokal dan model 3B+ yang memakan RAM 4GB–8GB di server produksi/lokal.
2. **Eliminasi Microservice Python**: Tidak perlu memelihara environment Python terpisah (`ai-service/`, `.venv`, Uvicorn, FastAPI), sehingga seluruh logika aplikasi terpusat di dalam framework Laravel.
3. **Latensi Rendah & Kapasitas Konteks Luas**: Gemini Flash 3.8 Low melalui 9router memiliki kecepatan respons tinggi dan penalaran kuat untuk membaca data dump/ringkasan aset berukuran menengah hingga besar tanpa truncation.
4. **Arsitektur Lebih Sederhana & Stabil**: Koneksi HTTP langsung dari Laravel menggunakan facade `Http::withToken(...)` dengan penanganan fallback yang terukur.

---

## 3. Matriks Pengguna & Hak Akses (Role-Based Access Control)

Sistem mendukung 4 (empat) peran pengguna (*user roles*):

| Hak Akses / Modul | Admin | Staff | Pimpinan | Peminjam (User) |
|---|:---:|:---:|:---:|:---:|
| **Dashboard Statistik & Grafik** | Ya | Tidak | Ya | Tidak |
| **Manajemen Akun & Role Pengguna** | CRUD | Tidak | Tidak | Tidak |
| **Pengaturan Sistem (Identitas/Kode Aset)** | CRUD | Tidak | Tidak | Tidak |
| **Master Data (Kategori, Lokasi, Vendor)** | CRUD | Read-Only | Tidak | Tidak |
| **Manajemen Data Aset** | CRUD | CRUD | Read-Only | Read-Only |
| **Manajemen Catatan Pemeliharaan** | CRUD | CRUD | Read-Only | Tidak |
| **Pengajuan Mutasi / Transfer Aset** | Buat & Lihat | Buat & Lihat | Tidak | Tidak |
| **Persetujuan (Approval) Mutasi Aset** | Ya | Tidak | Approve/Reject | Tidak |
| **Modul Peminjaman & Pengembalian Aset** | Monitor | Monitor | Monitor | Ajukan & Kembalikan |
| **Audit Trail & Riwayat Aset** | Lihat | Lihat | Lihat | Tidak |
| **Laporan & Ekspor Data (PDF/Excel)** | Ya | Tidak | Ya | Tidak |
| **Chatbot Asisten AI (Gemini Flash 3.8)** | Ya | Ya | Ya | Tidak |

*Catatan Pendaftaran:* Registrasi mandiri via `/register` menetapkan role default sebagai **`peminjam`**. Promosi ke role `admin`, `staff`, atau `pimpinan` hanya dapat dilakukan oleh **`admin`** melalui halaman `/users`.

---

## 4. Spesifikasi Fungsional Modul

### 4.1 Modul Autentikasi & Profil Pengguna
- **F-AUTH-01 (Login)**: Pengguna login menggunakan email dan kata sandi terenkripsi (Bcrypt). Sesi disimpan pada database (`sessions`).
- **F-AUTH-02 (Register)**: Pendaftaran publik pengguna baru dengan validasi nama unik, email valid, konfirmasi kata sandi minimal 8 karakter, dan penetapan role default `peminjam`.
- **F-AUTH-03 (Role Redirection)**: Setelah login berhasil, pengguna diarahkan ke beranda sesuai hak akses:
  - Admin & Pimpinan &rarr; `/dashboard`
  - Staff &rarr; `/assets`
  - Peminjam &rarr; `/borrowings`
- **F-AUTH-04 (Lupa Kata Sandi)**: Reset sandi berbasis token ke email yang terdaftar.
- **F-AUTH-05 (Logout)**: Invalidation sesi dan regenerasi CSRF token secara aman.

### 4.2 Modul Dashboard Analitik (Admin & Pimpinan)
- **F-DASH-01 (Statistik Ringkas)**:
  - Total aset, aset berstatus aktif, aset sedang dalam maintenance, dan aset berstatus rusak.
  - Total transaksi pemeliharaan, pemeliharaan terjadwal, dalam proses, dan selesai.
  - Jumlah pengajuan mutasi aset yang menunggu persetujuan (*Pending Approvals*).
- **F-DASH-02 (Visualisasi Grafik)**:
  - Distribusi jumlah aset per kategori (Bar/Pie Chart).
  - Proporsi status aset (Aktif, Dipinjam, Maintenance, Rusak, Dihapus).
- **F-DASH-03 (Feed Terkini)**: Daftar 5 aset terbaru yang ditambahkan dan 5 riwayat pemeliharaan terbaru.

### 4.3 Modul Master Data (Kategori, Lokasi, Vendor)
- **F-MST-01 (Kategori)**: Pengelompokan jenis aset (`nama`, `deskripsi`). Relasi dilindungi (*restrict on delete*) jika kategori masih menaungi aset.
- **F-MST-02 (Lokasi)**: Detail fisik penempatan aset (`nama_lokasi`, `gedung`, `lantai`, `ruangan`, `deskripsi`).
- **F-MST-03 (Vendor)**: Data rekanan pengadaan/reparasi aset (`nama_vendor`, `kontak_telepon`, `email`, `alamat`).

### 4.4 Modul Manajemen Data Aset
- **F-AST-01 (Auto-Generate Kode Aset)**: Penomoran otomatis kode aset berbasis konfigurasi pada `system_settings` (contoh: `{prefix}-{seq}`, default: `AST-GIN-0001`).
- **F-AST-02 (Pencatatan Detail Aset)**:
  - Nama aset, merk, tipe, kategori, lokasi saat ini, vendor rekanan.
  - Tanggal perolehan, harga pembelian (format mata uang IDR), penanggung jawab (*person-in-charge*).
  - Kondisi aset: `Baik`, `Rusak Ringan`, `Rusak Berat`.
  - Status operasional: `Aktif`, `Dipinjam`, `Maintenance`, `Rusak`, `Dihapus`.
- **F-AST-03 (Filter & Pencarian)**: Pencarian cepat berdasarkan nama aset, kode aset, lokasi, atau kategori.
- **F-AST-04 (Sinkronisasi Audit)**: Setiap penambahan atau perubahan status aset otomatis membuat rekaman log pada `asset_histories`.

### 4.5 Modul Mutasi / Transfer Lokasi Aset & Persetujuan (Approval)
- **F-TRF-01 (Pengajuan Mutasi)**: Staff atau Admin dapat mengajukan perpindahan aset dari lokasi asal ke lokasi tujuan disertai alasan pemindahan (`reason`). Status awal: `menunggu`.
- **F-TRF-02 (Approval oleh Pimpinan)**: Pimpinan meninjau pengajuan mutasi pada halaman `/approvals`:
  - **Setuju (Approve)**: Memperbarui lokasi aset ke `target_location_id`, mengubah status transfer menjadi `disetujui`, mencatat `decided_by`, `decided_at`, dan menghasilkan riwayat mutasi pada `asset_histories`.
  - **Tolak (Reject)**: Mengubah status menjadi `ditolak` dengan input wajib catatan penolakan (`decision_note`). Lokasi aset tidak berubah.

### 4.6 Modul Peminjaman & Pengembalian Aset (Asset Loans)
- **F-LOAN-01 (Pengajuan Pinjam)**: Pengguna ber-role `peminjam` dapat memilih aset yang berstatus `Aktif` dan mengajukan peminjaman dengan mengisi keperluan, tanggal pinjam, dan rencana tanggal kembali.
- **F-LOAN-02 (Perubahan Status Aset)**: Saat peminjaman disetujui/aktif, status aset berubah menjadi `Dipinjam`.
- **F-LOAN-03 (Pengembalian Aset)**: Peminjam atau staf memproses pengembalian dengan mencatat kondisi sebelum vs kondisi sesudah, tanggal riil pengembalian, serta catatan tambahan. Status aset kembali ke `Aktif` (atau `Maintenance`/`Rusak` jika terjadi kerusakan).

### 4.7 Modul Pemeliharaan (Maintenance Records)
- **F-MNT-01 (Pencatatan Jadwal & Masalah)**: Mencatat tanggal servis, keluhan teknis, jenis pemeliharaan (Preventif / Korektif / Perbaikan), teknisi penanggung jawab, serta estimasi biaya.
- **F-MNT-02 (Proses & Hasil Servis)**: Memperbarui progres pemeliharaan (`Terjadwal` &rarr; `Proses` &rarr; `Selesai` / `Dibatalkan`), mencatat detail kerusakan, hasil tindakan perbaikan, dan kondisi sesudah servis.
- **F-MNT-03 (Integrasi Biaya & Riwayat)**: Akumulasi biaya maintenance dan pencatatan otomatis ke `asset_histories`.

### 4.8 Modul Pengaturan Sistem (System Settings)
- **F-SET-01 (Profil Instansi)**: Pengaturan nama instansi, unit organisasi, logo resmi, zona waktu sistem (`Asia/Jakarta`), dan format tanggal.
- **F-SET-02 (Aturan Penomoran Aset)**: Penentuan prefix kode aset (e.g. `AST-GIN`), karakter pemisah (`-`), jumlah digit padding (e.g. 4 digit), dan counter angka berikutnya.
- **F-SET-03 (Reset Pengaturan)**: Opsi mengembalikan konfigurasi ke setelan default pabrik.

### 4.9 Modul Laporan & Ekspor
- **F-REP-01 (Rekapitulasi Data)**: Laporan komprehensif aset, distribusi per kategori/lokasi, laporan pemeliharaan beserta total realisasi biaya, dan rekapitulasi mutasi/peminjaman.
- **F-REP-02 (Ekspor Multi-Format)**: Ekspor dokumen cetak / PDF dan lembar kerja Excel / CSV.

---

## 5. Modul Chatbot AI & Predictive Assistant (Gemini Flash 3.8 Low via Antigravity API)

### 5.1 Spesifikasi Integrasi LLM
- **Nama Gateway**: Antigravity Router API
- **Model Identifier**: `ag/gemini-3.8-flash-low`
- **Base Endpoint**: `http://localhost:20128/v1`
- **Tipe Komunikasi**: HTTP REST POST (Server-to-Server via Laravel `Http` Client)
- **Format Pertukaran Data**: JSON OpenAI-compatible (`/chat/completions`) dengan `stream: false`
- **Waktu Maksimal (Timeout)**: 60 detik

### 5.2 Konfigurasi Lingkungan (`.env`)
```dotenv
# Konfigurasi Antigravity LLM API Router
ANTIGRAVITY_API_KEY=sk-a9a37f311b1527fb-r3j1ok-6dcdb951
ANTIGRAVITY_BASE_URL=http://localhost:20128/v1
ANTIGRAVITY_MODEL=ag/gemini-3.8-flash-low
ANTIGRAVITY_TIMEOUT=60
```

### 5.3 Knowledge Base Chatbot: Dataset Mesin & Fitur Sistem Web
Chatbot dilengkapi dengan 3 pilar knowledge komprehensif:
1. **Knowledge Dataset Predictive Maintenance (`dataset.csv`)**:
   - 10.000 rekaman operasional mesin pabrik (tipe L, M, H).
   - Analisis & rumus fisika 5 modus kegagalan: Tool Wear Failure (TWF), Heat Dissipation Failure (HDF), Power Failure (PWF), Overstrain Failure (OSF), dan Random Failure (RNF).
   - Dynamic Product ID lookup (misal: `L47230`, `M14860`, `H29424`) yang secara langsung membaca parameter telemetri riil dari file CSV.
   - Evaluasi anomali sensor (RPM, torsi, temperatur, tool wear) berbasis formula daya mekanik dan batas regangan.
2. **Knowledge Dummy Sistem & Fitur Website (SIM Aset)**:
   - Panduan alur seluruh menu aplikasi: Master Data (Kategori, Lokasi, Vendor), Manajemen Aset, Peminjaman Aset (Loans), Mutasi Aset & Persetujuan Pimpinan (`/approvals`), Maintenance Records, Riwayat Aset, Laporan & Ekspor, serta Pengaturan Sistem.
   - Matriks peran pengguna: Admin, Staff, Pimpinan, dan Peminjam.
   - Data dummy mesin pabrik yang dikorelasikan dengan inventaris aset di database.
   - Standar Operasional Prosedur (SOP) penanganan mesin rusak, pengajuan mutasi lokasi, dan peminjaman.
3. **Live Database Context**:
   - Data ringkasan aktual dari PostgreSQL (total aset, status aktif/rusak/maintenance, tiket servis bulan berjalan, dan mutasi pending approval).

### 5.4 Alur Kerja Chatbot (Context-Augmented Generation)
1. **Penerimaan Prompt**: Pengguna memasukkan pertanyaan di halaman `/ai`.
2. **Kompilasi Konteks Otomatis (`AiKnowledgeService`)**:
   - Melakukan deteksi Product ID / tipe kegagalan pada prompt dan mencari baris terkait di `dataset.csv`.
   - Mengambil ringkasan live dari PostgreSQL dan memadukannya dengan knowledge SOP/fitur web.
3. **Pengiriman Permintaan**:
   Laravel memanggil endpoint `POST http://localhost:20128/v1/chat/completions` membawa payload:
   ```json
   {
     "model": "ag/gemini-3.8-flash-low",
     "stream": false,
     "messages": [
       { "role": "system", "content": "Knowledge base komprehensif (dataset + fitur web + live DB)" },
       { "role": "user", "content": "Pertanyaan pengguna" }
     ],
     "temperature": 0.3,
     "max_tokens": 2048
   }
   ```
4. **Penyajian Respons**:
   Jawaban teks di-render menggunakan parser Markdown resmi Laravel (`Str::markdown()`) sehingga tabel, rumus, poin bullet, dan format kode tampil rapi di layar chat.
6. **Error Handling & Fallback**:
   Jika kuota habis atau jaringan terputus, sistem menangkap exception dan menampilkan pesan ramah: *"Layanan asisten AI sedang mengalami kendala koneksi ke 9router gateway. Silakan coba sesaat lagi."* tanpa membuat aplikasi crash.

---

## 6. Desain Database & Kamus Data (Data Dictionary)

### 6.1 Diagram Relasi Entitas Konseptual (ERD)
```
[users] 1 ----- * [transfers] (requested_by, decided_by)
[users] 1 ----- * [asset_loans]
[users] 1 ----- * [asset_histories]

[categories] 1 --- * [assets]
[locations]  1 --- * [assets]
[vendors]    1 --- * [assets]

[assets] 1 ----- * [maintenance_records]
[assets] 1 ----- * [transfers]
[assets] 1 ----- * [asset_loans]
[assets] 1 ----- * [asset_histories]
[assets] 1 ----- * [ai_predictions]

[system_settings] (Tabel Konfigurasi Tunggal)
```

### 6.2 Rincian Tabel Utama

#### 1. Tabel `users`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT PK | Auto increment |
| `name` | VARCHAR(255) | Nama lengkap pengguna |
| `email` | VARCHAR(255) | Unik, alamat email login |
| `password` | VARCHAR(255) | Hash kata sandi (Bcrypt) |
| `role` | VARCHAR(30) | `admin`, `staff`, `pimpinan`, `peminjam` (default: `peminjam`) |
| `remember_token` | VARCHAR(100) | Remember me token |
| `created_at`, `updated_at` | TIMESTAMP | Audit waktu |

#### 2. Tabel `categories`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT PK | Auto increment |
| `nama` | VARCHAR(255) | Nama kategori (mis. Elektronik, Kendaraan, Mebel) |
| `deskripsi` | TEXT (nullable) | Keterangan kategori |

#### 3. Tabel `locations`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT PK | Auto increment |
| `nama` | VARCHAR(255) | Nama lokasi umum (mis. Lab Komputer 1) |
| `gedung` | VARCHAR(255) | Nama/nomor gedung |
| `lantai` | VARCHAR(255) | Nomor/keterangan lantai |
| `ruangan` | VARCHAR(255) | Nama/nomor ruangan spesifik |
| `deskripsi` | TEXT (nullable) | Detail tambahan lokasi |

#### 4. Tabel `vendors`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT PK | Auto increment |
| `nama` | VARCHAR(255) | Nama perusahaan / rekanan penyedia |
| `kontak` | VARCHAR(255) | Nomor telepon / seluler |
| `email` | VARCHAR(255) | Alamat email vendor |
| `alamat` | TEXT | Alamat operasional vendor |

#### 5. Tabel `assets`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT PK | Auto increment |
| `kode_asset` | VARCHAR(255) | Unik, kode inventaris (mis. `AST-GIN-0001`) |
| `nama_asset` | VARCHAR(255) | Nama/deskripsi barang |
| `category_id` | BIGINT FK | Relasi ke `categories.id` (`RESTRICT`) |
| `location_id` | BIGINT FK | Relasi ke `locations.id` (`SET NULL`) |
| `vendor_id` | BIGINT FK | Relasi ke `vendors.id` (`SET NULL`) |
| `merk` | VARCHAR(255) | Merk / brand pabrikan |
| `tipe` | VARCHAR(255) | Seri atau tipe unit |
| `tanggal_perolehan` | DATE | Tanggal pengadaan |
| `harga` | NUMERIC(15,2) | Nilai perolehan dalam Rupiah |
| `kondisi` | VARCHAR(255) | Check: `Baik`, `Rusak Ringan`, `Rusak Berat` |
| `status` | VARCHAR(255) | Check: `Aktif`, `Dipinjam`, `Maintenance`, `Rusak`, `Dihapus` |
| `penanggung_jawab` | VARCHAR(255) | PIC pengampu aset |
| `deskripsi` | TEXT (nullable) | Catatan spesifikasi teknis |

#### 6. Tabel `transfers`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT PK | Auto increment |
| `asset_id` | BIGINT FK | Relasi ke `assets.id` (`RESTRICT`) |
| `origin_location_id` | BIGINT FK | Lokasi awal (`locations.id`) |
| `target_location_id` | BIGINT FK | Lokasi tujuan (`locations.id`) |
| `requested_by` | BIGINT FK | Relasi ke `users.id` pemohon (Staff/Admin) |
| `decided_by` | BIGINT FK | Relasi ke `users.id` penyetujui (Pimpinan) |
| `reason` | VARCHAR(255) | Alasan mutasi lokasi |
| `status` | VARCHAR(20) | `menunggu`, `disetujui`, `ditolak` |
| `decision_note` | VARCHAR(255) | Catatan persetujuan / penolakan |
| `decided_at` | TIMESTAMP | Waktu eksekusi approval |

#### 7. Tabel `asset_loans`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT PK | Auto increment |
| `asset_id` | BIGINT FK | Relasi ke `assets.id` (`CASCADE`) |
| `user_id` | BIGINT FK | Relasi ke `users.id` peminjam (`SET NULL`) |
| `tanggal_pinjam` | DATE | Tanggal mulai pinjam |
| `tanggal_rencana_kembali` | DATE | Estimasi tanggal selesai pinjam |
| `tanggal_kembali` | DATE (nullable) | Tanggal aktual pengembalian |
| `keperluan` | TEXT | Tujuan peminjaman barang |
| `kondisi_sebelum` | VARCHAR(255) | Kondisi fisik sebelum diserahkan |
| `kondisi_sesudah` | VARCHAR(255) | Kondisi fisik saat dikembalikan |
| `status` | VARCHAR(255) | Check: `Diajukan`, `Dipinjam`, `Dikembalikan`, `Ditolak` |
| `catatan` | TEXT (nullable) | Catatan tambahan |

#### 8. Tabel `maintenance_records`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT PK | Auto increment |
| `asset_id` | BIGINT FK | Relasi ke `assets.id` (`CASCADE`) |
| `tanggal` | DATE | Tanggal pengerjaan servis |
| `keluhan` | TEXT | Deskripsi kendala/masalah |
| `jenis` | VARCHAR(255) | Rutin, Perbaikan, Kalibrasi, Pergantian Part |
| `kerusakan` | TEXT (nullable) | Analisa kerusakan teknis |
| `biaya` | NUMERIC(15,2) | Total biaya pengerjaan & sparepart |
| `teknisi` | VARCHAR(255) | Nama teknisi / bengkel rekanan |
| `hasil_tindakan` | TEXT (nullable) | Solusi yang telah diimplementasikan |
| `kondisi_sebelum` | VARCHAR(255) | Kondisi awal saat masuk bengkel |
| `kondisi_sesudah` | VARCHAR(255) | Kondisi akhir setelah pengerjaan |
| `status` | VARCHAR(255) | Check: `Terjadwal`, `Proses`, `Selesai`, `Dibatalkan` |

#### 9. Tabel `asset_histories`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT PK | Auto increment |
| `asset_id` | BIGINT FK | Relasi ke `assets.id` (`CASCADE`) |
| `user_id` | BIGINT FK | Pelaku tindakan (`users.id`, `SET NULL`) |
| `aktivitas` | VARCHAR(255) | Label aksi (mis. `Asset Dibuat`, `Mutasi Lokasi`, `Maintenance`) |
| `deskripsi` | TEXT | Keterangan detail perubahan data |
| `tanggal` | TIMESTAMP | Waktu pencatatan log (default: CURRENT_TIMESTAMP) |

#### 10. Tabel `system_settings`
| Kolom | Tipe Data | Keterangan |
|---|---|---|
| `id` | BIGINT PK | Auto increment |
| `organization_name` | VARCHAR(255) | Nama instansi/perusahaan |
| `organization_unit` | VARCHAR(255) | Divisi / unit penanggung jawab |
| `timezone` | VARCHAR(255) | Default: `Asia/Jakarta (GMT+07:00)` |
| `date_format` | VARCHAR(255) | Format tampilan tanggal (e.g. `DD/MM/YYYY`) |
| `asset_prefix` | VARCHAR(20) | Prefix kode aset (e.g. `AST-GIN`) |
| `asset_separator` | VARCHAR(3) | Pemisah kode (e.g. `-`) |
| `asset_digits` | SMALLINT | Panjang digit running number (default: 4) |
| `asset_next_number` | INTEGER | Angka urut berikutnya untuk aset baru |
| `logo_path` | VARCHAR(255) | Path file logo instansi |
| `email_notifications` | BOOLEAN | Pengaktifan notifikasi email |

---

## 7. Rencana Kerja Implementasi Penggantian AI (Migration Plan)

```
[Langkah 1: Setup Kunci & Konfigurasi]
- Mendaftarkan token API 9router
- Menambahkan NINEROUTER_* pada file .env dan config/services.php

[Langkah 2: Refactor AiController & Service Layer]
- Menghapus ketergantungan HTTP ke localhost:8001 (ai-service)
- Membangun service class khusus (e.g. App\Services\NineRouterAiService)
- Mengirim pesan dengan format standar OpenAI/9router ke Gemini Flash 3.8 Low

[Langkah 3: Pembersihan Deprecated Stack]
- Menonaktifkan service FastAPI lokal (ai-service)
- Membersihkan konfigurasi AGNO_* di .env

[Langkah 4: Validasi & Uji Coba Chatbot]
- Pengujian tanya jawab status aset & pemeliharaan
- Pengujian toleransi kesalahan (error handling jika koneksi timeout)
```

---

## 8. Persyaratan Non-Fungsional (Non-Functional Requirements)

1. **Keamanan (Security)**:
   - Proteksi CSRF di seluruh form request POST, PUT, dan DELETE.
   - Hashing password menggunakan algoritma Bcrypt dengan cost parameter 12.
   - API Key 9router tersimpan secara aman di `.env` di luar kontrol versi Git.
2. **Kinerja & Responsivitas (Performance)**:
   - Waktu respons query database di bawah 150ms untuk pencarian inventaris.
   - Chatbot AI memberikan respons dalam 1–3 detik menggunakan Gemini Flash 3.8 Low via 9router.
3. **Ketersediaan & Reliabilitas (Reliability)**:
   - Apabila koneksi LLM mengalami gangguan, operasional inti sistem manajemen aset (CRUD, Mutasi, Peminjaman) tetap berjalan 100% normal tanpa hambatan.
4. **Skalabilitas (Scalability)**:
   - Mampu menangani hingga puluhan ribu rekaman aset dengan pengindeksan foreign key dan unique constraint PostgreSQL yang tepat.

---
*Dokumen PRD ini menjadi acuan spesifikasi resmi untuk pengembangan dan implementasi sistem SIM Aset.*
