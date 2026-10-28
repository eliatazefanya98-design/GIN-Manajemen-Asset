@extends('layouts.app')

@section('title', 'AI Assistant')

@section('content')
<div class="container-fluid ai-page">
    <div class="dashboard-header d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center gap-3">
        <div>
            <h2>AI Assistant</h2>
            <p class="text-muted mb-0">Tanyakan data inventaris aset, alur mutasi & approval, peminjaman, pemeliharaan, serta panduan fitur sistem SIM Aset.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge-status badge-status-approved d-inline-flex align-items-center gap-2 py-2 px-3">
                <span style="display:inline-block; width:8px; height:8px; border-radius:50%; background:#10b981;"></span>
                <span>AI Aktif · Assistant Real-Time</span>
            </span>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-3" role="alert">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="row justify-content-center">
        <div class="col-12">
            <section class="ai-chat-panel">
                <div class="ai-chat-topline">
                    <div class="ai-avatar"><i class="bi bi-stars"></i></div>
                    <div>
                        <strong>GIN Assistant</strong>
                        <small>Didukung Knowledge Sistem SIM Aset & Data Inventaris Real-Time</small>
                    </div>
                    <div class="ms-auto d-flex align-items-center gap-2">
                        @if(isset($messages) && $messages->count() > 0)
                            <span class="badge bg-light text-secondary border px-2 py-1 d-none d-sm-inline-block" style="font-size: 11px;">
                                <i class="bi bi-chat-dots me-1"></i> {{ $messages->count() }} pesan
                            </span>
                            <form action="{{ route('ai.clear') }}" method="POST" class="d-inline" onsubmit="return confirm('Mulai chat baru dan bersihkan riwayat obrolan ini?');">
                                @csrf
                                <button type="submit" class="btn btn-light btn-sm text-danger border" title="Hapus riwayat percakapan">
                                    <i class="bi bi-trash3 me-1"></i> Hapus Riwayat
                                </button>
                            </form>
                        @endif
                        <form action="{{ route('ai.clear') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm" title="Mulai sesi obrolan baru">
                                <i class="bi bi-plus-lg me-1"></i> Chat baru
                            </button>
                        </form>
                    </div>
                </div>

                <div class="ai-conversation" id="aiConversation">
                    {{-- Pesan sambutan awal --}}
                    <div class="ai-message ai-message-bot">
                        <div class="ai-avatar ai-avatar-small"><i class="bi bi-stars"></i></div>
                        <div>
                            <div class="ai-bubble">
                                <p class="mb-2">Halo, <strong>{{ auth()->user()->name ?? 'Pengguna' }}</strong>! Saya adalah asisten cerdas <strong>Sistem Informasi Manajemen Aset (SIM Aset)</strong> yang siap membantu operasional inventaris Anda.</p>
                                <p class="mb-2">Saya siap membantu Anda dengan:</p>
                                <ul class="mb-2 ps-3">
                                    <li><strong>Data inventaris aset riil</strong> dari database (status aktif, dipinjam, maintenance/perbaikan, maupun rusak).</li>
                                    <li><strong>Panduan alur kerja sistem</strong> (Master Data, Peminjaman Aset, Mutasi & Approval Pimpinan, serta Audit Trail Riwayat).</li>
                                    <li><strong>SOP pemeliharaan teknis</strong> dan informasi hak akses peran (Admin, Pimpinan, Staff, dan Peminjam).</li>
                                </ul>
                                <p class="mb-0">Riwayat percakapan Anda otomatis tersimpan. Silakan pilih topik pada <strong>Pilihan Cepat</strong> di bawah atau ajukan pertanyaan langsung melalui kolom chat!</p>
                            </div>
                            <small class="ai-time">Sistem</small>
                        </div>
                    </div>

                    {{-- Loop riwayat percakapan tersimpan --}}
                    @isset($messages)
                        @foreach($messages as $msg)
                            @if($msg->role === 'user')
                                <div class="ai-message ai-message-user">
                                    <div>
                                        <div class="ai-bubble">{{ $msg->message }}</div>
                                        <small class="ai-time">Anda · {{ $msg->created_at ? $msg->created_at->format('H:i') : 'Baru saja' }}</small>
                                    </div>
                                </div>
                            @else
                                <div class="ai-message ai-message-bot">
                                    <div class="ai-avatar ai-avatar-small"><i class="bi bi-stars"></i></div>
                                    <div>
                                        <div class="ai-bubble ai-response">
                                            {!! \Illuminate\Support\Str::markdown($msg->message) !!}
                                        </div>
                                        <small class="ai-time">GIN Assistant · {{ $msg->created_at ? $msg->created_at->format('H:i') : 'Baru saja' }}</small>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                    @endisset

                    {{-- Indikator Mengetik (Typing Indicator) saat AJAX memproses --}}
                    <div class="ai-message ai-message-bot" id="aiTypingIndicator" style="display: none;">
                        <div class="ai-avatar ai-avatar-small"><i class="bi bi-stars"></i></div>
                        <div>
                            <div class="ai-bubble ai-typing-bubble d-flex align-items-center gap-2 py-2 px-3">
                                <div class="ai-typing-dots">
                                    <span></span>
                                    <span></span>
                                    <span></span>
                                </div>
                                <span class="text-muted" style="font-size: 12px;">GIN Assistant sedang menganalisis data sistem...</span>
                            </div>
                        </div>
                    </div>
                </div>

                <form action="{{ route('ai.chat') }}" method="POST" class="ai-composer" id="aiChatForm">
                    @csrf
                    <label for="prompt" class="visually-hidden">Tulis prompt</label>
                    <textarea id="prompt" name="prompt" rows="2" placeholder="Tanyakan status aset, prosedur peminjaman, mutasi, pemeliharaan, atau panduan sistem SIM Aset..." required>{{ old('prompt') }}</textarea>
                    <div class="ai-composer-footer justify-content-end">
                        <button type="submit" class="btn btn-primary" id="btnSubmitAi"><i class="bi bi-send-fill me-1"></i> Kirim</button>
                    </div>
                </form>
            </section>

            @if($errors->any())
                <div class="alert alert-danger mt-3 mb-0">{{ $errors->first('prompt') }}</div>
            @endif

            <div class="ai-prompts">
                <div class="ai-section-label">Pilihan Cepat & Knowledge Prompts SIM Aset</div>
                <div class="row g-2">
                    <div class="col-md-4">
                        <button type="button" class="ai-prompt-card" data-prompt="Berikan ringkasan data inventaris aset di sistem saat ini: berapa total aset, berapa yang aktif, dipinjam, sedang maintenance, dan rusak?">
                            <i class="bi bi-box-seam"></i>
                            <span>Ringkasan status & inventaris aset</span>
                            <i class="bi bi-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="ai-prompt-card" data-prompt="Bagaimana alur dan prosedur peminjaman aset di sistem ini, serta apa saja yang perlu diperiksa saat pengembalian aset?">
                            <i class="bi bi-journal-check"></i>
                            <span>Alur peminjaman & pengembalian</span>
                            <i class="bi bi-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="ai-prompt-card" data-prompt="Jelaskan alur pengajuan mutasi lokasi aset oleh staf dan bagaimana mekanisme persetujuan (approval) oleh Pimpinan.">
                            <i class="bi bi-arrow-left-right"></i>
                            <span>Alur mutasi & approval pimpinan</span>
                            <i class="bi bi-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="ai-prompt-card" data-prompt="Bagaimana prosedur pencatatan pemeliharaan aset (maintenance), mulai dari keluhan kerusakan, penanganan teknisi, hingga realisasi biaya?">
                            <i class="bi bi-tools"></i>
                            <span>SOP pemeliharaan & perbaikan aset</span>
                            <i class="bi bi-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="ai-prompt-card" data-prompt="Bagaimana sistem mencatat riwayat aktivitas aset (audit trail) dan perubahan apa saja yang otomatis terekam dalam log aset?">
                            <i class="bi bi-clock-history"></i>
                            <span>Riwayat aset & audit trail sistem</span>
                            <i class="bi bi-arrow-up-right"></i>
                        </button>
                    </div>
                    <div class="col-md-4">
                        <button type="button" class="ai-prompt-card" data-prompt="Jelaskan pembagian hak akses peran (Admin, Pimpinan, Staff, Peminjam) serta cara pengelolaan master data kategori, lokasi, dan vendor.">
                            <i class="bi bi-shield-check"></i>
                            <span>Hak akses peran & master data</span>
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
    .ai-eyebrow { color:#c81e28; }
    .ai-status { padding:7px 12px; border:1px solid #d9e8e1; border-radius:20px; background:#f3fbf7; color:#27805f; font-size:11px; white-space:nowrap; }
    .ai-status span { display:inline-block; width:7px; height:7px; margin-right:6px; border-radius:50%; background:#18b88a; vertical-align:1px; }
    
    .ai-chat-panel { border:1px solid #e3e9f1; border-radius:12px; background:#fff; box-shadow:0 6px 20px rgba(48,67,96,.06); }
    .ai-chat-topline { display:flex; align-items:center; gap:12px; padding:16px 22px; border-bottom:1px solid #edf1f5; }
    .ai-chat-topline strong { display:block; color:#25344d; font-size:14px; }
    .ai-chat-topline small { display:block; color:#8995a7; font-size:11px; margin-top:2px; }
    .ai-avatar { display:flex; align-items:center; justify-content:center; width:38px; height:38px; border-radius:10px; color:#c81e28; background:#fdebed; font-size:18px; }
    .ai-avatar-small { flex:0 0 30px; width:30px; height:30px; border-radius:8px; font-size:14px; }
    
    .ai-conversation { min-height:420px; max-height:600px; overflow-y:auto; padding:25px 24px 15px; background:#fbfcfe; scroll-behavior: smooth; }
    .ai-message { display:flex; gap:12px; margin-bottom:20px; }
    .ai-message-user { justify-content:flex-end; }
    .ai-bubble { max-width:850px; padding:14px 18px; border-radius:4px 14px 14px 14px; color:#3b495d; background:#fff; border:1px solid #e7edf4; font-size:13px; line-height:1.7; white-space:normal; box-shadow:0 2px 6px rgba(0,0,0,.02); word-break: break-word; }
    .ai-message-user .ai-bubble { border:0; border-radius:14px 4px 14px 14px; color:#fff; background:#c81e28; font-size:13px; box-shadow:0 3px 10px rgba(200,30,40,.25); }
    
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
    .ai-response blockquote { border-left: 3px solid #c81e28; padding-left: 12px; margin-left: 0; color: #556987; font-style: italic; }

    /* Typing indicator */
    .ai-typing-bubble { background: #f8fafc; border: 1px dashed #cbd5e1; }
    .ai-typing-dots { display: inline-flex; align-items: center; gap: 4px; padding: 0 4px; }
    .ai-typing-dots span {
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #c81e28;
        display: inline-block;
        animation: aiDotBounce 1.4s infinite ease-in-out both;
    }
    .ai-typing-dots span:nth-child(1) { animation-delay: -0.32s; }
    .ai-typing-dots span:nth-child(2) { animation-delay: -0.16s; }
    @keyframes aiDotBounce {
        0%, 80%, 100% { transform: scale(0.2); opacity: 0.3; }
        40% { transform: scale(1); opacity: 1; }
    }

    .ai-time { display:block; margin:6px 3px 0; color:#a0aaba; font-size:10px; }
    .ai-message-user .ai-time { text-align:right; }
    .ai-composer { padding:15px 20px 16px; border-top:1px solid #e8edf3; }
    .ai-composer textarea { display:block; width:100%; min-height:56px; resize:vertical; border:0; outline:0; color:#35445b; font:inherit; font-size:13px; }
    .ai-composer textarea::placeholder { color:#a3adbc; }
    .ai-composer-footer { display:flex; align-items:center; gap:12px; color:#98a3b2; font-size:11px; margin-top:6px; }
    .ai-composer-footer .btn { padding:8px 16px; border-radius:8px; font-weight:500; font-size:12px; }
    
    .ai-prompts { padding-top:22px; }
    .ai-prompt-card { display:flex; align-items:center; gap:10px; width:100%; min-height:60px; padding:12px 14px; text-align:left; border:1px solid #e3e9f1; border-radius:10px; background:#fff; color:#526176; font-size:11.5px; transition:.15s ease; cursor:pointer; }
    .ai-prompt-card:hover { border-color:#fecaca; color:#c81e28; background:#fdf2f4; transform: translateY(-1px); box-shadow:0 4px 12px rgba(200,30,40,.08); }
    .ai-prompt-card > i:first-child { color:#c81e28; font-size:18px; flex-shrink:0; }
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
    document.addEventListener('DOMContentLoaded', function () {
        const promptTextarea = document.getElementById('prompt');
        const chatForm = document.getElementById('aiChatForm');
        const submitBtn = document.getElementById('btnSubmitAi');
        const conversation = document.getElementById('aiConversation');
        const typingIndicator = document.getElementById('aiTypingIndicator');

        // Fungsi scroll ke bawah secara otomatis
        function scrollToBottom() {
            if (conversation) {
                conversation.scrollTop = conversation.scrollHeight;
            }
        }
        scrollToBottom();

        // Escape HTML untuk mencegah injeksi teks pengguna saat append DOM
        function escapeHtml(text) {
            const map = {
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            };
            return text.replace(/[&<>"']/g, function(m) { return map[m]; });
        }

        // Format jam lokal HH:MM
        function getCurrentTime() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            return hours + ':' + minutes;
        }

        // Klik kartu prompt cepat
        document.querySelectorAll('[data-prompt]').forEach((card) => {
            card.addEventListener('click', () => {
                if (promptTextarea) {
                    promptTextarea.value = card.dataset.prompt;
                    promptTextarea.focus();
                }
            });
        });

        // Tangani pengiriman formulir dengan AJAX / Fetch agar riwayat tidak kedip
        if (chatForm && promptTextarea && submitBtn) {
            chatForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const promptText = promptTextarea.value.trim();
                if (!promptText) return;

                // 1. Tambahkan bubble chat pesan pengguna secara instan ke layar
                const userBubbleHtml = `
                    <div class="ai-message ai-message-user">
                        <div>
                            <div class="ai-bubble">${escapeHtml(promptText)}</div>
                            <small class="ai-time">Anda · ${getCurrentTime()}</small>
                        </div>
                    </div>
                `;
                if (typingIndicator) {
                    typingIndicator.insertAdjacentHTML('beforebegin', userBubbleHtml);
                    typingIndicator.style.display = 'flex';
                }
                scrollToBottom();

                // 2. Kosongkan textarea dan set tombol loading
                promptTextarea.value = '';
                submitBtn.disabled = true;
                const originalBtnHtml = submitBtn.innerHTML;
                submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Menjawab...';

                // 3. Kirim ke backend melalui Fetch API
                const csrfToken = document.querySelector('input[name="_token"]')?.value || '';

                try {
                    const response = await fetch(chatForm.action, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ prompt: promptText })
                    });

                    if (response.ok) {
                        const data = await response.json();
                        if (data && data.bot_message) {
                            const botBubbleHtml = `
                                <div class="ai-message ai-message-bot">
                                    <div class="ai-avatar ai-avatar-small"><i class="bi bi-stars"></i></div>
                                    <div>
                                        <div class="ai-bubble ai-response">
                                            ${data.bot_message.rendered}
                                        </div>
                                        <small class="ai-time">GIN Assistant · ${data.bot_message.time || getCurrentTime()}</small>
                                    </div>
                                </div>
                            `;
                            if (typingIndicator) {
                                typingIndicator.insertAdjacentHTML('beforebegin', botBubbleHtml);
                            }
                        }
                    } else {
                        // Jika respon error dari server
                        const errBubbleHtml = `
                            <div class="ai-message ai-message-bot">
                                <div class="ai-avatar ai-avatar-small text-danger"><i class="bi bi-exclamation-triangle-fill"></i></div>
                                <div>
                                    <div class="ai-bubble text-danger" style="background:#fff5f5; border-color:#fed7d7;">
                                        Maaf, terjadi kesalahan saat menghubungi layanan asisten AI. Silakan periksa koneksi atau coba sesaat lagi.
                                    </div>
                                    <small class="ai-time">Sistem · ${getCurrentTime()}</small>
                                </div>
                            </div>
                        `;
                        if (typingIndicator) {
                            typingIndicator.insertAdjacentHTML('beforebegin', errBubbleHtml);
                        }
                    }
                } catch (err) {
                    console.error('AI chat fetch error:', err);
                    const netErrHtml = `
                        <div class="ai-message ai-message-bot">
                            <div class="ai-avatar ai-avatar-small text-danger"><i class="bi bi-wifi-off"></i></div>
                            <div>
                                <div class="ai-bubble text-danger" style="background:#fff5f5; border-color:#fed7d7;">
                                    Gagal terhubung ke server. Pastikan server aplikasi dan koneksi internet Anda aktif.
                                </div>
                                <small class="ai-time">Sistem · ${getCurrentTime()}</small>
                            </div>
                        </div>
                    `;
                    if (typingIndicator) {
                        typingIndicator.insertAdjacentHTML('beforebegin', netErrHtml);
                    }
                } finally {
                    if (typingIndicator) {
                        typingIndicator.style.display = 'none';
                    }
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = originalBtnHtml;
                    scrollToBottom();
                    promptTextarea.focus();
                }
            });

            // Kirim saat tekan Enter tanpa Shift
            promptTextarea.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' && !e.shiftKey) {
                    if (e.isComposing) return;
                    e.preventDefault();
                    if (promptTextarea.value.trim().length > 0) {
                        chatForm.dispatchEvent(new Event('submit', { cancelable: true }));
                    }
                }
            });
        }
    });
</script>
@endpush
