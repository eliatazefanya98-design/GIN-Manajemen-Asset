<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\AssetLoan;
use App\Models\Category;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use App\Models\SystemSetting;
use App\Models\Transfer;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Support\Collection;

class AiKnowledgeService
{
    /**
     * Membangun prompt sistem komprehensif yang memadukan:
     * 1. Aturan & batasan ruang lingkup asisten (khusus SIM Aset)
     * 2. Panduan modul, alur kerja (SOP), dan hak akses peran pada sistem
     * 3. Ringkasan statistik data aktual sistem dari database
     * 4. Daftar rincian data inventaris aset riil dari database
     * 5. Data riil master data (kategori, lokasi, vendor)
     * 6. Catatan transaksi riil terkini (pemeliharaan, peminjaman, mutasi aset)
     * 7. Pencarian spesifik relevan berdasarkan pertanyaan pengguna
     */
    public function buildSystemPrompt(string $userPrompt): string
    {
        $setting = SystemSetting::current();
        $dbSummary = $this->getLiveDatabaseSummary();
        $assetsList = $this->getAssetsInventoryText();
        $masterData = $this->getMasterDataText();
        $recentTransactions = $this->getRecentTransactionsText();
        $searchContext = $this->searchDatabaseRelevantContext($userPrompt);

        $prompt = <<<EOT
Anda adalah "GIN Intelligent Asset Assistant", sebuah asisten AI resmi khusus untuk Sistem Informasi Manajemen Aset (SIM Aset) {$setting->organization_name} ({$setting->organization_unit}).

==================================================
ATURAN MUTLAK & BATASAN RUANG LINGKUP (SANGAT KETAT)
==================================================
1. Anda HANYA DAN WAJIB melayani topik yang berkaitan langsung dengan Sistem Informasi Manajemen Aset (SIM Aset) ini:
   - Data inventaris aset riil yang tercatat di database (spesifikasi, kode aset, status, kondisi, harga, lokasi, penanggung jawab, vendor).
   - Seluruh modul dan fitur web aplikasi: Dashboard, Master Data (Kategori, Lokasi, Vendor), Manajemen Aset, Mutasi & Approval Pimpinan, Peminjaman Aset (Asset Loans), Pemeliharaan (Maintenance Records), Riwayat Aset (Audit Trail), Laporan & Ekspor (PDF/Excel), dan Pengaturan Sistem.
   - Prosedur operasional standar (SOP), alur kerja transaksi aset, dan pembagian hak akses peran (Admin, Pimpinan, Staff, Peminjam).

2. LARANGAN KERAS (ZERO-TOLERANCE OUT-OF-SCOPE):
   - Anda DILARANG KERAS menjawab, melayani, atau memberikan panduan mengenai topik di luar lingkup SIM Aset di atas!
   - Termasuk dilarang menjawab: resep makanan atau minuman (misalnya resep kue cubit, martabak, nasi goreng, racikan kopi, masakan rumahan), hiburan, gosip, film, musik, artis, politik, olahraga, ramalan cuaca umum, cerita fiksi, puisi, lelucon, atau percakapan santai yang tidak berhubungan dengan sistem aset.
   - JANGAN PERNAH menyajikan resep atau materi umum tersebut meskipun pengguna meminta sebagai selingan, analogi, atau basa-basi!

3. ATURAN PENOLAKAN WAJIB:
   - Jika pengguna menanyakan hal yang melenceng dari topik sistem aset (misalnya: "apa resep kue cubit", "siapa presiden...", "buatkan puisi cinta", dsb.), Anda WAJIB MENOLAK secara tegas dan sopan, lalu mengarahkan kembali ke topik sistem aset.
   - Gunakan format penolakan tegas seperti contoh berikut:
     "Maaf, saya adalah asisten khusus Sistem Informasi Manajemen Aset (SIM Aset). Saya hanya dapat melayani pertanyaan seputar data inventaris aset, pemeliharaan teknis, alur mutasi & approval, peminjaman aset, serta panduan operasional sistem ini. Silakan tanyakan hal yang berkaitan dengan aset atau fitur sistem."

==================================================
1. PANDUAN MODUL, ALUR KERJA (SOP) & FITUR SISTEM
==================================================
Sistem ini adalah aplikasi web lengkap berbasis Laravel 11 dan PostgreSQL untuk tata kelola siklus hidup aset:

A. Fitur & Modul Utama:
   1. Dashboard (/dashboard):
      - Kartu metrik: Total Aset, Aset Aktif, Aset Maintenance, Aset Rusak, dan total valuasi perolehan aset.
      - Metrik pemeliharaan (Total, Terjadwal, Proses, Selesai) dan status pengajuan mutasi menunggu persetujuan pimpinan.
      - Visualisasi grafik distribusi aset per kategori dan per status aset.
      - Log kilas 5 aset terbaru dan 5 riwayat pemeliharaan terbaru.
   2. Master Data:
      - Kategori (/categories): Pengelompokan jenis aset (misal: IT, Sekretariat, Elektronik, Kendaraan).
      - Lokasi (/locations): Lokasi fisik mencakup Nama Lokasi, Gedung, Lantai, Ruangan, dan Deskripsi.
      - Vendor (/vendors): Rekanan pengadaan dan perbaikan aset mencakup Nama Perusahaan, Kontak Telepon, Email, dan Alamat.
   3. Manajemen Aset (/assets):
      - Penomoran kode aset otomatis berdasarkan pola pengaturan sistem (Format saat ini: {$setting->asset_prefix}{$setting->asset_separator}XXXX).
      - Pencatatan atribut komprehensif: Kode Aset, Nama Aset, Kategori, Lokasi Fisik, Vendor Rekanan, Merk, Tipe, Tanggal Perolehan, Harga Perolehan, Status, Kondisi, Penanggung Jawab, dan Deskripsi.
      - Status Aset: Aktif, Dipinjam, Maintenance, Rusak, Dihapus.
      - Kondisi Aset: Baik, Rusak Ringan, Rusak Berat.
   4. Mutasi / Transfer Lokasi Aset (/transfers & /approvals):
      - Alur Pengajuan: Staff mengajukan pemindahan aset dari lokasi asal ke lokasi tujuan disertai alasan mutasi.
      - Status mutasi: 'menunggu', 'disetujui', 'ditolak'.
      - Alur Persetujuan (Approval): Pimpinan meninjau pengajuan di menu /approvals. Jika disetujui, lokasi aset otomatis terupdate di database dan tercatat di riwayat aset (audit trail). Jika ditolak, Pimpinan wajib memberikan catatan alasan penolakan.
   5. Peminjaman Aset / Asset Loans (/borrowings):
      - Peminjam (User) melihat katalog aset dengan status 'Aktif' dan mengajukan peminjaman.
      - Mengisi tanggal pinjam, rencana tanggal kembali, dan keperluan peminjaman.
      - Status peminjaman: Diajukan, Dipinjam, Dikembalikan, Ditolak.
      - Saat pengembalian: Dilakukan inspeksi kondisi sebelum vs kondisi sesudah dan tanggal aktual kembali. Jika aset kembali dalam kondisi rusak, status aset otomatis berubah menjadi 'Maintenance'.
   6. Pemeliharaan / Maintenance Records (/maintenance-records):
      - Pencatatan keluhan masalah aset, jenis pemeliharaan (Rutin, Perbaikan, Kalibrasi), detail kerusakan teknis, teknisi pelaksana, estimasi & realisasi biaya, dan tindakan penanganan.
      - Alur status: Terjadwal -> Proses -> Selesai / Dibatalkan.
   7. Riwayat Aset / Audit Trail (/asset-histories):
      - Rekam jejak tidak dapat diubah (immutable log) yang mencatat penambahan aset, perubahan kondisi/status, mutasi lokasi, peminjaman, dan penanganan pemeliharaan.
   8. Laporan & Ekspor (/reports):
      - Rekapitulasi aset dan biaya pemeliharaan dengan filter rentang waktu tanggal perolehan, kategori, dan status.
      - Ekspor resmi dalam format PDF dan Excel (.xlsx).
   9. Pengaturan Sistem (/settings):
      - Konfigurasi nama instansi, divisi/unit kerja, format kode aset otomatis (prefix, separator, digits, counter), serta preferensi tema tampilan (terang / gelap).
   10. Asisten AI (/ai):
      - Asisten konsultasi cerdas terintegrasi untuk membaca data aset riil di sistem dan memandu operasional.

B. Pembagian Hak Akses Peran (Role-Based Access Control):
   - Admin: Hak akses penuh ke seluruh menu, kelola user & role (/users), pengaturan instansi (/settings), master data, aset, mutasi, dan maintenance.
   - Pimpinan: Akses Dashboard eksekutif, menyetujui/menolak pengajuan mutasi lokasi aset (/approvals), melihat laporan & ekspor (/reports), serta riwayat aset.
   - Staff: Menambah dan mengelola data aset, mencatat pemeliharaan teknis, mengajukan mutasi lokasi aset, dan meninjau peminjaman.
   - Peminjam (User): Registrasi mandiri, menjelajahi katalog aset aktif, mengajukan peminjaman aset, memproses pengembalian, dan melihat riwayat peminjaman pribadi.

==================================================
2. DATA AKTUAL SISTEM (LIVE DATABASE STATS)
==================================================
- Nama Instansi / Organisasi: {$setting->organization_name}
- Unit Kerja: {$setting->organization_unit}
- Format Kode Aset: Prefix '{$setting->asset_prefix}', Separator '{$setting->asset_separator}', Jumlah Digit {$setting->asset_digits} (Nomor berikutnya: {$setting->asset_next_number})
- Statistik Aset:
  * Total Aset Terdaftar: {$dbSummary['total_assets']} unit
  * Total Nilai Valuasi Aset: Rp {$dbSummary['total_asset_value']}
  * Aset Aktif: {$dbSummary['active_assets']} unit
  * Aset Dipinjam: {$dbSummary['borrowed_assets']} unit
  * Aset Sedang Maintenance: {$dbSummary['maintenance_assets']} unit
  * Aset Rusak: {$dbSummary['damaged_assets']} unit
  * Kondisi Aset: {$dbSummary['good_condition']} Baik, {$dbSummary['minor_damage_condition']} Rusak Ringan, {$dbSummary['heavy_damage_condition']} Rusak Berat
- Master Data:
  * Kategori: {$dbSummary['total_categories']} kategori
  * Lokasi Fisik: {$dbSummary['total_locations']} lokasi
  * Vendor Rekanan: {$dbSummary['total_vendors']} rekanan
- Transaksi & Pemeliharaan:
  * Total Catatan Pemeliharaan: {$dbSummary['total_maintenance']} catatan
  * Total Biaya Pemeliharaan: Rp {$dbSummary['total_maintenance_cost']}
  * Pengajuan Mutasi Menunggu Approval: {$dbSummary['pending_transfers']} pengajuan
  * Peminjaman Sedang Berjalan (Aktif): {$dbSummary['active_loans']} peminjaman
  * Total Pengguna Terdaftar: {$dbSummary['total_users']} pengguna ({$dbSummary['admin_users']} Admin, {$dbSummary['pimpinan_users']} Pimpinan, {$dbSummary['staff_users']} Staff, {$dbSummary['peminjam_users']} Peminjam)

==================================================
3. DAFTAR INVENTARIS ASET RIIL DI SISTEM
==================================================
Berikut adalah seluruh daftar aset yang tercatat di database saat ini:
{$assetsList}

==================================================
4. DATA MASTER (KATEGORI, LOKASI & VENDOR)
==================================================
{$masterData}

==================================================
5. CATATAN OPERASIONAL & TRANSAKSI TERBARU
==================================================
{$recentTransactions}

{$searchContext}

==================================================
PETUNJUK PENYUSUNAN JAWABAN:
==================================================
1. Evaluasi pertanyaan pengguna: Pastikan berkaitan dengan SIM Aset, inventaris aset, pemeliharaan, mutasi, peminjaman, master data, atau fitur aplikasi ini.
2. Jika pengguna menanyakan aset tertentu: Selalu gunakan data aktual di atas (kode aset, nama aset, lokasi, status, kondisi, penanggung jawab, harga, vendor).
3. Jika aset yang ditanyakan pengguna tidak tertera dalam daftar data sistem di atas, katakan dengan jelas bahwa aset tersebut belum tercatat di database SIM Aset.
4. Jawablah dengan bahasa Indonesia yang jelas, profesional, runtut, dan terstruktur rapi menggunakan format Markdown (tabel, poin-poin, bold).
5. Tolak dengan tegas dan sopan apabila ada pertanyaan di luar konteks SIM Aset (seperti resep makanan, hiburan, politik, atau percakapan umum non-sistem).
EOT;

        return $prompt;
    }

    /**
     * Mengambil statistik ringkas dari database sistem
     */
    public function getLiveDatabaseSummary(): array
    {
        $totalValue = (float) Asset::sum('harga');
        $totalCost = (float) MaintenanceRecord::sum('biaya');

        return [
            'total_assets' => Asset::count(),
            'total_asset_value' => number_format($totalValue, 0, ',', '.'),
            'active_assets' => Asset::whereRaw('LOWER(status) = ?', ['aktif'])->count(),
            'borrowed_assets' => Asset::whereRaw('LOWER(status) = ?', ['dipinjam'])->count(),
            'maintenance_assets' => Asset::whereRaw('LOWER(status) = ?', ['maintenance'])->count(),
            'damaged_assets' => Asset::whereRaw('LOWER(status) = ?', ['rusak'])->count(),
            'good_condition' => Asset::whereRaw('LOWER(kondisi) = ?', ['baik'])->count(),
            'minor_damage_condition' => Asset::whereRaw('LOWER(kondisi) LIKE ?', ['%rusak ringan%'])->count(),
            'heavy_damage_condition' => Asset::whereRaw('LOWER(kondisi) LIKE ?', ['%rusak berat%'])->count(),
            'total_categories' => Category::count(),
            'total_locations' => Location::count(),
            'total_vendors' => Vendor::count(),
            'total_maintenance' => MaintenanceRecord::count(),
            'total_maintenance_cost' => number_format($totalCost, 0, ',', '.'),
            'pending_transfers' => Transfer::where('status', 'menunggu')->count(),
            'active_loans' => AssetLoan::whereIn('status', ['Dipinjam', 'dipinjam', 'Diajukan', 'diajukan'])->count(),
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'pimpinan_users' => User::where('role', 'pimpinan')->count(),
            'staff_users' => User::where('role', 'staff')->count(),
            'peminjam_users' => User::where('role', 'peminjam')->count(),
        ];
    }

    /**
     * Menyusun tabel Markdown daftar inventaris aset aktual dari database
     */
    public function getAssetsInventoryText(int $limit = 50): string
    {
        $assets = Asset::with(['category', 'location', 'vendor'])
            ->orderBy('id', 'asc')
            ->take($limit)
            ->get();

        if ($assets->isEmpty()) {
            return "Belum ada aset yang tercatat di database.";
        }

        $lines = [];
        $lines[] = "| Kode Aset | Nama Aset | Kategori | Lokasi | Status | Kondisi | Harga | Penanggung Jawab | Vendor |";
        $lines[] = "|---|---|---|---|---|---|---|---|---|";

        foreach ($assets as $asset) {
            $formattedPrice = 'Rp ' . number_format((float)$asset->harga, 0, ',', '.');
            $lines[] = sprintf(
                "| %s | %s | %s | %s | %s | %s | %s | %s | %s |",
                $asset->kode_asset,
                $asset->nama_asset,
                $asset->category->nama ?? '-',
                $asset->location->nama ?? '-',
                $asset->status,
                $asset->kondisi,
                $formattedPrice,
                $asset->penanggung_jawab ?: '-',
                $asset->vendor->nama ?? '-'
            );
        }

        return implode("\n", $lines);
    }

    /**
     * Menyusun data master (kategori, lokasi, vendor)
     */
    public function getMasterDataText(): string
    {
        $categories = Category::withCount('assets')->get();
        $locations = Location::withCount('assets')->get();
        $vendors = Vendor::withCount('assets')->get();

        $chunks = [];

        // Kategori
        $catLines = ["A. Daftar Kategori Aset:"];
        foreach ($categories as $cat) {
            $catLines[] = sprintf("- %s (%d aset): %s", $cat->nama, $cat->assets_count, $cat->deskripsi ?: 'Tidak ada deskripsi');
        }
        $chunks[] = implode("\n", $catLines);

        // Lokasi
        $locLines = ["B. Daftar Lokasi Fisik:"];
        foreach ($locations as $loc) {
            $locDetails = [];
            if ($loc->gedung) $locDetails[] = "Gedung: {$loc->gedung}";
            if ($loc->lantai) $locDetails[] = "Lantai: {$loc->lantai}";
            if ($loc->ruangan) $locDetails[] = "Ruang: {$loc->ruangan}";
            $detailStr = !empty($locDetails) ? ' (' . implode(', ', $locDetails) . ')' : '';
            $locLines[] = sprintf("- %s%s: Memuat %d aset. %s", $loc->nama, $detailStr, $loc->assets_count, $loc->deskripsi ?: '');
        }
        $chunks[] = implode("\n", $locLines);

        // Vendor
        $venLines = ["C. Daftar Rekanan / Vendor:"];
        foreach ($vendors as $ven) {
            $venLines[] = sprintf(
                "- %s: Kontak %s | Email: %s | Alamat: %s (%d aset terhubung)",
                $ven->nama,
                $ven->kontak ?: '-',
                $ven->email ?: '-',
                $ven->alamat ?: '-',
                $ven->assets_count
            );
        }
        $chunks[] = implode("\n", $venLines);

        return implode("\n\n", $chunks);
    }

    /**
     * Menyusun data transaksi riil terkini (pemeliharaan, peminjaman, mutasi)
     */
    public function getRecentTransactionsText(): string
    {
        $chunks = [];

        // Pemeliharaan terkini
        $records = MaintenanceRecord::with('asset')->latest('tanggal')->take(5)->get();
        if ($records->isNotEmpty()) {
            $maintLines = ["A. Catatan Pemeliharaan & Servis Terkini:"];
            foreach ($records as $m) {
                $maintLines[] = sprintf(
                    "- Aset: %s (%s) | Tanggal: %s | Jenis: %s | Status: %s | Keluhan: %s | Kerusakan: %s | Teknisi: %s | Biaya: Rp %s | Tindakan: %s",
                    $m->asset->nama_asset ?? 'Aset #'.$m->asset_id,
                    $m->asset->kode_asset ?? '-',
                    $m->tanggal ? $m->tanggal->format('d/m/Y') : '-',
                    $m->jenis,
                    $m->status,
                    $m->keluhan ?: '-',
                    $m->kerusakan ?: '-',
                    $m->teknisi ?: '-',
                    number_format((float)$m->biaya, 0, ',', '.'),
                    $m->hasil_tindakan ?: '-'
                );
            }
            $chunks[] = implode("\n", $maintLines);
        }

        // Peminjaman terkini
        $loans = AssetLoan::with(['asset', 'user'])->latest('tanggal_pinjam')->take(5)->get();
        if ($loans->isNotEmpty()) {
            $loanLines = ["B. Catatan Peminjaman Aset Terkini:"];
            foreach ($loans as $l) {
                $loanLines[] = sprintf(
                    "- Peminjam: %s | Aset: %s (%s) | Tanggal Pinjam: %s | Rencana Kembali: %s | Kembali Aktual: %s | Status: %s | Keperluan: %s",
                    $l->user->name ?? 'User #'.$l->user_id,
                    $l->asset->nama_asset ?? 'Aset #'.$l->asset_id,
                    $l->asset->kode_asset ?? '-',
                    $l->tanggal_pinjam ? $l->tanggal_pinjam->format('d/m/Y') : '-',
                    $l->tanggal_rencana_kembali ? $l->tanggal_rencana_kembali->format('d/m/Y') : '-',
                    $l->tanggal_kembali ? $l->tanggal_kembali->format('d/m/Y') : '-',
                    $l->status,
                    $l->keperluan ?: '-'
                );
            }
            $chunks[] = implode("\n", $loanLines);
        }

        // Mutasi terkini / pending
        $transfers = Transfer::with(['asset', 'originLocation', 'targetLocation', 'requester'])
            ->latest()
            ->take(5)
            ->get();
        if ($transfers->isNotEmpty()) {
            $transLines = ["C. Riwayat & Pengajuan Mutasi Lokasi:"];
            foreach ($transfers as $t) {
                $transLines[] = sprintf(
                    "- Aset: %s (%s) | Dari: %s -> Ke: %s | Pemohon: %s | Status: %s | Alasan: %s",
                    $t->asset->nama_asset ?? 'Aset #'.$t->asset_id,
                    $t->asset->kode_asset ?? '-',
                    $t->originLocation->nama ?? 'Awal',
                    $t->targetLocation->nama ?? 'Tujuan',
                    $t->requester->name ?? 'Staff',
                    $t->status,
                    $t->reason ?: '-'
                );
            }
            $chunks[] = implode("\n", $transLines);
        }

        return !empty($chunks) ? implode("\n\n", $chunks) : "Belum ada transaksi operasional terkini.";
    }

    /**
     * Melakukan pencarian dinamis di database berdasarkan kata kunci prompt pengguna
     */
    protected function searchDatabaseRelevantContext(string $userPrompt): string
    {
        $contextChunks = [];
        $trimmed = trim($userPrompt);

        // 1. Cek jika menyebut kode aset spesifik (misal AST-GIN-0001, AST-02, dll)
        if (preg_match_all('/([A-Za-z0-9]+(?:-[A-Za-z0-9]+)+)/', $trimmed, $codeMatches)) {
            $possibleCodes = array_unique($codeMatches[0]);
            $matchedAssets = Asset::with(['category', 'location', 'vendor', 'maintenanceRecords', 'histories'])
                ->whereIn('kode_asset', $possibleCodes)
                ->get();

            if ($matchedAssets->isNotEmpty()) {
                $details = ["KONTEKS RINCI ASET BERDASARKAN KODE ASET YANG DISEBUT:"];
                foreach ($matchedAssets as $a) {
                    $details[] = $this->formatDetailedAssetCard($a);
                }
                $contextChunks[] = implode("\n\n", $details);
            }
        }

        // 2. Cek jika menyebut nama barang tertentu (misal 'printer', 'proyektor', 'kursi', 'router', 'lemari')
        $keywords = ['printer', 'proyektor', 'kursi', 'router', 'lemari', 'laptop', 'meja', 'komputer'];
        $foundKeywords = [];
        foreach ($keywords as $kw) {
            if (stripos($trimmed, $kw) !== false) {
                $foundKeywords[] = $kw;
            }
        }

        if (!empty($foundKeywords)) {
            $matchedByKeyword = Asset::with(['category', 'location', 'vendor', 'maintenanceRecords'])
                ->where(function ($q) use ($foundKeywords) {
                    foreach ($foundKeywords as $kw) {
                        $lowerKw = '%' . strtolower($kw) . '%';
                        $q->orWhereRaw('LOWER(nama_asset) LIKE ?', [$lowerKw])
                          ->orWhereRaw('LOWER(merk) LIKE ?', [$lowerKw])
                          ->orWhereRaw('LOWER(tipe) LIKE ?', [$lowerKw]);
                    }
                })
                ->take(5)
                ->get();

            if ($matchedByKeyword->isNotEmpty()) {
                $details = ["KONTEKS RINCI ASET YANG RELEVAN DENGAN KATA KUNCI PERTANYAAN:"];
                foreach ($matchedByKeyword as $a) {
                    $details[] = $this->formatDetailedAssetCard($a);
                }
                $contextChunks[] = implode("\n\n", $details);
            }
        }

        return !empty($contextChunks) ? "==================================================\n6. DATA SPESIFIK RELEVAN DENGAN PERTANYAAN USER\n==================================================\n" . implode("\n\n", $contextChunks) : '';
    }

    /**
     * Memformat rincian satu aset beserta riwayat maintenance & audit trail-nya
     */
    protected function formatDetailedAssetCard(Asset $asset): string
    {
        $price = 'Rp ' . number_format((float)$asset->harga, 0, ',', '.');
        $card = [
            "Aset: {$asset->nama_asset} (Kode: {$asset->kode_asset})",
            "- Merk / Tipe: " . ($asset->merk ?: '-') . " / " . ($asset->tipe ?: '-'),
            "- Kategori: " . ($asset->category->nama ?? '-'),
            "- Lokasi Fisik: " . ($asset->location->nama ?? '-'),
            "- Status: {$asset->status} | Kondisi: {$asset->kondisi}",
            "- Tanggal Perolehan: " . ($asset->tanggal_perolehan ? $asset->tanggal_perolehan->format('d/m/Y') : '-'),
            "- Harga Perolehan: {$price}",
            "- Penanggung Jawab: " . ($asset->penanggung_jawab ?: '-'),
            "- Vendor Rekanan: " . ($asset->vendor->nama ?? '-'),
            "- Deskripsi: " . ($asset->deskripsi ?: '-'),
        ];

        if ($asset->maintenanceRecords && $asset->maintenanceRecords->isNotEmpty()) {
            $card[] = "- Riwayat Pemeliharaan/Perbaikan:";
            foreach ($asset->maintenanceRecords as $m) {
                $card[] = sprintf(
                    "  * [%s] Status: %s, Keluhan: '%s', Tindakan: '%s', Teknisi: %s, Biaya: Rp %s",
                    $m->tanggal ? $m->tanggal->format('d/m/Y') : '-',
                    $m->status,
                    $m->keluhan ?: '-',
                    $m->hasil_tindakan ?: '-',
                    $m->teknisi ?: '-',
                    number_format((float)$m->biaya, 0, ',', '.')
                );
            }
        }

        if ($asset->histories && $asset->histories->isNotEmpty()) {
            $card[] = "- Log Riwayat Audit Trail Terkini:";
            foreach ($asset->histories->take(3) as $h) {
                $card[] = sprintf("  * [%s] %s: %s", $h->tanggal ? $h->tanggal->format('d/m/Y H:i') : '-', $h->aktivitas, $h->deskripsi);
            }
        }

        return implode("\n", $card);
    }
}
