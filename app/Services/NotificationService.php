<?php

namespace App\Services;

use App\Models\Asset;
use App\Models\AssetLoan;
use App\Models\MaintenanceRecord;
use App\Models\Transfer;
use App\Models\AiPrediction;
use App\Models\User;
use Carbon\Carbon;

class NotificationService
{
    /**
     * Get aggregated system notifications tailored for the given user.
     *
     * @param User|null $user
     * @return array{unread_count: int, items: array}
     */
    public function getNotifications(?User $user = null): array
    {
        if (!$user) {
            return [
                'unread_count' => 0,
                'items' => [],
            ];
        }

        $role = strtolower((string) $user->role);
        $items = [];

        // 1. Peminjaman Terlambat (Overdue Loans)
        try {
            $overdueQuery = AssetLoan::with(['asset', 'user'])
                ->where('status', 'Dipinjam')
                ->whereNotNull('tanggal_rencana_kembali')
                ->where('tanggal_rencana_kembali', '<', now()->toDateString());

            if ($role === 'peminjam') {
                $overdueQuery->where('user_id', $user->id);
            }

            $overdueLoans = $overdueQuery->latest('tanggal_rencana_kembali')->take(5)->get();

            foreach ($overdueLoans as $loan) {
                $diffDays = Carbon::parse($loan->tanggal_rencana_kembali)->diffInDays(now()->startOfDay());
                $diffText = $diffDays == 0 ? 'Hari ini' : "Terlambat {$diffDays} hari";
                $assetName = $loan->asset?->nama_asset ?? 'Aset';
                $borrowerName = $loan->user?->name ?? 'Pengguna';

                $items[] = [
                    'id' => 'loan_overdue_' . $loan->id,
                    'type' => 'danger',
                    'priority' => 1,
                    'badge' => 'Terlambat',
                    'icon' => 'bi-exclamation-octagon-fill',
                    'title' => 'Pengembalian Aset Terlambat',
                    'message' => $role === 'peminjam'
                        ? "Aset \"{$assetName}\" telah melewati batas waktu pengembalian."
                        : "Aset \"{$assetName}\" dipinjam oleh {$borrowerName} belum dikembalikan.",
                    'time' => "{$diffText} (Tempo: " . Carbon::parse($loan->tanggal_rencana_kembali)->format('d M Y') . ')',
                    'url' => route('loans.index'),
                    'timestamp' => $loan->updated_at ? $loan->updated_at->timestamp : time(),
                ];
            }
        } catch (\Throwable $e) {
            // Silently catch in case of table/query issues
        }

        // 2. Peminjaman Jatuh Tempo Hari Ini
        try {
            $dueTodayQuery = AssetLoan::with(['asset', 'user'])
                ->where('status', 'Dipinjam')
                ->where('tanggal_rencana_kembali', now()->toDateString());

            if ($role === 'peminjam') {
                $dueTodayQuery->where('user_id', $user->id);
            }

            $dueTodayLoans = $dueTodayQuery->take(5)->get();

            foreach ($dueTodayLoans as $loan) {
                $assetName = $loan->asset?->nama_asset ?? 'Aset';
                $borrowerName = $loan->user?->name ?? 'Pengguna';

                $items[] = [
                    'id' => 'loan_due_today_' . $loan->id,
                    'type' => 'warning',
                    'priority' => 2,
                    'badge' => 'Hari Ini',
                    'icon' => 'bi-clock-history',
                    'title' => 'Jatuh Tempo Pengembalian',
                    'message' => $role === 'peminjam'
                        ? "Aset \"{$assetName}\" harus dikembalikan hari ini."
                        : "Peminjaman \"{$assetName}\" oleh {$borrowerName} jatuh tempo hari ini.",
                    'time' => 'Batas pengembalian hari ini',
                    'url' => route('loans.index'),
                    'timestamp' => $loan->updated_at ? $loan->updated_at->timestamp : time(),
                ];
            }
        } catch (\Throwable $e) {
        }

        // 3. Persetujuan Mutasi / Transfer
        try {
            if (in_array($role, ['admin', 'pimpinan'], true)) {
                $pendingTransfers = Transfer::with(['asset', 'targetLocation', 'requester'])
                    ->where('status', 'menunggu')
                    ->latest()
                    ->take(5)
                    ->get();

                foreach ($pendingTransfers as $tr) {
                    $assetName = $tr->asset?->nama_asset ?? 'Aset';
                    $targetLocName = $tr->targetLocation?->name ?? $tr->targetLocation?->nama ?? 'Lokasi Baru';
                    $requesterName = $tr->requester?->name ?? 'Staff';

                    $items[] = [
                        'id' => 'transfer_pending_' . $tr->id,
                        'type' => 'primary',
                        'priority' => 2,
                        'badge' => 'Persetujuan',
                        'icon' => 'bi-arrow-left-right',
                        'title' => 'Pengajuan Mutasi Aset',
                        'message' => "{$requesterName} mengajukan mutasi \"{$assetName}\" ke {$targetLocName}.",
                        'time' => $tr->created_at ? $tr->created_at->diffForHumans() : 'Menunggu',
                        'url' => route('approvals.index'),
                        'timestamp' => $tr->created_at ? $tr->created_at->timestamp : time(),
                    ];
                }
            } elseif ($role === 'staff') {
                $decidedTransfers = Transfer::with(['asset', 'targetLocation'])
                    ->where('requested_by', $user->id)
                    ->whereIn('status', ['disetujui', 'ditolak'])
                    ->where('decided_at', '>=', now()->subDays(3))
                    ->latest('decided_at')
                    ->take(5)
                    ->get();

                foreach ($decidedTransfers as $tr) {
                    $isApproved = $tr->status === 'disetujui';
                    $assetName = $tr->asset?->nama_asset ?? 'Aset';

                    $items[] = [
                        'id' => 'transfer_decided_' . $tr->id,
                        'type' => $isApproved ? 'success' : 'danger',
                        'priority' => 3,
                        'badge' => ucfirst($tr->status),
                        'icon' => $isApproved ? 'bi-check-circle-fill' : 'bi-x-circle-fill',
                        'title' => 'Mutasi ' . ucfirst($tr->status),
                        'message' => "Pengajuan mutasi untuk \"{$assetName}\" telah {$tr->status}.",
                        'time' => $tr->decided_at ? Carbon::parse($tr->decided_at)->diffForHumans() : 'Baru saja',
                        'url' => route('transfers.show', $tr->id),
                        'timestamp' => $tr->decided_at ? Carbon::parse($tr->decided_at)->timestamp : time(),
                    ];
                }
            }
        } catch (\Throwable $e) {
        }

        // 4. Jadwal Pemeliharaan (Maintenance Terjadwal)
        try {
            if (in_array($role, ['admin', 'staff', 'pimpinan'], true)) {
                $upcomingMaint = MaintenanceRecord::with('asset')
                    ->whereRaw('LOWER(status) = ?', ['terjadwal'])
                    ->where('tanggal', '<=', now()->addDays(7)->toDateString())
                    ->orderBy('tanggal')
                    ->take(5)
                    ->get();

                foreach ($upcomingMaint as $m) {
                    $maintDate = Carbon::parse($m->tanggal);
                    $isPast = $maintDate->lt(now()->startOfDay());
                    $assetName = $m->asset?->nama_asset ?? 'Aset';

                    $items[] = [
                        'id' => 'maintenance_' . $m->id,
                        'type' => $isPast ? 'danger' : 'warning',
                        'priority' => $isPast ? 1 : 3,
                        'badge' => $isPast ? 'Lewat Jadwal' : 'Maintenance',
                        'icon' => 'bi-tools',
                        'title' => $isPast ? 'Servis Aset Lewat Jadwal' : 'Jadwal Pemeliharaan',
                        'message' => "Pemeliharaan \"{$assetName}\" dijadwalkan pada " . $maintDate->format('d M Y'),
                        'time' => $isPast ? 'Tertunda sejak ' . $maintDate->format('d M Y') : 'Jadwal: ' . $maintDate->format('d M Y'),
                        'url' => route('maintenance-records.index'),
                        'timestamp' => $maintDate->timestamp,
                    ];
                }
            }
        } catch (\Throwable $e) {
        }

        // 5. Prediksi Kerusakan Berisiko Tinggi (AI Prediction)
        try {
            if (in_array($role, ['admin', 'staff', 'pimpinan'], true)) {
                $aiAlerts = AiPrediction::with('asset')
                    ->where(function ($q) {
                        $q->where('risk_category', 'like', '%tinggi%')
                          ->orWhere('risk_score', '>=', 0.75);
                    })
                    ->where('predicted_at', '>=', now()->subDays(14))
                    ->latest('predicted_at')
                    ->take(3)
                    ->get();

                foreach ($aiAlerts as $ai) {
                    $scorePct = round(((float) $ai->risk_score) * 100);
                    $assetName = $ai->asset?->nama_asset ?? 'Aset';

                    $items[] = [
                        'id' => 'ai_alert_' . $ai->id,
                        'type' => 'danger',
                        'priority' => 2,
                        'badge' => 'AI Alert (' . $scorePct . '%)',
                        'icon' => 'bi-cpu-fill',
                        'title' => 'Peringatan Risiko Kerusakan AI',
                        'message' => "Aset \"{$assetName}\" memiliki estimasi risiko kerusakan tinggi ({$scorePct}%).",
                        'time' => Carbon::parse($ai->predicted_at)->diffForHumans(),
                        'url' => route('ai.index'),
                        'timestamp' => Carbon::parse($ai->predicted_at)->timestamp,
                    ];
                }
            }
        } catch (\Throwable $e) {
        }

        // 6. Laporan Aset Rusak Baru
        try {
            if (in_array($role, ['admin', 'staff'], true)) {
                $damagedAssets = Asset::whereRaw('LOWER(status) = ?', ['rusak'])
                    ->where('updated_at', '>=', now()->subDays(3))
                    ->latest('updated_at')
                    ->take(3)
                    ->get();

                foreach ($damagedAssets as $ast) {
                    $items[] = [
                        'id' => 'asset_damaged_' . $ast->id,
                        'type' => 'danger',
                        'priority' => 3,
                        'badge' => 'Rusak',
                        'icon' => 'bi-exclamation-triangle-fill',
                        'title' => 'Laporan Aset Rusak',
                        'message' => "Aset \"{$ast->nama_asset}\" ({$ast->kode_asset}) dalam kondisi rusak.",
                        'time' => $ast->updated_at ? $ast->updated_at->diffForHumans() : 'Baru saja',
                        'url' => route('assets.show', $ast->id),
                        'timestamp' => $ast->updated_at ? $ast->updated_at->timestamp : time(),
                    ];
                }
            }
        } catch (\Throwable $e) {
        }

        // Urutkan berdasarkan prioritas (1: danger/kritis, 2: peringatan/approval, 3: info/jadwal), lalu waktu terbaru
        usort($items, function ($a, $b) {
            if ($a['priority'] === $b['priority']) {
                return $b['timestamp'] <=> $a['timestamp'];
            }
            return $a['priority'] <=> $b['priority'];
        });

        return [
            'unread_count' => count($items),
            'items' => $items,
        ];
    }
}
