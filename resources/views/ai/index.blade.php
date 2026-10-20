@extends('layouts.app')

@section('title', 'AI Assistant')

@section('content')
<div class="ai-page">
    <div class="ai-heading">
        <div>
            <div class="ai-eyebrow"><i class="bi bi-stars"></i> ANTIGRAVITY AI ASSISTANT</div>
            <h2>AI Assistant</h2>
            <p class="text-muted mb-0">Tanyakan data aset, analisis dataset predictive maintenance mesin, atau panduan fitur sistem SIM Aset.</p>
        </div>
        <div class="ai-status"><span></span> AI aktif · Gemini 3.8 Flash Low</div>
    </div>

    <div class="row justify-content-center">
        <div class="col-12">
            <section class="ai-chat-panel">
                <div class="ai-chat-topline">
                    <div class="ai-avatar"><i class="bi bi-stars"></i></div>
                    <div>
                        <strong>GIN Assistant</strong>
                        <small>Didukung Knowledge dataset.csv & Panduan SIM Aset</small>
                    </div>
                    <a href="{{ route('ai.index') }}" class="btn btn-light btn-sm ms-auto">
                        <i class="bi bi-plus-lg me-1"></i> Chat baru
                    </a>
                </div>

                <div class="ai-conversation">
                    <div class="ai-message ai-message-bot">
                        <div class="ai-avatar ai-avatar-small"><i class="bi bi-stars"></i></div>
                        <div>
                            <div class="ai-bubble">
                                <p class="mb-2">Halo, <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>! Saya adalah asisten cerdas sistem manajemen aset yang terhubung dengan model <strong>Gemini 3.8 Flash Low</strong>.</p>
                                <p class="mb-2">Saya siap membantu Anda dengan:</p>
                                <ul class="mb-2 ps-3">
                                    <li><strong>Analisis 10.000 data sensor mesin pabrik</strong> dari <code>dataset.csv</code> (deteksi modus kegagalan TWF, HDF, PWF, OSF, RNF).</li>
                                    <li><strong>Panduan seluruh fitur website</strong> (Master Data, Aset, Peminjaman, Mutasi & Approval Pimpinan, Maintenance, dan Riwayat).</li>
                                    <li><strong>Data aset riil & riwayat pemeliharaan</strong> yang tersimpan di sistem saat ini.</li>
                                </ul>
                                <p class="mb-0">Silakan tanyakan kondisi aset, cari Product ID mesin, atau konsultasikan alur kerja sistem!</p>
                            </div>
                            <small class="ai-time">Baru saja</small>
                        </div>
                    </div>

                    @isset($prompt)
                        <div class="ai-message ai-message-user">
                            <div>
                                <div class="ai-bubble">{{ $prompt }}</div>
                                <small class="ai-time">Anda · Baru saja</small>
                            </div>
                        </div>
                    @endisset

                    @isset($response)
                        <div class="ai-message ai-message-bot">
                            <div class="ai-avatar ai-avatar-small"><i class="bi bi-stars"></i></div>
                            <div>
                                <div class="ai-bubble ai-response">
                                    {!! \Illuminate\Support\Str::markdown($response) !!}
                                </div>
                                <small class="ai-time">GIN Assistant · Baru saja</small>
                            </div>
                        </div>
                    @endisset
                </div>

                <form action="{{ route('ai.chat') }}" method="POST" class="ai-composer">
                    @csrf
                    <label for="prompt" class="visually-hidden">Tulis prompt</label>
                    <textarea id="prompt" name="prompt" rows="2" placeholder="Tanyakan data aset, analisis mesin dari dataset.csv, atau panduan fitur sistem..." required>{{ old('prompt') }}</textarea>
                    <div class="ai-composer-footer">
                        <span><i class="bi bi-info-circle me-1"></i> Terhubung ke Antigravity API (Gemini 3.8 Flash Low)</span>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-send-fill me-1"></i> Kirim</button>
                    </div>
                </form>
            </section>

            @if($errors->any())
                <div class="alert alert-danger mt-3 mb-0">{{ $errors->first('prompt') }}</div>
            @endif

            <div class="ai-prompts">
                <div class="ai-section-label">Pilihan Cepat & Knowledge Prompts</div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <button type="button" class="ai-prompt-card" data-prompt="Jelaskan alur mutasi aset dan bagaimana peran Pimpinan dalam menyetujuinya">
                            <i class="bi bi-arrow-left-right"></i>
                            <span>Alur mutasi & approval pimpinan</span>
                            <i class="bi bi-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="ai-prompt-card" data-prompt="Cek data mesin Product ID L47230 pada dataset.csv dan jelaskan kondisi kegagalannya">
                            <i class="bi bi-cpu"></i>
                            <span>Cek mesin L47230 (Dataset)</span>
                            <i class="bi bi-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="ai-prompt-card" data-prompt="Apa saja indikator dan penyebab terjadinya Heat Dissipation Failure (HDF) pada mesin?">
                            <i class="bi bi-thermometer-high"></i>
                            <span>Penyebab kegagalan HDF</span>
                            <i class="bi bi-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="ai-prompt-card" data-prompt="Jika mesin Tipe L bekerja dengan torsi 65 Nm, RPM 1350, dan tool wear 210 menit, apakah berisiko mengalami failure?">
                            <i class="bi bi-shield-exclamation"></i>
                            <span>Diagnosa risiko sensor mesin</span>
                            <i class="bi bi-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="ai-prompt-card" data-prompt="Bagaimana SOP penanganan teknis jika ditemukan mesin atau aset operasional yang rusak di web ini?">
                            <i class="bi bi-tools"></i>
                            <span>SOP aset/mesin rusak</span>
                            <i class="bi bi-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="ai-prompt-card" data-prompt="Berikan ringkasan statistik dataset predictive maintenance dan kondisi aset saat ini di database">
                            <i class="bi bi-file-earmark-bar-graph"></i>
                            <span>Statistik dataset & database</span>
                            <i class="bi bi-arrow-up-right"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
    .ai-page { max-width: 1200px; margin: 0 auto; }
    .ai-heading { display:flex; align-items:flex-end; justify-content:space-between; gap:20px; margin-bottom:20px; }
    .ai-heading h2 { margin:4px 0 5px; font-size:24px; letter-spacing:-.03em; }
    .ai-eyebrow, .ai-section-label { color:#66758d; font-size:10px; font-weight:700; letter-spacing:.12em; text-transform:uppercase; }
    .ai-eyebrow { color:#2f6fed; }
    .ai-status { padding:7px 12px; border:1px solid #d9e8e1; border-radius:20px; background:#f3fbf7; color:#27805f; font-size:11px; white-space:nowrap; }
    .ai-status span { display:inline-block; width:7px; height:7px; margin-right:6px; border-radius:50%; background:#18b88a; vertical-align:1px; }
    
    .ai-chat-panel { border:1px solid #e3e9f1; border-radius:12px; background:#fff; box-shadow:0 6px 20px rgba(48,67,96,.06); }
    .ai-chat-topline { display:flex; align-items:center; gap:12px; padding:16px 22px; border-bottom:1px solid #edf1f5; }
    .ai-chat-topline strong { display:block; color:#25344d; font-size:14px; }
    .ai-chat-topline small { display:block; color:#8995a7; font-size:11px; margin-top:2px; }
    .ai-avatar { display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:10px; color:#2f6fed; background:#eaf2ff; font-size:18px; }
    .ai-avatar-small { flex:0 0 30px; width:30px; height:30px; border-radius:8px; font-size:14px; }
    
    .ai-conversation { min-height:420px; max-height:600px; overflow-y:auto; padding:25px 24px 15px; background:#fbfcfe; }
    .ai-message { display:flex; gap:12px; margin-bottom:20px; }
    .ai-message-user { justify-content:flex-end; }
    .ai-bubble { max-width:850px; padding:14px 18px; border-radius:4px 14px 14px 14px; color:#3b495d; background:#fff; border:1px solid #e7edf4; font-size:13px; line-height:1.7; white-space:normal; box-shadow:0 2px 6px rgba(0,0,0,.02); }
    .ai-message-user .ai-bubble { border:0; border-radius:14px 4px 14px 14px; color:#fff; background:#2f6fed; font-size:13px; box-shadow:0 3px 10px rgba(47,111,237,.25); }
    
    /* Markdown Typography Inside Chat Bubble */
    .ai-response { color: #2d3b4f; }
    .ai-response p { margin-bottom: 0.85rem; }
    .ai-response p:last-child { margin-bottom: 0; }
    .ai-response h1, .ai-response h2, .ai-response h3, .ai-response h4 { color: #1c2838; font-size: 14px; font-weight: 700; margin-top: 1.1rem; margin-bottom: 0.5rem; }
    .ai-response h1:first-child, .ai-response h2:first-child, .ai-response h3:first-child { margin-top: 0; }
    .ai-response ul, .ai-response ol { padding-left: 1.35rem; margin-bottom: 0.85rem; }
    .ai-response li { margin-bottom: 0.35rem; }
    .ai-response code { background: #f0f4f9; padding: 2px 6px; border-radius: 4px; color: #23477e; font-size: 11.5px; }
    .ai-response pre { background: #1e293b; color: #f8fafc; padding: 12px; border-radius: 8px; overflow-x: auto; margin-bottom: 0.85rem; }
    .ai-response pre code { background: transparent; color: inherit; padding: 0; }
    .ai-response table { width: 100%; border-collapse: collapse; margin-bottom: 0.85rem; font-size: 12px; }
    .ai-response table th, .ai-response table td { border: 1px solid #e1e7f0; padding: 8px 12px; text-align: left; }
    .ai-response table th { background: #f4f7fb; color: #334155; font-weight: 600; }
    .ai-response blockquote { border-left: 3px solid #2f6fed; padding-left: 12px; margin-left: 0; color: #556987; font-style: italic; }

    .ai-time { display:block; margin:6px 3px 0; color:#a0aaba; font-size:10px; }
    .ai-message-user .ai-time { text-align:right; }
    .ai-composer { padding:15px 20px 16px; border-top:1px solid #e8edf3; }
    .ai-composer textarea { display:block; width:100%; min-height:56px; resize:vertical; border:0; outline:0; color:#35445b; font:inherit; font-size:13px; }
    .ai-composer textarea::placeholder { color:#a3adbc; }
    .ai-composer-footer { display:flex; align-items:center; justify-content:space-between; gap:12px; color:#98a3b2; font-size:11px; margin-top:6px; }
    .ai-composer-footer .btn { padding:8px 16px; border-radius:8px; font-weight:500; font-size:12px; }
    
    .ai-prompts { padding-top:22px; }
    .ai-prompt-card { display:flex; align-items:center; gap:10px; width:100%; min-height:60px; padding:12px 14px; text-align:left; border:1px solid #e3e9f1; border-radius:10px; background:#fff; color:#526176; font-size:11.5px; transition:.15s ease; cursor:pointer; }
    .ai-prompt-card:hover { border-color:#a8c4f7; color:#2f6fed; background:#f8fbff; transform: translateY(-1px); box-shadow:0 4px 12px rgba(47,111,237,.08); }
    .ai-prompt-card > i:first-child { color:#2f6fed; font-size:18px; flex-shrink:0; }
    .ai-prompt-card span { flex:1; line-height: 1.35; font-weight: 500; }
    .ai-prompt-card > i:last-child { color:#a3afbf; font-size:13px; flex-shrink:0; }

    @media (max-width: 575px) { 
        .ai-heading { align-items:flex-start; flex-direction:column; gap:10px; } 
        .ai-status { align-self:flex-start; } 
        .ai-composer-footer span { max-width:60%; font-size:10px; } 
        .ai-prompt-card { min-height:50px; font-size:11px; } 
    }
</style>
@endpush

@push('scripts')
<script>
    document.querySelectorAll('[data-prompt]').forEach((card) => {
        card.addEventListener('click', () => {
            const prompt = document.getElementById('prompt');
            prompt.value = card.dataset.prompt;
            prompt.focus();
        });
    });
</script>
@endpush
