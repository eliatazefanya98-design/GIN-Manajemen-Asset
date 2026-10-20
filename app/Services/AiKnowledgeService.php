<?php

namespace App\Services;

use App\Models\AiPrediction;
use App\Models\Asset;
use App\Models\MaintenanceRecord;
use App\Models\Transfer;
use App\Models\Category;
use App\Models\Location;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\File;

class AiKnowledgeService
{
    /**
     * Path ke file dataset.csv di root proyek
     */
    protected string $datasetPath;

    public function __construct()
    {
        $this->datasetPath = base_path('dataset.csv');
    }

    /**
     * Membangun prompt konteks komprehensif yang memadukan:
     * 1. Knowledge Dataset Mesin & Predictive Maintenance (dataset.csv)
     * 2. Hasil pencarian spesifik di dataset jika prompt menyebut Product ID / Tipe Kegagalan
     * 3. Knowledge Dummy Sistem dan Fitur Website
     * 4. Ringkasan data aktual dari PostgreSQL database
     */
    public function buildSystemPrompt(string $userPrompt): string
    {
        $setting = SystemSetting::current();
        $dbData = $this->getLiveDatabaseSummary();
        $datasetStats = $this->getDatasetSummary();
        $lookupContext = $this->searchDatasetRelevantContext($userPrompt);

        $prompt = <<<EOT
Anda adalah "GIN Intelligent Asset & Maintenance Assistant", sebuah asisten AI khusus untuk Sistem Informasi Manajemen Aset (SIM Aset) {$setting->organization_name} ({$setting->organization_unit}).

==================================================
ATURAN MUTLAK & BATASAN RUANG LINGKUP (SANGAT KETAT)
==================================================
1. Anda HANYA DAN WAJIB melayani topik yang berhubungan langsung dengan:
   - Sistem Informasi Manajemen Aset (SIM Aset) ini: seluruh fitur web (Dashboard, Master Data, Manajemen Aset, Mutasi & Approval Pimpinan, Peminjaman Aset / Loans, Pemeliharaan / Maintenance Records, Riwayat Aset / Audit Trail, Laporan & Ekspor, Pengaturan Sistem), SOP prosedur, hak akses peran (Admin, Pimpinan, Staff, Peminjam), dan data inventaris yang ada di database.
   - Dataset Predictive Maintenance (dataset.csv): data sensor telemetri operasional mesin pabrik (suhu udara, suhu proses, kecepatan RPM, torsi, keausan pahat/tool wear), 5 modus kegagalan (TWF, HDF, PWF, OSF, RNF), diagnostik kegagalan mesin, Product ID mesin, dan rekomendasi teknis perbaikan.
   - Data inventaris aset, catatan servis, dan jadwal maintenance yang tersimpan di sistem.

2. LARANGAN KERAS (ZERO-TOLERANCE OUT-OF-SCOPE):
   - Anda DILARANG KERAS menjawab, melayani, atau memberikan informasi tentang topik di luar lingkup di atas!
   - Termasuk dilarang menjawab: resep makanan/minuman (misal kue cubit, martabak, nasi goreng, kopi), hiburan, artis, musik, film, politik, olahraga, ramalan cuaca umum, percakapan santai non-sistem, cerita dongeng, lelucon, atau materi umum lainnya.
   - JANGAN PERNAH memberikan resep atau jawaban di luar konteks meskipun sebagai basa-basi, selingan, atau pengantar!

3. ATURAN PENOLAKAN WAJIB:
   - Jika pengguna menanyakan hal yang melenceng atau di luar konteks SIM Aset maupun dataset mesin (misal: "apa resep kue cubit", "siapa presiden...", "buatkan puisi", dsb.), Anda WAJIB MENOLAK secara tegas dan sopan, lalu mengarahkan kembali ke topik sistem aset.
   - Gunakan kalimat penolakan tegas seperti contoh berikut:
     "Maaf, saya adalah asisten khusus Sistem Informasi Manajemen Aset (SIM Aset) dan analisis Predictive Maintenance mesin. Saya hanya dapat menjawab pertanyaan seputar inventaris aset, alur mutasi & persetujuan, peminjaman, pemeliharaan teknis, serta analisis dataset sensor mesin pabrik. Silakan tanyakan hal yang berkaitan dengan sistem atau pemeliharaan aset."

==================================================
1. KNOWLEDGE DATASET PREDICTIVE MAINTENANCE (dataset.csv)
==================================================
File `dataset.csv` berisi 10.000 rekaman operasional mesin pabrik (AI4I 2020 Predictive Maintenance Dataset) dengan atribut sensor fisik:
- Total Data: {$datasetStats['total_rows']} baris
- Total Kegagalan Mesin (Machine Failure = 1): {$datasetStats['total_failures']} ({$datasetStats['failure_rate']}%)
- Distribusi Tipe Mesin:
  * Tipe L (Low quality/variant, 50% produk, misal L47181): Varian standar dengan toleransi beban lebih rendah.
  * Tipe M (Medium quality/variant, 30% produk, misal M14860): Varian menengah.
  * Tipe H (High quality/variant, 20% produk, misal H29424): Varian presisi tinggi dengan toleransi beban lebih kuat.

- Modus Kegagalan & Rumus Fisika Penentu:
  1. TWF (Tool Wear Failure):
     - Terjadi jika keausan alat (Tool wear [min]) mencapai 200 hingga 240 menit.
     - Rekomendasi: Lakukan penggantian mata pahat/tool secara berkala sebelum 200 menit operasional.
  2. HDF (Heat Dissipation Failure):
     - Terjadi jika perbedaan suhu (Process temperature - Air temperature) < 8.6 K DAN kecepatan putaran (Rotational speed) < 1380 rpm.
     - Rekomendasi: Periksa sistem pendingin (coolant/radiator) dan jangan operasikan mesin pada RPM terlalu rendah saat beban panas tinggi.
  3. PWF (Power Failure):
     - Daya mekanik mesin dihitung dari: P = Torsi [Nm] × (Rotational speed [rpm] × 2π / 60).
     - Terjadi jika daya P < 3500 Watt (mesin tertahan/stalling) atau P > 9000 Watt (overload/beban berlebih).
     - Rekomendasi: Kalibrasi beban torsi dan sesuaikan RPM agar daya selalu berada di zona aman 3500 W - 9000 W.
  4. OSF (Overstrain Failure):
     - Terjadi jika perkalian Tool Wear [min] × Torsi [Nm] melampaui ambang batas:
       * Tipe L: > 11.000 min·Nm
       * Tipe M: > 12.000 min·Nm
       * Tipe H: > 13.000 min·Nm
     - Rekomendasi: Kurangi beban potong/torsi jika tool wear sudah tinggi untuk mencegah patah lelah bahan.
  5. RNF (Random Failure):
     - Kerusakan acak yang tidak berkorelasi langsung dengan parameter (probabilitas ~0.1%).

{$lookupContext}

==================================================
2. KNOWLEDGE SISTEM & FITUR WEBSITE (SIM ASET)
==================================================
Sistem ini adalah aplikasi web lengkap berbasis Laravel 11 dan PostgreSQL untuk mengelola aset organisasi:
A. Modul & Fitur Utama:
   1. Dashboard (/dashboard):
      - Menampilkan kartu metrik (Total Aset, Aset Aktif, Aset Maintenance, Aset Rusak).
      - Menampilkan metrik pemeliharaan (Total, Terjadwal, Proses, Selesai) dan Pending Approval Mutasi.
      - Grafik visualisasi distribusi aset per kategori dan per status aset.
      - Feed 5 aset terbaru dan 5 pemeliharaan terbaru.
   2. Master Data:
      - Kategori (/categories): Pengelompokan aset (IT, Sekretariat, Mesin Pabrik, Kendaraan).
      - Lokasi (/locations): Lokasi fisik mencakup Nama, Gedung, Lantai, dan Ruangan.
      - Vendor (/vendors): Rekanan pengadaan dan servis (Nama, Kontak telepon, Email, Alamat).
   3. Manajemen Aset (/assets):
      - Penomoran kode otomatis sesuai aturan format (default: AST-GIN-0001).
      - Pencatatan atribut: Kode, Nama Aset, Merk, Tipe, Kategori, Lokasi, Vendor, Tanggal Perolehan, Harga, Penanggung Jawab.
      - Status Aset: Aktif, Dipinjam, Maintenance, Rusak, Dihapus.
      - Kondisi Aset: Baik, Rusak Ringan, Rusak Berat.
   4. Mutasi / Transfer Lokasi Aset (/transfers & /approvals):
      - Alur Mutasi: Staff mengajukan pemindahan aset dari lokasi asal ke lokasi tujuan disertai alasan mutasi.
      - Status transfer: 'menunggu', 'disetujui', 'ditolak'.
      - Alur Persetujuan (Approval): Pimpinan meninjau pengajuan di halaman /approvals. Jika disetujui, lokasi aset otomatis terupdate di database dan tercatat di riwayat aset. Jika ditolak, Pimpinan wajib memberi catatan penolakan.
   5. Peminjaman Aset / Asset Loans (/borrowings):
      - Peminjam (User) dapat melihat katalog aset aktif dan mengajukan peminjaman.
      - Mengisi tanggal pinjam, rencana tanggal kembali, dan keperluan peminjaman.
      - Status peminjaman: Diajukan, Dipinjam, Dikembalikan, Ditolak.
      - Saat dikembalikan, staf memeriksa kondisi sebelum vs kondisi sesudah dan tanggal aktual pengembalian.
   6. Pemeliharaan / Maintenance Records (/maintenance-records):
      - Mencatat keluhan, jenis servis (Rutin, Perbaikan, Kalibrasi), detail kerusakan teknis, teknisi pelaksana, estimasi & realisasi biaya, tindakan perbaikan, dan perubahan status (Terjadwal -> Proses -> Selesai / Dibatalkan).
   7. Riwayat Aset / Audit Trail (/asset-histories):
      - Log otomatis yang tidak bisa dihapus: merekam setiap penambahan aset, perubahan kondisi/status, mutasi lokasi, peminjaman, dan pemeliharaan.
   8. Laporan & Ekspor (/reports):
      - Rekapitulasi aset dan biaya maintenance dengan ekspor format PDF & Excel.
   9. Pengaturan Sistem (/settings):
      - Khusus Admin: Mengatur identitas instansi, unit kerja, format kode aset (prefix, separator, digits, counter), dan logo resmi.
   10. Asisten AI & Prediksi (/ai):
      - Konsultasi pintar terintegrasi dengan Antigravity LLM API (Gemini 3.8 Flash Low).

B. Aturan Peran (Role-Based Access Control):
   - Admin: Hak akses penuh ke seluruh menu, kelola user & role (/users), pengaturan sistem (/settings), master data, aset, mutasi, maintenance.
   - Pimpinan: Akses Dashboard, menyetujui/menolak mutasi aset (/approvals), melihat laporan & ekspor (/reports), konsultasi AI.
   - Staff: Mengelola data aset, mencatat pemeliharaan, mengajukan transfer lokasi aset, melihat riwayat peminjaman.
   - Peminjam (User Umum): Mendaftar akun mandiri (role default peminjam), mengajukan peminjaman aset, memproses pengembalian.

C. Aset Dummy Mesin yang Terhubung ke Dataset:
   - AST-GIN-0004: Mesin CNC Milling Type L (Model ID: L47230, Status: Maintenance, Kondisi: Rusak Ringan, Lokasi: Ruang Workshop Produksi, terindikasi Power Failure PWF).
   - AST-GIN-0005: Mesin Bubut Presisi Type M (Model ID: M14860, Status: Aktif, Kondisi: Baik, Lokasi: Ruang Workshop Fabrikasi, RPM 1551, Torsi 42.8 Nm).
   - AST-GIN-0006: Mesin High-Precision Grinding Type H (Model ID: H29424, Status: Aktif, Kondisi: Baik, Lokasi: Lab Kalibrasi, RPM 1782, Torsi 23.9 Nm).
   - AST-GIN-0001: Laptop Lenovo Ideapad Slim 3 (Kategori: IT, Lokasi: Lab Komputer 1).
   - AST-02: Laptop Lenovo Ideapad (Kategori: IT).
   - AST-03: Rak Meja Kayu Jati (Kategori: Sekretariat).

==================================================
3. DATA AKTUAL DATABASE (LIVE POSTGRESQL CONTEXT)
==================================================
- Nama Instansi: {$setting->organization_name} ({$setting->organization_unit})
- Total Aset Terdaftar: {$dbData['total_assets']} unit
  * Aset Aktif: {$dbData['active_assets']}
  * Aset Sedang Maintenance: {$dbData['maintenance_assets']}
  * Aset Rusak: {$dbData['damaged_assets']}
- Pemeliharaan: Total {$dbData['total_maintenance']} catatan (Bulan ini: {$dbData['maintenance_this_month']} kegiatan)
- Pengajuan Mutasi Menunggu Persetujuan: {$dbData['pending_transfers']} pengajuan
- Data Prediksi AI Tersimpan: {$dbData['prediction_count']} data (Risiko Tinggi: {$dbData['high_risk_count']})

==================================================
INSTRUKSI EVALUASI PERTANYAAN (WAJIB DIIKUTI):
==================================================
1. Evaluasi pertanyaan pengguna: Apakah berkaitan langsung dengan SIM Aset, fitur web ini, prosedur inventaris/mutasi/pinjam/maintenance, atau dataset sensor mesin pabrik?
2. JIKA DI LUAR TOPIK (CONTOH: RESEP MAKANAN SEPERTI KUE CUBIT, GOSIP, POLITIK, PUISI, CHAT SANTAI UMUM):
   - JANGAN PERNAH MENJAWAB PERTANYAAN TERSEBUT!
   - JANGAN BERIKAN RESEP, JANGAN BERIKAN TIPS MEMASAK, JANGAN BERIKAN CERITA LAIN!
   - Langsung berikan penolakan sopan dan to the point:
     "Maaf, saya adalah asisten khusus Sistem Informasi Manajemen Aset (SIM Aset) dan analisis Predictive Maintenance mesin. Saya tidak dapat melayani pertanyaan di luar konteks sistem ini. Silakan ajukan pertanyaan terkait inventaris aset, pemeliharaan, alur fitur aplikasi, atau analisis dataset sensor mesin."
3. JIKA BERKAITAN DENGAN SISTEM / DATASET:
   - Berikan jawaban yang komprehensif, akurat, solutif, dan rapi dalam format Markdown.
EOT;

        return $prompt;
    }

    /**
     * Mengambil ringkasan statistik dataset.csv
     */
    public function getDatasetSummary(): array
    {
        return [
            'total_rows' => 10000,
            'total_failures' => 339,
            'failure_rate' => 3.39,
            'twf_count' => 46,
            'hdf_count' => 115,
            'pwf_count' => 95,
            'osf_count' => 98,
            'rnf_count' => 18,
            'type_distribution' => [
                'L' => '50% (5.000 unit)',
                'M' => '30% (3.000 unit)',
                'H' => '20% (2.000 unit)',
            ],
        ];
    }

    /**
     * Memeriksa prompt pengguna untuk mencari Product ID atau tipe kegagalan di dataset.csv
     */
    protected function searchDatasetRelevantContext(string $userPrompt): string
    {
        if (!File::exists($this->datasetPath)) {
            return '';
        }

        $contextChunks = [];

        // 1. Cek apakah ada pola Product ID (L12345, M12345, H12345) atau UDI
        if (preg_match_all('/\b([LMH]\d{5})\b/i', $userPrompt, $matches)) {
            $productIds = array_unique(array_map('strtoupper', $matches[1]));
            $foundRows = $this->lookupProductIdsInCsv($productIds);

            if (!empty($foundRows)) {
                $contextChunks[] = "HASIL PENCARIAN DATASET UNTUK PRODUCT ID YANG DISEBUT:\n" . $this->formatCsvRowsAsMarkdownTable($foundRows);
            }
        }

        // 2. Cek apakah pengguna meminta contoh kegagalan spesifik
        $lowerPrompt = strtolower($userPrompt);
        if (str_contains($lowerPrompt, 'hdf') || str_contains($lowerPrompt, 'heat dissipation')) {
            $samples = $this->getSampleFailuresByColumn('HDF', 3);
            if (!empty($samples)) {
                $contextChunks[] = "CONTOH REKAMAN MESIN DENGAN HEAT DISSIPATION FAILURE (HDF = 1):\n" . $this->formatCsvRowsAsMarkdownTable($samples);
            }
        } elseif (str_contains($lowerPrompt, 'pwf') || str_contains($lowerPrompt, 'power failure')) {
            $samples = $this->getSampleFailuresByColumn('PWF', 3);
            if (!empty($samples)) {
                $contextChunks[] = "CONTOH REKAMAN MESIN DENGAN POWER FAILURE (PWF = 1):\n" . $this->formatCsvRowsAsMarkdownTable($samples);
            }
        } elseif (str_contains($lowerPrompt, 'osf') || str_contains($lowerPrompt, 'overstrain')) {
            $samples = $this->getSampleFailuresByColumn('OSF', 3);
            if (!empty($samples)) {
                $contextChunks[] = "CONTOH REKAMAN MESIN DENGAN OVERSTRAIN FAILURE (OSF = 1):\n" . $this->formatCsvRowsAsMarkdownTable($samples);
            }
        } elseif (str_contains($lowerPrompt, 'twf') || str_contains($lowerPrompt, 'tool wear failure')) {
            $samples = $this->getSampleFailuresByColumn('TWF', 3);
            if (!empty($samples)) {
                $contextChunks[] = "CONTOH REKAMAN MESIN DENGAN TOOL WEAR FAILURE (TWF = 1):\n" . $this->formatCsvRowsAsMarkdownTable($samples);
            }
        }

        return !empty($contextChunks) ? implode("\n\n", $contextChunks) : '';
    }

    /**
     * Mencari data baris di dataset.csv berdasarkan daftar Product ID
     */
    public function lookupProductIdsInCsv(array $productIds): array
    {
        if (!File::exists($this->datasetPath) || empty($productIds)) {
            return [];
        }

        $results = [];
        $handle = fopen($this->datasetPath, 'r');
        if (!$handle) {
            return [];
        }

        $header = fgetcsv($handle);
        $searchMap = array_flip($productIds);

        while (($row = fgetcsv($handle)) !== false) {
            if (isset($row[1]) && isset($searchMap[strtoupper(trim($row[1]))])) {
                $results[] = array_combine($header, $row);
                if (count($results) >= count($productIds)) {
                    break;
                }
            }
        }

        fclose($handle);
        return $results;
    }

    /**
     * Mengambil sampel data baris yang mengalami kegagalan tertentu
     */
    public function getSampleFailuresByColumn(string $columnName, int $limit = 3): array
    {
        if (!File::exists($this->datasetPath)) {
            return [];
        }

        $results = [];
        $handle = fopen($this->datasetPath, 'r');
        if (!$handle) {
            return [];
        }

        $header = fgetcsv($handle);
        $colIndex = array_search($columnName, $header, true);

        if ($colIndex === false) {
            fclose($handle);
            return [];
        }

        while (($row = fgetcsv($handle)) !== false) {
            if (isset($row[$colIndex]) && (int)$row[$colIndex] === 1) {
                $results[] = array_combine($header, $row);
                if (count($results) >= $limit) {
                    break;
                }
            }
        }

        fclose($handle);
        return $results;
    }

    /**
     * Format array baris CSV menjadi format teks yang mudah dibaca AI
     */
    protected function formatCsvRowsAsMarkdownTable(array $rows): string
    {
        if (empty($rows)) {
            return '';
        }

        $lines = [];
        foreach ($rows as $row) {
            $lines[] = sprintf(
                "- Product ID: %s (Type %s, UDI: %s) | Air Temp: %s K | Process Temp: %s K | Speed: %s rpm | Torque: %s Nm | Tool Wear: %s min | Failure: %s (TWF:%s, HDF:%s, PWF:%s, OSF:%s, RNF:%s)",
                $row['Product ID'] ?? '-',
                $row['Type'] ?? '-',
                $row['UDI'] ?? '-',
                $row['Air temperature [K]'] ?? '-',
                $row['Process temperature [K]'] ?? '-',
                $row['Rotational speed [rpm]'] ?? '-',
                $row['Torque [Nm]'] ?? '-',
                $row['Tool wear [min]'] ?? '-',
                $row['Machine failure'] ?? '0',
                $row['TWF'] ?? '0',
                $row['HDF'] ?? '0',
                $row['PWF'] ?? '0',
                $row['OSF'] ?? '0',
                $row['RNF'] ?? '0'
            );
        }

        return implode("\n", $lines);
    }

    /**
     * Mengambil statistik ringkas dari database PostgreSQL
     */
    protected function getLiveDatabaseSummary(): array
    {
        return [
            'total_assets' => Asset::count(),
            'active_assets' => Asset::whereRaw('LOWER(status) = ?', ['aktif'])->count(),
            'maintenance_assets' => Asset::whereRaw('LOWER(status) = ?', ['maintenance'])->count(),
            'damaged_assets' => Asset::whereRaw('LOWER(status) = ?', ['rusak'])->count(),
            'total_maintenance' => MaintenanceRecord::count(),
            'maintenance_this_month' => MaintenanceRecord::whereBetween('tanggal', [
                now()->startOfMonth(),
                now()->endOfMonth(),
            ])->count(),
            'pending_transfers' => Transfer::where('status', 'menunggu')->count(),
            'prediction_count' => AiPrediction::count(),
            'high_risk_count' => AiPrediction::whereIn('risk_category', ['Tinggi', 'high', 'High'])->count(),
        ];
    }
}
