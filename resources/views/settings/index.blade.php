@extends('layouts.app')

@section('title', 'Pengaturan Sistem')

@section('content')
<div class="settings-page">
    <div class="settings-heading">
        <div>
            <div class="settings-eyebrow"><i class="bi bi-sliders2"></i> PENGATURAN SISTEM</div>
            <h3>Pengaturan Sistem</h3>
            <p>Kelola informasi perusahaan, format nomor aset, serta kebijakan konfigurasi sistem.</p>
        </div>
        <div class="settings-actions">
            <button type="submit" form="reset-settings-form" class="btn btn-light"><i class="bi bi-arrow-counterclockwise"></i> Reset Default</button>
            <button type="submit" form="settings-form" class="btn btn-primary"><i class="bi bi-check2"></i> Simpan Perubahan</button>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success settings-alert"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}</div>
    @endif

    <nav class="settings-tabs" aria-label="Navigasi pengaturan">
        <a class="active" href="#general"><i class="bi bi-building"></i> Umum</a>
    </nav>

    <form id="reset-settings-form" method="POST" action="{{ route('settings.reset') }}" class="d-none">
        @csrf
    </form>

    <form id="settings-form" method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        <div class="settings-grid">
            <div class="settings-main-column">
                <section class="settings-panel" id="general">
                    <div class="settings-panel-title"><span class="settings-icon blue"><i class="bi bi-building"></i></span><div><h5>Identitas Instansi & Regional</h5><small>Informasi utama yang tampil pada dokumen dan dashboard</small></div><span class="saved-pill"><i class="bi bi-check-circle-fill"></i> Tersimpan {{ !empty($settings['updated_at']) ? \Illuminate\Support\Carbon::parse($settings['updated_at'])->format('d/m/Y H:i') : 'hari ini' }}</span></div>
                    <div class="settings-form-grid">
                        <div><label for="organization_name">Nama Organisasi / Entitas</label><input id="organization_name" name="organization_name" class="form-control" value="{{ old('organization_name', $settings['organization_name']) }}"><small class="form-hint">Nama resmi perusahaan atau instansi</small></div>
                        <div><label for="organization_unit">Unit / Divisi Utama</label><input id="organization_unit" name="organization_unit" class="form-control" value="{{ old('organization_unit', $settings['organization_unit']) }}"></div>
                        <div><label for="timezone">Zona Waktu Default</label><select id="timezone" name="timezone" class="form-select"><option value="Asia/Jakarta (GMT+07:00)" @selected($settings['timezone'] === 'Asia/Jakarta (GMT+07:00)')>WIB - Asia/Jakarta (GMT+07:00)</option><option value="Asia/Makassar (GMT+08:00)" @selected($settings['timezone'] === 'Asia/Makassar (GMT+08:00)')>WITA - Asia/Makassar (GMT+08:00)</option><option value="Asia/Jayapura (GMT+09:00)" @selected($settings['timezone'] === 'Asia/Jayapura (GMT+09:00)')>WIT - Asia/Jayapura (GMT+09:00)</option></select></div>
                        <div><label for="date_format">Mata Uang & Penulisan Aset</label><select id="date_format" name="date_format" class="form-select"><option value="DD/MM/YYYY" @selected($settings['date_format'] === 'DD/MM/YYYY')>DD/MM/YYYY</option><option value="YYYY-MM-DD" @selected($settings['date_format'] === 'YYYY-MM-DD')>YYYY-MM-DD</option></select></div>
                        <div>
                            <label class="form-label" for="theme-toggle">Tema Tampilan</label>
                            <div id="theme-toggle" class="theme-options">
                                <label class="theme-option"><input type="radio" name="theme" value="light" @checked(($settings['theme'] ?? 'light') === 'light')><span class="theme-option-label"><i class="bi bi-sun"></i> Terang</span></label>
                                <label class="theme-option"><input type="radio" name="theme" value="dark" @checked(($settings['theme'] ?? 'light') === 'dark')><span class="theme-option-label"><i class="bi bi-moon-stars"></i> Gelap</span></label>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="settings-panel" id="format">
                    <div class="settings-panel-title"><span class="settings-icon coral"><i class="bi bi-upc-scan"></i></span><div><h5>Format Penomoran Kode Aset</h5><small>Atur pola kode unik untuk setiap aset baru</small></div></div>
                    <div class="settings-form-grid code-fields">
                        <div><label for="asset_prefix">Prefix Kode</label><input id="asset_prefix" name="asset_prefix" class="form-control" value="{{ old('asset_prefix', $settings['asset_prefix']) }}"></div>
                        <div><label for="asset_separator">Pemisah</label><input id="asset_separator" name="asset_separator" class="form-control" value="{{ old('asset_separator', $settings['asset_separator']) }}"></div>
                        <div><label for="asset_digits">Jumlah Digit</label><input id="asset_digits" name="asset_digits" type="number" min="2" max="8" class="form-control" value="{{ old('asset_digits', $settings['asset_digits']) }}"></div>
                        <div><label for="asset_next_number">Nomor Urut Berikutnya</label><input id="asset_next_number" name="asset_next_number" type="number" min="1" class="form-control" value="{{ old('asset_next_number', $settings['asset_next_number']) }}"></div>
                    </div>
                    <div class="format-preview"><div><small>PRATINJAU HASIL KODE OTOMATIS</small><strong id="asset-code-preview">{{ $settings['asset_prefix'] }}{{ $settings['asset_separator'] }}{{ str_pad((string) $settings['asset_next_number'], $settings['asset_digits'], '0', STR_PAD_LEFT) }}</strong></div><button type="button" id="copy-asset-code" class="btn btn-dark btn-sm"><i class="bi bi-copy"></i> Salin Contoh Kode</button></div>
                </section>
            </div>

            <aside class="settings-side-column">
                <section class="settings-panel logo-panel">
                    <div class="settings-panel-title"><div><h5>Logo & Brand Asset</h5><small>PNG / SVG (512×512)</small></div><i class="bi bi-three-dots"></i></div>
                    <div class="logo-dropzone">@if($logoUrl)<img class="logo-preview-image" src="{{ $logoUrl }}" alt="Logo organisasi">@else<div class="logo-preview"><i class="bi bi-box-seam"></i></div>@endif<strong>{{ $settings['organization_name'] }} Asset Brand</strong><small>PNG, JPG, WEBP, atau SVG · Maks. 4 MB</small><label for="logo" class="btn btn-outline-primary btn-sm">Pilih File Logo</label><input id="logo" name="logo" type="file" accept=".png,.jpg,.jpeg,.webp,.svg,image/png,image/jpeg,image/webp,image/svg+xml" class="d-none"></div>
                    <div class="storage-row"><span>Ukuran File Maks.</span><strong>4.0 MB</strong></div>
                </section>

                <section class="settings-panel" id="security">
                    <div class="settings-panel-title"><span class="settings-icon green"><i class="bi bi-shield-check"></i></span><div><h5>Status Enkripsi Master</h5><small>Perlindungan data sistem aktif</small></div></div>
                    <p class="security-copy">Seluruh metadata aset, rincian biaya pengadaan, dan tag identifikasi dilindungi dengan enkripsi AES-256 oleh sistem.</p>
                    <div class="encryption-status"><span><i class="bi bi-lock-fill"></i> STATUS</span><strong>ACTIVE <small>AES-256</small></strong><i class="bi bi-check-circle-fill"></i></div>
                </section>

                <section class="settings-panel notification-panel" id="notifications">
                    <label class="toggle-row"><span><strong>Email Notifications</strong><small>Notifikasi perubahan status aset</small></span><input type="checkbox" name="email_notifications" value="1" @checked($settings['email_notifications'])></label>
                </section>
            </aside>
        </div>
    </form>
</div>
@endsection

@push('styles')
<style>
.settings-page { max-width: 1180px; margin: 0 auto; color: #293750; }
.settings-heading { display: flex; justify-content: space-between; align-items: end; gap: 20px; margin-bottom: 17px; }
.settings-eyebrow { color: #7b8aa0; font-size: 9px; font-weight: 700; letter-spacing: .08em; }.settings-eyebrow i { color: #2f6fed; margin-right: 4px; }.settings-heading h3 { margin: 4px 0 3px; font-size: 20px; }.settings-heading p { margin: 0; color: #8a96a8; font-size: 10px; }.settings-actions { display: flex; gap: 8px; }.settings-actions .btn { font-size: 10px; padding: 7px 10px; }.settings-actions .btn-primary { background: #f04b3f; border-color: #f04b3f; }.settings-actions .btn-primary:hover { background: #d93d32; border-color: #d93d32; }
.settings-tabs { display: flex; gap: 18px; align-items: center; padding: 0 8px; border-bottom: 1px solid #e4eaf2; margin-bottom: 18px; }.settings-tabs a { display: inline-flex; gap: 6px; align-items: center; padding: 9px 8px; color: #8895a8; font-size: 10px; text-decoration: none; border-bottom: 2px solid transparent; }.settings-tabs a.active { color: #f04b3f; border-color: #f04b3f; font-weight: 700; }.settings-tabs a i { font-size: 11px; }
.settings-alert { display: flex; gap: 8px; align-items: center; margin-bottom: 14px; }.settings-grid { display: grid; grid-template-columns: minmax(0, 1.65fr) minmax(250px, .75fr); gap: 16px; align-items: start; }.settings-main-column, .settings-side-column { display: grid; gap: 16px; }.settings-panel { padding: 16px; border: 1px solid #e5ebf3; border-radius: 9px; background: #fff; box-shadow: 0 4px 16px rgba(48, 67, 96, .035); }.settings-panel-title { display: flex; gap: 9px; align-items: center; margin-bottom: 16px; }.settings-panel-title h5 { margin: 0; font-size: 11px; }.settings-panel-title small { display: block; margin-top: 3px; color: #97a3b3; font-size: 8px; }.settings-icon { display: grid; place-items: center; width: 28px; height: 28px; border-radius: 6px; font-size: 13px; }.settings-icon.blue { color: #2f6fed; background: #eaf2ff; }.settings-icon.coral { color: #f04b68; background: #ffebef; }.settings-icon.green { color: #18b88a; background: #e3f8f1; }.saved-pill { margin-left: auto; padding: 5px 7px; border-radius: 5px; color: #139b75; background: #e3f8f1; font-size: 7px; white-space: nowrap; }.settings-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 13px 16px; }.settings-form-grid label { display: block; margin-bottom: 6px; font-size: 9px; }.settings-form-grid .form-control, .settings-form-grid .form-select { min-height: 32px; font-size: 10px; }.form-hint { display: block; margin-top: 4px; color: #a0aaba; font-size: 8px; }.code-fields { grid-template-columns: 1.2fr .7fr 1fr; }.format-preview { display: flex; justify-content: space-between; align-items: center; margin-top: 15px; padding: 10px; border-radius: 7px; color: #fff; background: #19273e; }.format-preview small { display: block; color: #8e9bb0; font-size: 7px; letter-spacing: .08em; }.format-preview strong { display: block; margin-top: 5px; font: 700 12px monospace; letter-spacing: .08em; }.format-preview .btn { font-size: 8px; }.logo-panel .settings-panel-title { justify-content: space-between; }.logo-dropzone { display: grid; justify-items: center; gap: 6px; padding: 14px 8px; border: 1px dashed #d8e1ed; border-radius: 7px; text-align: center; }.logo-preview { display: grid; place-items: center; width: 42px; height: 42px; border-radius: 7px; color: #fff; background: #19273e; font-size: 22px; }.logo-dropzone strong { font-size: 9px; }.logo-dropzone small { color: #97a3b3; font-size: 8px; }.logo-dropzone .btn { margin-top: 4px; font-size: 8px; }.storage-row { display: flex; justify-content: space-between; margin-top: 13px; color: #9aa6b7; font-size: 8px; }.storage-row strong { color: #55647a; }.security-copy { color: #8290a4; font-size: 9px; line-height: 1.6; }.encryption-status { display: flex; align-items: center; justify-content: space-between; margin-top: 13px; padding-top: 10px; border-top: 1px solid #edf1f6; }.encryption-status span { color: #8d9aac; font-size: 8px; }.encryption-status strong { color: #18a980; font-size: 9px; }.encryption-status strong small { color: #9aa6b7; font-weight: 400; }.encryption-status > i { color: #18b88a; }.toggle-row { display: flex; justify-content: space-between; align-items: center; cursor: pointer; }.toggle-row strong, .toggle-row small { display: block; }.toggle-row strong { font-size: 10px; }.toggle-row small { margin-top: 4px; color: #97a3b3; font-size: 8px; }.toggle-row input { width: 30px; height: 16px; accent-color: #2f6fed; }
@media (max-width: 800px) { .settings-heading { align-items: start; flex-direction: column; }.settings-actions { width: 100%; }.settings-actions .btn { flex: 1; }.settings-grid { grid-template-columns: 1fr; }.settings-tabs { overflow-x: auto; gap: 7px; }.settings-tabs a { white-space: nowrap; }.code-fields { grid-template-columns: 1fr 1fr; } }
@media (max-width: 520px) { .settings-form-grid, .code-fields { grid-template-columns: 1fr; }.format-preview { align-items: start; flex-direction: column; gap: 9px; }.format-preview .btn { align-self: stretch; }.saved-pill { display: none; } }
</style>
<style>
.logo-preview-image { width: 42px; height: 42px; object-fit: contain; border-radius: 7px; }
</style>
<script>
(() => {
    const prefix = document.getElementById('asset_prefix');
    const separator = document.getElementById('asset_separator');
    const digits = document.getElementById('asset_digits');
    const nextNumber = document.getElementById('asset_next_number');
    const preview = document.getElementById('asset-code-preview');
    const copyButton = document.getElementById('copy-asset-code');

    const updatePreview = () => {
        const length = Math.max(2, Number(digits.value) || 2);
        const number = String(Math.max(1, Number(nextNumber.value) || 1)).padStart(length, '0');
        preview.textContent = `${prefix.value || 'AST-GIN'}${separator.value || '-'}${number}`;
    };

    [prefix, separator, digits, nextNumber].forEach((field) => field.addEventListener('input', updatePreview));
    copyButton.addEventListener('click', async () => {
        await navigator.clipboard.writeText(preview.textContent);
        copyButton.innerHTML = '<i class="bi bi-check2"></i> Tersalin';
        setTimeout(() => { copyButton.innerHTML = '<i class="bi bi-copy"></i> Salin Contoh Kode'; }, 1400);
    });
})();
</script>
@endpush