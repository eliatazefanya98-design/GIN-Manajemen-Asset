<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Location;
use App\Models\MaintenanceRecord;
use App\Models\SystemSetting;
use App\Models\User;
use App\Models\Vendor;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Akun Pengguna Bawaan
        $admin = User::firstOrCreate(
            ['email' => 'admin@gin.co.id'],
            [
                'name' => 'Administrator',
                'password' => Hash::make('password'),
                'role' => 'admin',
            ]
        );
  
        User::firstOrCreate(
            ['email' => 'staff@gin.co.id'],
            [
                'name' => 'Staff Operasional',
                'password' => Hash::make('password'),
                'role' => 'staff',
            ]
        );

        User::firstOrCreate(
            ['email' => 'pimpinan@gin.co.id'],
            [
                'name' => 'Pimpinan',
                'password' => Hash::make('password'),
                'role' => 'pimpinan',
            ]
        );

        User::firstOrCreate(
            ['email' => 'peminjam@gin.co.id'],
            [
                'name' => 'Peminjam Aset',
                'password' => Hash::make('password'),
                'role' => 'peminjam',
            ]
        );

        // 2. Pengaturan Sistem
        SystemSetting::query()->firstOrCreate([], SystemSetting::defaults());

        // 3. Kategori
        $itCategory = Category::firstOrCreate(
            ['nama' => 'IT'],
            ['deskripsi' => 'Peralatan dan perangkat teknologi informasi.']
        );

        $sekretariatCategory = Category::firstOrCreate(
            ['nama' => 'Sekretariat'],
            ['deskripsi' => 'Peralatan administrasi dan perkantoran.']
        );

        Category::firstOrCreate(
            ['nama' => 'Elektronik'],
            ['deskripsi' => 'Peralatan elektronik umum.']
        );

        Category::firstOrCreate(
            ['nama' => 'Kendaraan'],
            ['deskripsi' => 'Kendaraan dinas dan operasional.']
        );

        // 4. Lokasi
        $labLocation = Location::firstOrCreate(
            ['nama' => 'Lab Komputer 1'],
            [
                'gedung' => 'Gedung A',
                'lantai' => '1',
                'ruangan' => 'Lab 101',
                'deskripsi' => 'Laboratorium komputer utama.',
            ]
        );

        $rapatLocation = Location::firstOrCreate(
            ['nama' => 'Ruang Rapat'],
            [
                'gedung' => 'Gedung Utama',
                'lantai' => '2',
                'ruangan' => 'Ruang Rapat VIP',
                'deskripsi' => 'Ruang rapat utama pimpinan dan staf.',
            ]
        );

        Location::firstOrCreate(
            ['nama' => 'Gudang Utama'],
            [
                'gedung' => 'Gedung B',
                'lantai' => '1',
                'ruangan' => 'Gudang Inventaris',
                'deskripsi' => 'Penyimpanan aset dan cadangan.',
            ]
        );

        // 5. Vendor
        Vendor::firstOrCreate(
            ['nama' => 'PT Teknologi Indonesia'],
            [
                'kontak' => '081234567890',
                'email' => 'info@teknologi.co.id',
                'alamat' => 'Jl. Sudirman No. 10 Jakarta Pusat',
            ]
        );

        Vendor::firstOrCreate(
            ['nama' => 'CV Makmur Jaya'],
            [
                'kontak' => '089876543210',
                'email' => 'sales@makmurjaya.com',
                'alamat' => 'Jl. Gatot Subroto No. 45 Bandung',
            ]
        );

        // 6. Jalankan Sample Aset
        $this->call(SampleAssetSeeder::class);

        // 7. Sample Maintenance Record
        $proyektor = \App\Models\Asset::where('nama_asset', 'Proyektor BenQ MX560')->first();
        if ($proyektor) {
            MaintenanceRecord::firstOrCreate(
                ['asset_id' => $proyektor->id, 'keluhan' => 'Konektor HDMI longgar'],
                [
                    'tanggal' => now()->toDateString(),
                    'jenis' => 'Perbaikan',
                    'kerusakan' => 'Port HDMI rusak ringan',
                    'biaya' => 250000,
                    'teknisi' => 'Budi Santoso',
                    'hasil_tindakan' => 'Solder ulang pin port HDMI dan uji konektivitas',
                    'kondisi_sebelum' => 'Rusak Ringan',
                    'kondisi_sesudah' => 'Baik',
                    'status' => 'Proses',
                ]
            );
        }
    }
}
