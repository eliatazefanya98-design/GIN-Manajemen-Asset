<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\Location;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class TransferController extends Controller
{
    public function index(): View
    {
        $transfers = Transfer::with(['asset', 'originLocation', 'targetLocation', 'requester'])
            ->when(Auth::user()->role === 'staff', fn (Builder $query) => $query->where('requested_by', Auth::id()))
            ->latest()
            ->paginate(15);

        return view('transfers.index', compact('transfers'));
    }

    public function create(): View
    {
        return view('transfers.create', [
            'assets' => Asset::with('location')->orderBy('kode_asset')->get(),
            'locations' => Location::query()->orderBy('name')->get(),
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'asset_id' => ['required', 'exists:assets,id'],
            'target_location_id' => ['required', 'exists:locations,id'],
            'reason' => ['required', 'string', 'max:255'],
        ]);

        $transfer = DB::transaction(function () use ($validated): Transfer {
            $asset = Asset::query()->lockForUpdate()->findOrFail($validated['asset_id']);

            if (! in_array(strtolower($asset->status), ['aktif', 'active', 'digunakan'], true)) {
                throw ValidationException::withMessages([
                    'asset_id' => 'Aset harus berstatus aktif dan tidak sedang digunakan untuk proses lain.',
                ]);
            }

            if ((int) $asset->location_id === (int) $validated['target_location_id']) {
                throw ValidationException::withMessages([
                    'target_location_id' => 'Lokasi tujuan harus berbeda dari lokasi aset saat ini.',
                ]);
            }

            if (Transfer::query()->where('asset_id', $asset->id)->where('status', 'menunggu')->exists()) {
                throw ValidationException::withMessages([
                    'asset_id' => 'Aset sudah memiliki pengajuan transfer yang menunggu persetujuan.',
                ]);
            }

            $transfer = Transfer::create([
                'asset_id' => $asset->id,
                'origin_location_id' => $asset->location_id,
                'target_location_id' => $validated['target_location_id'],
                'requested_by' => Auth::id(),
                'reason' => $validated['reason'],
            ]);

            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => Auth::id(),
                'aktivitas' => 'Pengajuan Transfer',
                'deskripsi' => 'Pengajuan transfer lokasi menunggu persetujuan.',
                'tanggal' => now(),
            ]);

            return $transfer;
        });

        return redirect()->route('transfers.show', $transfer)->with('success', 'Pengajuan transfer dikirim untuk persetujuan.');
    }

    public function show(Transfer $transfer): View
    {
        abort_unless(Auth::user()->role === 'admin' || (int) $transfer->requested_by === (int) Auth::id(), 403);

        return view('transfers.show', [
            'transfer' => $transfer->load(['asset', 'originLocation', 'targetLocation', 'requester', 'decider']),
        ]);
    }

    public function approvalIndex(): View
    {
        $transfers = Transfer::with(['asset', 'originLocation', 'targetLocation', 'requester'])
            ->where('status', 'menunggu')
            ->latest()
            ->paginate(15);

        return view('approvals.index', compact('transfers'));
    }

    public function approvalShow(Transfer $transfer): View
    {
        return view('approvals.show', [
            'transfer' => $transfer->load(['asset', 'originLocation', 'targetLocation', 'requester', 'decider']),
        ]);
    }

    public function approve(Transfer $transfer): RedirectResponse
    {
        DB::transaction(function () use ($transfer): void {
            $lockedTransfer = Transfer::query()->lockForUpdate()->findOrFail($transfer->id);

            if ($lockedTransfer->status !== 'menunggu') {
                throw ValidationException::withMessages(['approval' => 'Pengajuan ini sudah diproses.']);
            }

            $asset = Asset::query()->lockForUpdate()->findOrFail($lockedTransfer->asset_id);

            if ((int) $asset->location_id !== (int) $lockedTransfer->origin_location_id) {
                throw ValidationException::withMessages(['approval' => 'Lokasi aset sudah berubah; pengajuan ini tidak dapat disetujui.']);
            }

            $asset->update(['location_id' => $lockedTransfer->target_location_id]);
            $lockedTransfer->update([
                'status' => 'disetujui',
                'decided_by' => Auth::id(),
                'decided_at' => now(),
            ]);

            AssetHistory::create([
                'asset_id' => $asset->id,
                'user_id' => Auth::id(),
                'aktivitas' => 'Transfer Disetujui',
                'deskripsi' => 'Aset dipindahkan ke lokasi ' . ($lockedTransfer->targetLocation?->name ?? $lockedTransfer->targetLocation?->nama ?? $lockedTransfer->target_location_id) . '.',
                'tanggal' => now(),
            ]);
        });

        return redirect()->route('approvals.index')->with('success', 'Pengajuan transfer disetujui.');
    }

    public function reject(Request $request, Transfer $transfer): RedirectResponse
    {
        $validated = $request->validate([
            'decision_note' => ['required', 'string', 'max:255'],
        ]);

        DB::transaction(function () use ($transfer, $validated): void {
            $lockedTransfer = Transfer::query()->lockForUpdate()->findOrFail($transfer->id);

            if ($lockedTransfer->status !== 'menunggu') {
                throw ValidationException::withMessages(['approval' => 'Pengajuan ini sudah diproses.']);
            }

            $lockedTransfer->update([
                'status' => 'ditolak',
                'decided_by' => Auth::id(),
                'decision_note' => $validated['decision_note'],
                'decided_at' => now(),
            ]);
        });

        return redirect()->route('approvals.index')->with('success', 'Pengajuan transfer ditolak.');
    }
}