<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\AssetLoan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class LoanController extends Controller
{
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('search', ''));
        $isBorrower = Auth::user()->role === 'peminjam';

        $availableAssets = Asset::with(['category', 'location'])
            ->whereRaw('LOWER(status) = ?', ['aktif'])
            ->whereDoesntHave('loans', fn (Builder $query) => $query->where('status', 'Dipinjam'))
            ->when($search !== '', fn (Builder $query) => $query->where(function (Builder $searchQuery) use ($search) {
                $searchQuery->where('kode_asset', 'ilike', "%{$search}%")
                    ->orWhere('nama_asset', 'ilike', "%{$search}%");
            }))
            ->orderBy('nama_asset')
            ->paginate(12, ['*'], 'assets_page')
            ->withQueryString();

        $activeLoans = AssetLoan::with(['asset', 'user'])
            ->where('status', 'Dipinjam')
            ->when($isBorrower, fn (Builder $query) => $query->where('user_id', Auth::id()))
            ->latest('tanggal_pinjam')
            ->paginate(10, ['*'], 'loans_page')
            ->withQueryString();

        return view('loans.index', compact('availableAssets', 'activeLoans', 'search', 'isBorrower'));
    }

    public function create(Asset $asset): View
    {
        abort_unless(strtolower($asset->status) === 'aktif' && ! $asset->loans()->where('status', 'Dipinjam')->exists(), 404);

        return view('loans.create', compact('asset'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'tanggal_pinjam' => ['required', 'date'],
            'tanggal_rencana_kembali' => ['required', 'date', 'after_or_equal:tanggal_pinjam'],
            'keperluan' => ['required', 'string', 'max:255'],
        ]);

        $loan = DB::transaction(function () use ($validated): AssetLoan {
            $asset = Asset::query()->lockForUpdate()->findOrFail($validated['asset_id']);

            if (strtolower($asset->status) !== 'aktif' || $asset->loans()->where('status', 'Dipinjam')->exists()) {
                throw ValidationException::withMessages(['asset_id' => 'Aset sedang tidak tersedia.']);
            }

            $loan = AssetLoan::create([
                ...$validated,
                'user_id' => Auth::id(),
                'kondisi_sebelum' => $asset->kondisi,
                'status' => 'Dipinjam',
            ]);

            $asset->update(['status' => 'Dipinjam']);

            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => Auth::id(),
                'aktivitas' => 'Peminjaman',
                'deskripsi' => 'Aset dipinjam hingga ' . $validated['tanggal_rencana_kembali'] . '.',
                'tanggal' => now(),
            ]);

            return $loan;
        });

        return redirect()->route('loans.index')->with('success', 'Peminjaman berhasil dicatat.');
    }

    public function returnAsset(Request $request, AssetLoan $loan): RedirectResponse
    {
        $validated = $request->validate([
            'tanggal_kembali' => ['required', 'date', 'after_or_equal:' . $loan->getRawOriginal('tanggal_pinjam')],
            'kondisi_sesudah' => ['required', Rule::in(['Baik', 'Rusak Ringan', 'Rusak Berat'])],
            'catatan' => ['nullable', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($loan, $validated): void {
            $lockedLoan = AssetLoan::query()
                ->where('user_id', Auth::id())
                ->lockForUpdate()
                ->findOrFail($loan->id);

            if ($lockedLoan->status !== 'Dipinjam') {
                throw ValidationException::withMessages(['loan' => 'Peminjaman ini sudah dikembalikan atau tidak aktif.']);
            }

            $asset = Asset::query()->lockForUpdate()->findOrFail($lockedLoan->asset_id);
            $damaged = $validated['kondisi_sesudah'] !== 'Baik';

            $lockedLoan->update([
                'tanggal_kembali' => $validated['tanggal_kembali'],
                'kondisi_sesudah' => $validated['kondisi_sesudah'],
                'catatan' => $validated['catatan'] ?? null,
                'status' => 'Dikembalikan',
            ]);

            $asset->update([
                'kondisi' => $validated['kondisi_sesudah'],
                'status' => $damaged ? 'Maintenance' : 'Aktif',
            ]);

            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => Auth::id(),
                'aktivitas' => 'Pengembalian',
                'deskripsi' => 'Aset dikembalikan dengan kondisi ' . $validated['kondisi_sesudah'] . '. ' . ($validated['catatan'] ?? ''),
                'tanggal' => now(),
            ]);
        });

        return redirect()->route('loans.history')->with('success', 'Pengembalian berhasil dicatat.');
    }

    public function history(): View
    {
        $loans = AssetLoan::with(['asset', 'user'])
            ->when(Auth::user()->role === 'peminjam', fn (Builder $query) => $query->where('user_id', Auth::id()))
            ->latest('tanggal_pinjam')
            ->paginate(15);

        return view('loans.history', compact('loans'));
    }
}