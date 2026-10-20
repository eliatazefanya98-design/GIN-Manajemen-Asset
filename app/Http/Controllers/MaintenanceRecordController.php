<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\MaintenanceRecord;
use App\Models\AssetHistory;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;


class MaintenanceRecordController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $maintenanceRecords = MaintenanceRecord::query()
            ->with('asset')
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('jenis', 'like', "%{$search}%")
                        ->orWhere('keluhan', 'like', "%{$search}%")
                        ->orWhere('kerusakan', 'like', "%{$search}%")
                        ->orWhere('teknisi', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('asset', function ($query) use ($search) {
                            $query->where('kode_asset', 'like', "%{$search}%")
                                ->orWhere('nama_asset', 'like', "%{$search}%");
                        });
                });
            })
            ->latest('tanggal')
            ->paginate(10)
            ->withQueryString();

        return view('maintenance_records.index', compact(
            'maintenanceRecords',
            'search'
        ));
    }

    public function create()
    {
        $assets = Asset::orderBy('kode_asset')->get();

        return view('maintenance_records.create', compact(
            'assets'
        ));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'asset_id' => [
            'required',
            'exists:assets,id',
        ],

        'tanggal' => [
            'required',
            'date',
        ],

        'jenis' => [
            'nullable',
            'string',
            'max:255',
        ],

        'keluhan' => [
            'nullable',
            'string',
        ],

        'kerusakan' => [
            'nullable',
            'string',
        ],

        'biaya' => [
            'nullable',
            'numeric',
            'min:0',
        ],

        'teknisi' => [
            'nullable',
            'string',
            'max:255',
        ],

        'hasil_tindakan' => [
            'nullable',
            'string',
        ],

        'kondisi_sebelum' => [
            'nullable',
            'string',
            'max:255',
        ],

        'kondisi_sesudah' => [
            'nullable',
            'string',
            'max:255',
        ],

        'status' => [
            'required',
            Rule::in([
                'Terjadwal',
                'Proses',
                'Selesai',
                'Dibatalkan',
            ]),
        ],
    ]);

    // Membuat data maintenance
    $maintenanceRecord = MaintenanceRecord::create($validated);

    // Membuat history otomatis
    AssetHistory::create([
        'asset_id' => $maintenanceRecord->asset_id,
        'aktivitas' => 'Maintenance',
        'deskripsi' => 'Maintenance asset dilakukan. '
            . 'Jenis: ' . ($maintenanceRecord->jenis ?? '-')
            . '. Keluhan: ' . ($maintenanceRecord->keluhan ?? '-')
            . '. Status: ' . $maintenanceRecord->status . '.',
        'tanggal' => now(),
        'user_id' => auth()->id(),
    ]);

    return redirect()
        ->route('maintenance-records.show', $maintenanceRecord)
        ->with('success', 'Data maintenance berhasil ditambahkan.');
}

    public function show(MaintenanceRecord $maintenanceRecord)
    {
        $maintenanceRecord->load('asset');

        return view('maintenance_records.show', compact(
            'maintenanceRecord'
        ));
    }

    public function edit(MaintenanceRecord $maintenanceRecord)
    {
        $assets = Asset::orderBy('kode_asset')->get();

        return view('maintenance_records.edit', compact(
            'maintenanceRecord',
            'assets'
        ));
    }

    public function update(
    Request $request,
    MaintenanceRecord $maintenanceRecord
) {
    $validated = $request->validate([
        'asset_id' => [
            'required',
            'exists:assets,id',
        ],

        'tanggal' => [
            'required',
            'date',
        ],

        'jenis' => [
            'nullable',
            'string',
            'max:255',
        ],

        'keluhan' => [
            'nullable',
            'string',
        ],

        'kerusakan' => [
            'nullable',
            'string',
        ],

        'biaya' => [
            'nullable',
            'numeric',
            'min:0',
        ],

        'teknisi' => [
            'nullable',
            'string',
            'max:255',
        ],

        'hasil_tindakan' => [
            'nullable',
            'string',
        ],

        'kondisi_sebelum' => [
            'nullable',
            'string',
            'max:255',
        ],

        'kondisi_sesudah' => [
            'nullable',
            'string',
            'max:255',
        ],

        'status' => [
            'required',
            Rule::in([
                'Terjadwal',
                'Proses',
                'Selesai',
                'Dibatalkan',
            ]),
        ],
    ]);

    // Simpan data lama sebelum diperbarui
    $oldData = $maintenanceRecord->getOriginal();

    // Update maintenance
    $maintenanceRecord->update($validated);

    // Ambil perubahan yang terjadi
    $changes = $maintenanceRecord->getChanges();

    // Hapus updated_at dari daftar perubahan
    unset($changes['updated_at']);

    // Jika ada perubahan, buat history otomatis
    if (!empty($changes)) {
        $deskripsiPerubahan = [];

        foreach ($changes as $field => $newValue) {
            $oldValue = $oldData[$field] ?? null;

            $deskripsiPerubahan[] =
                ucfirst(str_replace('_', ' ', $field))
                . ': '
                . ($oldValue ?? '-')
                . ' → '
                . ($newValue ?? '-');
        }

        AssetHistory::create([
            'asset_id' => $maintenanceRecord->asset_id,
            'aktivitas' => 'Maintenance Diperbarui',
            'deskripsi' => implode('; ', $deskripsiPerubahan),
            'tanggal' => now(),
            'user_id' => auth()->id(),
        ]);
    }

    return redirect()
        ->route('maintenance-records.show', $maintenanceRecord)
        ->with('success', 'Data maintenance berhasil diperbarui.');
}
    public function destroy(MaintenanceRecord $maintenanceRecord)
    {
        $maintenanceRecord->delete();

        return redirect()
            ->route('maintenance-records.index')
            ->with('success', 'Data maintenance berhasil dihapus.');
    }
}