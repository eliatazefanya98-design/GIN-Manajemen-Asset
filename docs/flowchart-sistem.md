# Flowchart Sistem Manajemen Aset

## Alur Utama

```mermaid
flowchart TD
    START([Mulai]) --> HAS_ACCOUNT{Sudah memiliki akun?}
    HAS_ACCOUNT -- Belum --> REGISTER[Isi formulir pendaftaran]
    REGISTER --> REGISTER_VALID{Data valid?}
    REGISTER_VALID -- Tidak --> REGISTER
    REGISTER_VALID -- Ya --> CREATE_USER[Simpan akun dengan role peminjam]
    CREATE_USER --> AUTHENTICATE[Login dan buat sesi]
    HAS_ACCOUNT -- Sudah --> LOGIN[Masukkan email dan kata sandi]
    LOGIN --> CREDENTIALS{Kredensial valid?}
    CREDENTIALS -- Tidak --> LOGIN
    CREDENTIALS -- Ya --> AUTHENTICATE
    AUTHENTICATE --> DASHBOARD[Dashboard / Beranda Role]

    DASHBOARD --> DASH_DATA[Ambil statistik aset, maintenance,<br/>kategori, lokasi, dan data terbaru]
    DASH_DATA --> DASH_VIEW[Tampilkan ringkasan dan grafik]
    DASH_VIEW --> MENU{Pilih fitur}

    MENU --> MASTER[Data kategori, lokasi, vendor,<br/>aset, maintenance, atau riwayat]
    MASTER --> MASTER_ACTION{Aksi baca atau ubah data?}
    MASTER_ACTION -- Lihat daftar/detail --> READ_DATA[Baca data dari database]
    READ_DATA --> SHOW_DATA[Tampilkan daftar atau detail]
    MASTER_ACTION -- Tambah, ubah, atau hapus --> CHECK_ADMIN{Role admin/staff?}
    CHECK_ADMIN -- Tidak --> FORBIDDEN[Tolak akses: HTTP 403]
    CHECK_ADMIN -- Ya --> VALIDATE[Validasi data masukan]
    VALIDATE --> DATA_VALID{Data valid?}
    DATA_VALID -- Tidak --> VALIDATE
    DATA_VALID -- Ya --> SAVE_DATA[Simpan perubahan ke database]
    SAVE_DATA --> TRACK_HISTORY{Membuat aset atau maintenance?}
    TRACK_HISTORY -- Ya --> WRITE_HISTORY[Buat riwayat aktivitas aset]
    TRACK_HISTORY -- Tidak --> SHOW_DATA
    WRITE_HISTORY --> SHOW_DATA

    MENU --> SETTINGS[Lihat pengaturan]
    SETTINGS --> SETTINGS_ACTION{Ubah atau reset pengaturan?}
    SETTINGS_ACTION -- Tidak --> MENU
    SETTINGS_ACTION -- Ya --> SETTINGS_ADMIN{Role admin?}
    SETTINGS_ADMIN -- Tidak --> FORBIDDEN
    SETTINGS_ADMIN -- Ya --> SAVE_SETTINGS[Simpan atau reset pengaturan]
    SAVE_SETTINGS --> MENU

    MENU --> USERS[Kelola pengguna dan role]
    USERS --> USERS_ADMIN{Role admin?}
    USERS_ADMIN -- Tidak --> FORBIDDEN
    USERS_ADMIN -- Ya --> UPDATE_ROLE[Perbarui role pengguna]
    UPDATE_ROLE --> MENU

    MENU --> AI[Gunakan asisten AI]
    AI --> AI_FLOW[[Alur layanan AI Gemini Flash 3.8 Low via 9router]]
    AI_FLOW --> MENU

    MENU --> LOGOUT[Logout dan hapus sesi]
    LOGOUT --> END([Selesai])

    classDef decision fill:#fff4cc,stroke:#b7791f,color:#242424;
    classDef process fill:#e8f4f8,stroke:#28728a,color:#172b34;
    classDef error fill:#fde8e7,stroke:#b42318,color:#541a16;
    class HAS_ACCOUNT,CREDENTIALS,REGISTER_VALID,MASTER_ACTION,CHECK_ADMIN,DATA_VALID,TRACK_HISTORY,SETTINGS_ACTION,SETTINGS_ADMIN,USERS_ADMIN decision;
    class CREATE_USER,AUTHENTICATE,DASHBOARD,DASH_DATA,DASH_VIEW,READ_DATA,SHOW_DATA,VALIDATE,SAVE_DATA,WRITE_HISTORY,SETTINGS,SAVE_SETTINGS,USERS,UPDATE_ROLE,AI,LOGOUT process;
    class FORBIDDEN error;
```

## Alur Asisten AI (Gemini Flash 3.8 Low via Antigravity 9router)

```mermaid
flowchart LR
    USER[Pengguna login mengirim prompt] --> LARAVEL[Laravel memvalidasi prompt]
    LARAVEL --> VALID{Prompt valid?}
    VALID -- Tidak --> RETRY[Kembalikan kesalahan validasi]
    RETRY --> USER
    VALID -- Ya --> CONTEXT[Ambil konteks dari database:<br/>jumlah aset, maintenance bulan ini,<br/>ringkasan prediksi risiko]
    CONTEXT --> REQUEST[Kirim prompt dan konteks ke API 9router]
    REQUEST --> GEMINI[Gemini Flash 3.8 Low memproses permintaan]
    GEMINI --> RESPONSE{Respons berhasil?}
    RESPONSE -- Ya --> RETURN[Laravel menampilkan jawaban AI]
    RESPONSE -- Tidak --> FALLBACK[Tampilkan pesan layanan 9router<br/>belum dapat dihubungi]
    RETURN --> USER
    FALLBACK --> USER
```

## Catatan Akses

- Semua halaman aplikasi utama, termasuk dashboard, daftar/detail data, pengaturan, dan asisten AI, memerlukan login.
- Pengguna yang mendaftar mandiri otomatis mendapat role `peminjam`; pembuatan atau promosi ke akun `staff`, `pimpinan`, atau `admin` dilakukan melalui pengelolaan role oleh admin yang sudah ada.
- Akses tambah, ubah, dan hapus data master/transaksi, pengaturan, serta pengelolaan role dibatasi untuk admin/staff sesuai matriks hak akses.
- Pembuatan aset, mutasi, peminjaman, dan maintenance menghasilkan catatan pada riwayat aset.
- Alur AI menggunakan data ringkasan real-time dari database aplikasi dan terhubung langsung via HTTP ke Antigravity 9router API menggunakan model `gemini-3.8-flash-low`.