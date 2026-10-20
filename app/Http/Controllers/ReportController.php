<?php

namespace App\Http\Controllers;

use App\Models\Asset;
use App\Models\AssetHistory;
use App\Models\MaintenanceRecord;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\Rule;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use Symfony\Component\HttpFoundation\Response;

class ReportController extends Controller
{
    private const REPORTS = [
        'assets' => 'Aset',
        'maintenance' => 'Maintenance',
        'history' => 'Riwayat Aset',
        'peminjaman' => 'Peminjaman',
        'stock_opname' => 'Stock Opname',
    ];

    public function index(Request $request): Response|\Illuminate\Contracts\View\View
    {
        $filters = $this->validatedFilters($request);
        [$headers, $rows] = $this->reportData($filters['type'], $filters['start_date'], $filters['end_date']);

        return view('reports.index', [
            'types' => self::REPORTS,
            'filters' => $filters,
            'headers' => $headers,
            'rows' => $rows,
            'reportTitle' => self::REPORTS[$filters['type']],
        ]);
    }

    public function export(Request $request, string $format): Response
    {
        abort_unless(in_array($format, ['pdf', 'xlsx'], true), 404);

        $filters = $this->validatedFilters($request);
        [$headers, $rows] = $this->reportData($filters['type'], $filters['start_date'], $filters['end_date']);
        $title = self::REPORTS[$filters['type']];
        $filename = 'laporan-' . $filters['type'] . '-' . $filters['start_date'] . '-sampai-' . $filters['end_date'];

        if ($format === 'pdf') {
            return Pdf::loadView('reports.pdf', compact('headers', 'rows', 'title', 'filters'))
                ->setPaper('a4', 'landscape')
                ->download($filename . '.pdf');
        }

        return $this->downloadSpreadsheet($headers, $rows, $filename . '.xlsx');
    }

    private function validatedFilters(Request $request): array
    {
        $validated = $request->validate([
            'type' => ['nullable', Rule::in(array_keys(self::REPORTS))],
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        return [
            'type' => $validated['type'] ?? 'assets',
            'start_date' => $validated['start_date'] ?? now()->startOfMonth()->toDateString(),
            'end_date' => $validated['end_date'] ?? now()->toDateString(),
        ];
    }

    private function reportData(string $type, string $startDate, string $endDate): array
    {
        return match ($type) {
            'maintenance' => $this->maintenanceRows($startDate, $endDate),
            'history' => $this->historyRows($startDate, $endDate),
            'peminjaman' => $this->loanRows($startDate, $endDate),
            'stock_opname' => $this->stockOpnameRows($startDate, $endDate),
            default => $this->assetRows($startDate, $endDate),
        };
    }

    private function assetRows(string $startDate, string $endDate): array
    {
        $records = Asset::with(['category', 'location', 'vendor'])
            ->whereDate('tanggal_perolehan', '>=', $startDate)
            ->whereDate('tanggal_perolehan', '<=', $endDate)
            ->orderBy('tanggal_perolehan')
            ->get();

        return [
            ['Kode Aset', 'Nama Aset', 'Kategori', 'Lokasi', 'Vendor', 'Kondisi', 'Status', 'Tanggal Perolehan', 'Harga'],
            $records->map(fn (Asset $asset) => [
                $asset->kode_asset,
                $asset->nama_asset,
                $asset->category?->name ?? $asset->category?->nama ?? '-',
                $asset->location?->name ?? $asset->location?->nama ?? '-',
                $asset->vendor?->name ?? $asset->vendor?->nama ?? '-',
                $asset->kondisi,
                $asset->status,
                Carbon::parse($asset->tanggal_perolehan)->toDateString(),
                $asset->harga,
            ])->all(),
        ];
    }

    private function maintenanceRows(string $startDate, string $endDate): array
    {
        $records = MaintenanceRecord::with('asset')
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->orderBy('tanggal')
            ->get();

        return [
            ['Tanggal', 'Kode Aset', 'Nama Aset', 'Jenis', 'Keluhan', 'Teknisi', 'Status', 'Biaya', 'Hasil Tindakan'],
            $records->map(fn (MaintenanceRecord $record) => [
                Carbon::parse($record->tanggal)->toDateString(),
                $record->asset?->kode_asset,
                $record->asset?->nama_asset,
                $record->jenis,
                $record->keluhan,
                $record->teknisi,
                $record->status,
                $record->biaya,
                $record->hasil_tindakan,
            ])->all(),
        ];
    }

    private function historyRows(string $startDate, string $endDate): array
    {
        $records = AssetHistory::with(['asset', 'user'])
            ->whereDate('tanggal', '>=', $startDate)
            ->whereDate('tanggal', '<=', $endDate)
            ->orderBy('tanggal')
            ->get();

        return [
            ['Tanggal', 'Kode Aset', 'Nama Aset', 'Aktivitas', 'Pengguna', 'Deskripsi'],
            $records->map(fn (AssetHistory $history) => [
                $history->tanggal?->format('Y-m-d H:i:s'),
                $history->asset?->kode_asset,
                $history->asset?->nama_asset,
                $history->aktivitas,
                $history->user?->name,
                $history->deskripsi,
            ])->all(),
        ];
    }

    private function loanRows(string $startDate, string $endDate): array
    {
        $headers = ['Tanggal Pinjam', 'Kode Aset', 'Nama Aset', 'Peminjam', 'Rencana Kembali', 'Tanggal Kembali', 'Keperluan', 'Status'];

        if (! Schema::hasTable('asset_loans')) {
            return [$headers, []];
        }

        $rows = DB::table('asset_loans')
            ->leftJoin('assets', 'assets.id', '=', 'asset_loans.asset_id')
            ->leftJoin('users', 'users.id', '=', 'asset_loans.user_id')
            ->whereBetween('asset_loans.tanggal_pinjam', [$startDate, $endDate])
            ->orderBy('asset_loans.tanggal_pinjam')
            ->get([
                'asset_loans.tanggal_pinjam', 'assets.kode_asset', 'assets.nama_asset', 'users.name',
                'asset_loans.tanggal_rencana_kembali', 'asset_loans.tanggal_kembali',
                'asset_loans.keperluan', 'asset_loans.status',
            ])
            ->map(fn ($loan) => [
                $loan->tanggal_pinjam,
                $loan->kode_asset,
                $loan->nama_asset,
                $loan->name,
                $loan->tanggal_rencana_kembali,
                $loan->tanggal_kembali,
                $loan->keperluan,
                $loan->status,
            ])->all();

        return [$headers, $rows];
    }

    private function stockOpnameRows(string $startDate, string $endDate): array
    {
        $headers = ['Tanggal Opname', 'Kode Aset', 'Nama Aset', 'Kondisi Sistem', 'Kondisi Aktual', 'Lokasi Sistem', 'Lokasi Aktual', 'Keberadaan', 'Hasil', 'Catatan'];

        if (! Schema::hasTable('stock_opnames')) {
            return [$headers, []];
        }

        $rows = DB::table('stock_opnames')
            ->leftJoin('assets', 'assets.id', '=', 'stock_opnames.asset_id')
            ->whereBetween('stock_opnames.tanggal_opname', [$startDate, $endDate])
            ->orderBy('stock_opnames.tanggal_opname')
            ->get([
                'stock_opnames.tanggal_opname', 'assets.kode_asset', 'assets.nama_asset',
                'stock_opnames.kondisi_sistem', 'stock_opnames.kondisi_aktual',
                'stock_opnames.lokasi_sistem', 'stock_opnames.lokasi_aktual',
                'stock_opnames.keberadaan', 'stock_opnames.hasil', 'stock_opnames.catatan',
            ])
            ->map(fn ($opname) => [
                $opname->tanggal_opname,
                $opname->kode_asset,
                $opname->nama_asset,
                $opname->kondisi_sistem,
                $opname->kondisi_aktual,
                $opname->lokasi_sistem,
                $opname->lokasi_aktual,
                $opname->keberadaan,
                $opname->hasil,
                $opname->catatan,
            ])->all();

        return [$headers, $rows];
    }

    private function downloadSpreadsheet(array $headers, array $rows, string $filename): Response
    {
        abort_unless(extension_loaded('zip'), 500, 'Extension PHP ZIP harus aktif untuk membuat file XLSX.');

        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        foreach (array_merge([$headers], $rows) as $rowIndex => $row) {
            foreach ($row as $columnIndex => $value) {
                $coordinate = $sheet->getCell([$columnIndex + 1, $rowIndex + 1])->getCoordinate();
                $sheet->setCellValueExplicit($coordinate, (string) ($value ?? ''), DataType::TYPE_STRING);
            }
        }

        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->getFont()->setBold(true);
        $sheet->freezePane('A2');

        ob_start();
        (new Xlsx($spreadsheet))->save('php://output');
        $content = ob_get_clean();
        $spreadsheet->disconnectWorksheets();

        return response($content, 200, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}