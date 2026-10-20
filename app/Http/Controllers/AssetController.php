<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\Category;
use App\Models\Location;
use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Models\AssetHistory;
use App\Models\SystemSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AssetController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');

        $assets = Asset::query()
            ->when(Auth::user()->role === 'peminjam', fn ($query) => $query->whereRaw('LOWER(status) = ?', ['aktif']))
            ->with(['category', 'location', 'vendor'])
            ->when($search, function ($query, $search) {
                $query->where(function ($query) use ($search) {
                    $query->where('kode_asset', 'like', "%{$search}%")
                        ->orWhere('nama_asset', 'like', "%{$search}%")
                        ->orWhere('merk', 'like', "%{$search}%")
                        ->orWhere('tipe', 'like', "%{$search}%")
                        ->orWhere('kondisi', 'like', "%{$search}%")
                        ->orWhere('status', 'like', "%{$search}%")
                        ->orWhereHas('category', function ($query) use ($search) {
                            $query->where('nama', 'like', "%{$search}%");
                        })
                        ->orWhereHas('location', function ($query) use ($search) {
                            $query->where('nama', 'like', "%{$search}%");
                        })
                        ->orWhereHas('vendor', function ($query) use ($search) {
                            $query->where('nama', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10)
            ->withQueryString();

        return view('assets.index', compact(
            'assets',
            'search'
        ));
    }

    public function create()
    {
        $categories = Category::orderBy('nama')->get();
        $locations = Location::orderBy('nama')->get();
        $vendors = Vendor::orderBy('nama')->get();

        $nextAssetCode = SystemSetting::current()->nextAssetCode();

        return view('assets.create', compact(
            'categories',
            'locations',
            'vendors',
            'nextAssetCode'
        ));
    }

    public function store(Request $request)
{
    $validated = $request->validate([
        'kode_asset' => [
            'nullable',
            'string',
            'max:255',
            'unique:assets,kode_asset',
        ],
        'nama_asset' => ['required', 'string', 'max:255'],
        'category_id' => ['required', 'exists:categories,id'],
        'location_id' => ['nullable', 'exists:locations,id'],
        'vendor_id' => ['nullable', 'exists:vendors,id'],
        'merk' => ['nullable', 'string', 'max:255'],
        'tipe' => ['nullable', 'string', 'max:255'],
        'tanggal_perolehan' => ['nullable', 'date'],
        'harga' => ['nullable', 'numeric', 'min:0'],
        'kondisi' => [
            'required',
            Rule::in([
                'Baik',
                'Rusak Ringan',
                'Rusak Berat',
            ]),
        ],
        'status' => [
            'required',
            Rule::in([
                'Aktif',
                'Dipinjam',
                'Maintenance',
                'Rusak',
                'Dihapus',
            ]),
        ],
        'penanggung_jawab' => ['nullable', 'string', 'max:255'],
        'deskripsi' => ['nullable', 'string'],
    ]);

    $generatedCode = $request->input('generated_kode_asset');

    $asset = DB::transaction(function () use ($validated, $generatedCode) {
        $setting = SystemSetting::query()->lockForUpdate()->firstOrCreate([], SystemSetting::defaults());

        $isAutomaticCode = blank($validated['kode_asset'] ?? null)
            || $validated['kode_asset'] === $generatedCode;

        if ($isAutomaticCode) {
            $validated['kode_asset'] = $setting->nextAssetCode();
            $setting->increment('asset_next_number');
        }

        return Asset::create($validated);
    });

    // Membuat history otomatis
    AssetHistory::create([
        'asset_id' => $asset->id,
        'aktivitas' => 'Asset Dibuat',
        'deskripsi' => 'Asset ' . $asset->kode_asset . ' - ' . $asset->nama_asset . ' berhasil ditambahkan ke sistem.',
        'tanggal' => now(),
        'user_id' => Auth::id(),
    ]);

    return redirect()
        ->route('assets.show', $asset)
        ->with('success', 'Asset berhasil ditambahkan.');
}
    public function show(Asset $asset)
    {
        $asset->load([
            'category',
            'location',
            'vendor',
            'maintenanceRecords',
            'histories',
            'aiPredictions',
        ]);

        return view('assets.show', compact('asset'));
    }

    public function edit(Asset $asset)
    {
        $categories = Category::orderBy('nama')->get();
        $locations = Location::orderBy('nama')->get();
        $vendors = Vendor::orderBy('nama')->get();

        return view('assets.edit', compact(
            'asset',
            'categories',
            'locations',
            'vendors'
        ));
    }

    public function update(Request $request, Asset $asset)
{
    $validated = $request->validate([
        'kode_asset' => [
            'required',
            'string',
            'max:255',
            Rule::unique('assets', 'kode_asset')
                ->ignore($asset->id),
        ],
        'nama_asset' => ['required', 'string', 'max:255'],
        'category_id' => ['required', 'exists:categories,id'],
        'location_id' => ['nullable', 'exists:locations,id'],
        'vendor_id' => ['nullable', 'exists:vendors,id'],
        'merk' => ['nullable', 'string', 'max:255'],
        'tipe' => ['nullable', 'string', 'max:255'],
        'tanggal_perolehan' => ['nullable', 'date'],
        'harga' => ['nullable', 'numeric', 'min:0'],
        'kondisi' => [
            'required',
            Rule::in([
                'Baik',
                'Rusak Ringan',
                'Rusak Berat',
            ]),
        ],
        'status' => [
            'required',
            Rule::in([
                'Aktif',
                'Dipinjam',
                'Maintenance',
                'Rusak',
                'Dihapus',
            ]),
        ],
        'penanggung_jawab' => ['nullable', 'string', 'max:255'],
        'deskripsi' => ['nullable', 'string'],
    ]);

    // Simpan data lama sebelum diperbarui
    $oldData = $asset->getOriginal();

    // Update asset
    $asset->update($validated);

    // Ambil perubahan yang terjadi
    $changes = $asset->getChanges();

    // Hapus updated_at dari daftar perubahan
    unset($changes['updated_at']);

    // Jika memang ada perubahan, buat history
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
            'asset_id' => $asset->id,
            'aktivitas' => 'Asset Diperbarui',
            'deskripsi' => implode('; ', $deskripsiPerubahan),
            'tanggal' => now(),
            'user_id' => Auth::id(),
        ]);
    }

    return redirect()
        ->route('assets.show', $asset)
        ->with('success', 'Asset berhasil diperbarui.');
}

    public function destroy(Asset $asset)
    {
        $asset->delete();

        return redirect()
            ->route('assets.index')
            ->with('success', 'Asset berhasil dihapus.');
    }
}