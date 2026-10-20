<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetHistory;
use Illuminate\Http\Request;

class AssetHistoryController extends Controller
{
    /**
     * Menampilkan daftar asset history.
     */
    public function index(Request $request)
    {
        $query = AssetHistory::with(['asset', 'user'])
            ->latest('tanggal');

        if ($request->filled('search')) {
            $search = $request->search;

            $query->where(function ($q) use ($search) {
                $q->where('aktivitas', 'ilike', "%{$search}%")
                    ->orWhere('deskripsi', 'ilike', "%{$search}%")
                    ->orWhereHas('asset', function ($assetQuery) use ($search) {
                        $assetQuery->where('kode_asset', 'ilike', "%{$search}%")
                            ->orWhere('nama_asset', 'ilike', "%{$search}%");
                    });
            });
        }

        $histories = $query->paginate(10)->withQueryString();

        return view('asset_histories.index', compact('histories'));
    }

    /**
     * Menampilkan form tambah history.
     */
    public function create()
    {
        $assets = Asset::orderBy('kode_asset')->get();

        return view('asset_histories.create', compact('assets'));
    }

    /**
     * Menyimpan history baru.
     */
    public function store(Request $request)
{
    $validated = $request->validate([
        'asset_id' => ['required', 'exists:assets,id'],
        'aktivitas' => ['required', 'string', 'max:255'],
        'deskripsi' => ['nullable', 'string'],
        'tanggal' => ['required', 'date'],
    ]);

    $history = AssetHistory::create($validated);

    return redirect()
        ->route('asset-histories.show', $history)
        ->with('success', 'Asset history berhasil ditambahkan.');
}
    /**
     * Menampilkan detail history.
     */
    public function show(AssetHistory $assetHistory)
    {
        $assetHistory->load(['asset', 'user']);

        return view('asset_histories.show', [
            'history' => $assetHistory,
        ]);
    }

    /**
     * Menampilkan form edit history.
     */
    public function edit(AssetHistory $assetHistory)
    {
        $assets = Asset::orderBy('kode_asset')->get();

        return view('asset_histories.edit', [
            'history' => $assetHistory,
            'assets' => $assets,
        ]);
    }

    /**
     * Memperbarui history.
     */
    public function update(Request $request, AssetHistory $assetHistory)
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'aktivitas' => ['required', 'string', 'max:255'],
            'deskripsi' => ['nullable', 'string'],
            'tanggal' => ['required', 'date'],
        ]);

        $assetHistory->update($validated);

        return redirect()
            ->route('asset-histories.show', $assetHistory)
            ->with('success', 'Asset history berhasil diperbarui.');
    }

    /**
     * Menghapus history.
     */
    public function destroy(AssetHistory $assetHistory)
    {
        $assetHistory->delete();

        return redirect()
            ->route('asset-histories.index')
            ->with('success', 'Asset history berhasil dihapus.');
    }
}