<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\MaintenanceRecord;
use App\Models\Location;
use App\Models\Transfer;

class DashboardController extends Controller
{
    public function index()
    {
        // ==========================================
        // STATISTIK ASSET
        // ==========================================

        $totalAssets = Asset::count();

        $activeAssets = Asset::whereRaw('LOWER(status) = ?', ['aktif'])->count();

        $maintenanceAssets = Asset::whereRaw('LOWER(status) = ?', ['maintenance'])->count();

        $damagedAssets = Asset::whereRaw('LOWER(status) = ?', ['rusak'])->count();


        // ==========================================
        // STATISTIK MAINTENANCE
        // ==========================================

        $totalMaintenance = MaintenanceRecord::count();

        $scheduledMaintenance = MaintenanceRecord::whereRaw('LOWER(status) = ?', ['terjadwal'])->count();

        $processMaintenance = MaintenanceRecord::whereRaw('LOWER(status) = ?', ['proses'])->count();

        $completedMaintenance = MaintenanceRecord::whereRaw('LOWER(status) = ?', ['selesai'])->count();

        $pendingApprovals = Transfer::where('status', 'menunggu')->count();


        // ==========================================
        // ASSET TERBARU
        // ==========================================

        $latestAssets = Asset::with([
            'category',
            'location',
        ])
            ->latest()
            ->take(5)
            ->get();


        // ==========================================
        // MAINTENANCE TERBARU
        // ==========================================

        $latestMaintenance = MaintenanceRecord::with([
            'asset',
        ])
            ->latest('tanggal')
            ->take(5)
            ->get();


        // ==========================================
        // DATA GRAFIK KATEGORI
        // ==========================================

        $categories = Category::withCount('assets')
            ->orderByDesc('assets_count')
            ->get();

        $categoryLabels = $categories
            ->pluck('nama')
            ->values();

        $categoryData = $categories
            ->pluck('assets_count')
            ->values();


        // ==========================================
        // DATA GRAFIK STATUS ASSET
        // ==========================================

        $statuses = [
            'Aktif',
            'Dipinjam',
            'Maintenance',
            'Rusak',
            'Dihapus',
        ];

        $statusLabels = $statuses;

        $statusData = [];

        foreach ($statuses as $status) {
            $statusData[] = Asset::whereRaw('LOWER(status) = ?', [strtolower($status)])->count();
        }


        // ==========================================
        // DATA ASSET PER DIVISI / LOKASI
        // ==========================================

        $locations = Location::withCount('assets')
            ->orderByDesc('assets_count')
            ->get();

        $divisionLabels = $locations
            ->pluck('nama')
            ->values();

        $divisionData = $locations
            ->pluck('assets_count')
            ->values();


        // ==========================================
        // JADWAL MAINTENANCE
        // ==========================================

        $scheduledMaintenances = MaintenanceRecord::with([
            'asset',
        ])
            ->whereRaw('LOWER(status) = ?', ['terjadwal'])
            ->orderBy('tanggal')
            ->take(5)
            ->get();


        // ==========================================
        // KIRIM DATA KE VIEW
        // ==========================================

        return view('dashboard.index', compact(
            // Statistik asset
            'totalAssets',
            'activeAssets',
            'maintenanceAssets',
            'damagedAssets',

            // Statistik maintenance
            'totalMaintenance',
            'scheduledMaintenance',
            'processMaintenance',
            'completedMaintenance',
            'pendingApprovals',

            // Asset terbaru
            'latestAssets',

            // Maintenance terbaru
            'latestMaintenance',

            // Grafik kategori
            'categoryLabels',
            'categoryData',

            // Grafik status
            'statusLabels',
            'statusData',

            // Asset per divisi / lokasi
            'divisionLabels',
            'divisionData',

            // Jadwal maintenance
            'scheduledMaintenances'
        ));
    }
}