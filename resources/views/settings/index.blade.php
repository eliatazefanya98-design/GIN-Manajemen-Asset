@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="container-fluid settings-page">

    {{-- HEADER --}}
    <div class="dashboard-header">
        <div>
            <h2>Pengaturan Sistem</h2>
            <p class="text-muted mb-0">Kelola identitas instansi, format nomor aset, tema tampilan, serta kebijakan konfigurasi.</p>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" form="reset-settings-form" class="btn btn-outline-secondary" onclick="return confirm('Kembalikan semua pengaturan ke nilai default?')">
                <i class="bi bi-arrow-counterclockwise me-1"></i> Reset Default
            </button>
            <button type="submit" form="settings-form" class="btn btn-primary">
                <i class="bi bi-check2 me-1"></i> Simpan Perubahan
            </button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill me-2 fs-5"></i>
            <div>{{ session('success') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form id="reset-settings-form" method="POST" action="{{ route('settings.reset') }}" class="d-none">
        @csrf
    </form>

    <form id="settings-form" method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row g-4">
            {{-- MAIN COLUMN --}}
            <div class="col-lg-8">
                {{-- IDENTITAS & REGIONAL --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <strong class="d-inline-flex align-items-center gap-2">
                            <i class="bi bi-building text-primary"></i> Identitas Instansi & Regional
                        </strong>
                        @if(!empty($settings['updated_at']))
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle-fill me-1"></i> Tersimpan {{ \Illuminate\Support\Carbon::parse($settings['updated_at'])->format('d/m/Y H:i') }}
                            </span>
                        @endif
                    </div>

                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="organization_name" class="form-label">Nama Organisasi / Entitas <span class="text-danger">*</span></label>
                                <input id="organization_name" name="organization_name" class="form-control" value="{{ old('organization_name', $settings['organization_name']) }}" required>
                                <small class="text-muted">Nama resmi perusahaan atau instansi Anda</small>
                            </div>

                            <div class="col-md-6">
                                <label for="organization_unit" class="form-label">Unit / Divisi Utama</label>
                                <input id="organization_unit" name="organization_unit" class="form-control" value="{{ old('organization_unit', $settings['organization_unit']) }}">
                                <small class="text-muted">Divisi pengelola sistem aset</small>
                            </div>

                            <div class="col-md-6">
                                <label for="timezone" class="form-label">Zona Waktu Default</label>
                                <select id="timezone" name="timezone" class="form-select">
                                    <option value="Asia/Jakarta (GMT+07:00)" @selected($settings['timezone'] === 'Asia/Jakarta (GMT+07:00)')>WIB - Asia/Jakarta (GMT+07:00)</option>
                                    <option value="Asia/Makassar (GMT+08:00)" @selected($settings['timezone'] === 'Asia/Makassar (GMT+08:00)')>WITA - Asia/Makassar (GMT+08:00)</option>
                                    <option value="Asia/Jayapura (GMT+09:00)" @selected($settings['timezone'] === 'Asia/Jayapura (GMT+09:00)')>WIT - Asia/Jayapura (GMT+09:00)</option>
                                </select>
                            </div>

                            <div class="col-md-6">
                                <label for="date_format" class="form-label">Format Penulisan Tanggal</label>
                                <select id="date_format" name="date_format" class="form-select">
                                    <option value="DD/MM/YYYY" @selected($settings['date_format'] === 'DD/MM/YYYY')>DD/MM/YYYY</option>
                                    <option value="YYYY-MM-DD" @selected($settings['date_format'] === 'YYYY-MM-DD')>YYYY-MM-DD</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label class="form-label" for="theme-toggle">Tema Tampilan Antarmuka</label>
                                <div id="theme-toggle" class="d-flex gap-3 mt-1">
                                    <label class="theme-card-option flex-grow-1 p-3 rounded border {{ ($settings['theme'] ?? 'light') === 'light' ? 'border-primary bg-primary-subtle' : 'bg-light' }} d-flex align-items-center gap-3 cursor-pointer">
                                        <input type="radio" name="theme" value="light" @checked(($settings['theme'] ?? 'light') === 'light') class="form-check-input">
                                        <div>
                                            <strong class="d-block"><i class="bi bi-sun-fill text-warning me-1"></i> Mode Terang (Light)</strong>
                                            <small class="text-muted">Tampilan bersih dengan palet warna cerah default</small>
                                        </div>
                                    </label>

                                    <label class="theme-card-option flex-grow-1 p-3 rounded border {{ ($settings['theme'] ?? 'light') === 'dark' ? 'border-primary bg-primary-subtle' : 'bg-light' }} d-flex align-items-center gap-3 cursor-pointer">
                                        <input type="radio" name="theme" value="dark" @checked(($settings['theme'] ?? 'light') === 'dark') class="form-check-input">
                                        <div>
                                            <strong class="d-block"><i class="bi bi-moon-stars-fill text-primary me-1"></i> Mode Gelap (Dark)</strong>
                                            <small class="text-muted">Tampilan kontras tinggi yang nyaman di mata</small>
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FORMAT PENOMORAN --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <strong class="d-inline-flex align-items-center gap-2">
                            <i class="bi bi-upc-scan text-primary"></i> Format Penomoran Kode Aset
                        </strong>
                    </div>

                    <div class="card-body">
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label for="asset_prefix" class="form-label">Prefix Kode</label>
                                <input id="asset_prefix" name="asset_prefix" class="form-control" value="{{ old('asset_prefix', $settings['asset_prefix']) }}">
                            </div>

                            <div class="col-md-2">
                                <label for="asset_separator" class="form-label">Pemisah</label>
                                <input id="asset_separator" name="asset_separator" class="form-control text-center" value="{{ old('asset_separator', $settings['asset_separator']) }}">
                            </div>

                            <div class="col-md-3">
                                <label for="asset_digits" class="form-label">Jumlah Digit</label>
                                <input id="asset_digits" name="asset_digits" type="number" min="2" max="8" class="form-control" value="{{ old('asset_digits', $settings['asset_digits']) }}">
                            </div>

                            <div class="col-md-3">
                                <label for="asset_next_number" class="form-label">Nomor Urut</label>
                                <input id="asset_next_number" name="asset_next_number" type="number" min="1" class="form-control" value="{{ old('asset_next_number', $settings['asset_next_number']) }}">
                            </div>
                        </div>

                        {{-- PREVIEW --}}
                        <div class="p-3 rounded bg-dark text-white d-flex justify-content-between align-items-center flex-wrap gap-2">
                            <div>
                                <small class="text-secondary text-uppercase d-block" style="font-size: 11px; letter-spacing: 0.05em;">PRATINJAU HASIL KODE OTOMATIS</small>
                                <strong id="asset-code-preview" class="fs-5 font-monospace text-warning">{{ $settings['asset_prefix'] }}{{ $settings['asset_separator'] }}{{ str_pad((string) $settings['asset_next_number'], $settings['asset_digits'], '0', STR_PAD_LEFT) }}</strong>
                            </div>
                            <button type="button" id="copy-asset-code" class="btn btn-sm btn-outline-light">
                                <i class="bi bi-copy me-1"></i> Salin Contoh Kode
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            {{-- SIDE COLUMN --}}
            <div class="col-lg-4">
                {{-- LOGO PANEL --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <strong class="d-inline-flex align-items-center gap-2">
                            <i class="bi bi-image text-primary"></i> Logo & Identitas Brand
                        </strong>
                    </div>

                    <div class="card-body text-center">
                        <div class="mb-3">
                            @if($logoUrl)
                                <img class="logo-preview-image rounded border shadow-sm" src="{{ $logoUrl }}" alt="Logo organisasi" style="width: 80px; height: 80px; object-fit: contain; padding: 6px;">
                            @else
                                <div class="stat-icon blue mx-auto" style="width: 80px; height: 80px; font-size: 32px; border-radius: 12px;">
                                    <i class="bi bi-box-seam"></i>
                                </div>
                            @endif
                        </div>

                        <h6 class="fw-bold mb-1">{{ $settings['organization_name'] }}</h6>
                        <small class="text-muted d-block mb-3">PNG, JPG, WEBP, atau SVG · Maks. 4 MB</small>

                        <label for="logo" class="btn btn-outline-primary btn-sm">
                            <i class="bi bi-upload me-1"></i> Pilih File Logo
                        </label>
                        <input id="logo" name="logo" type="file" accept=".png,.jpg,.jpeg,.webp,.svg,image/png,image/jpeg,image/webp,image/svg+xml" class="d-none">
                    </div>
                </div>

                {{-- SECURITY & NOTIF --}}
                <div class="card shadow-sm mb-4">
                    <div class="card-header">
                        <strong class="d-inline-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-success"></i> Keamanan Sistem
                        </strong>
                    </div>

                    <div class="card-body">
                        <p class="text-muted small mb-3">
                            Seluruh metadata inventaris dan data pengguna dilindungi dengan enkripsi AES-256 oleh sistem.
                        </p>

                        <div class="d-flex align-items-center justify-content-between p-2 rounded bg-light mb-3">
                            <span class="small text-muted"><i class="bi bi-lock-fill text-success me-1"></i> Status Enkripsi</span>
                            <span class="badge bg-success">Active AES-256</span>
                        </div>

                        <hr class="my-3">

                        <div class="form-check form-switch d-flex justify-content-between align-items-center p-0">
                            <div>
                                <label class="form-check-label fw-bold small d-block mb-0" for="email_notifications">Email Notifications</label>
                                <small class="text-muted">Notifikasi perubahan status aset</small>
                            </div>
                            <input class="form-check-input ms-auto" type="checkbox" id="email_notifications" name="email_notifications" value="1" @checked($settings['email_notifications']) style="cursor: pointer;">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>

</div>
@endsection

@push('scripts')
<script>
(() => {
    const prefix = document.getElementById('asset_prefix');
    const separator = document.getElementById('asset_separator');
    const digits = document.getElementById('asset_digits');
    const nextNumber = document.getElementById('asset_next_number');
    const preview = document.getElementById('asset-code-preview');
    const copyButton = document.getElementById('copy-asset-code');

    const updatePreview = () => {
        if (!preview) return;
        const length = Math.max(2, Number(digits?.value) || 2);
        const number = String(Math.max(1, Number(nextNumber?.value) || 1)).padStart(length, '0');
        preview.textContent = `${prefix?.value || 'AST'}${separator?.value || '-'}${number}`;
    };

    [prefix, separator, digits, nextNumber].filter(Boolean).forEach((field) => field.addEventListener('input', updatePreview));

    if (copyButton && preview) {
        copyButton.addEventListener('click', async () => {
            await navigator.clipboard.writeText(preview.textContent);
            copyButton.innerHTML = '<i class="bi bi-check2"></i> Tersalin';
            setTimeout(() => { copyButton.innerHTML = '<i class="bi bi-copy"></i> Salin Contoh Kode'; }, 1400);
        });
    }
})();
</script>
@endpush