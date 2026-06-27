<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Orens Pro - Spesifikasi Skema Database & ERD</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm 1.5cm 1.5cm 2cm;
        }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0F172A; /* slate-900 */
            line-height: 1.5;
            background-color: #ffffff;
            font-size: 9.5pt;
        }

        .page-break {
            page-break-before: always;
        }

        /* --- Cover Page Design --- */
        .cover-page {
            padding: 3rem 1.5rem;
            height: 100%;
            background-color: #0F172A;
            color: #ffffff;
            position: relative;
        }

        .cover-header {
            margin-bottom: 5rem;
        }

        .cover-logo {
            font-size: 24pt;
            font-weight: bold;
            color: #FF7B00; /* Primary Orens */
            letter-spacing: 2px;
        }

        .cover-body {
            margin-top: 8rem;
            margin-bottom: 8rem;
        }

        .cover-tagline {
            font-size: 11pt;
            text-transform: uppercase;
            letter-spacing: 3px;
            color: #FF7B00;
            font-weight: bold;
            margin-bottom: 1rem;
        }

        .cover-title {
            font-size: 30pt;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .cover-subtitle {
            font-size: 12pt;
            color: #94A3B8; /* slate-400 */
            border-left: 4px solid #FF7B00;
            padding-left: 1rem;
            max-width: 90%;
        }

        .cover-footer {
            position: absolute;
            bottom: 2rem;
            left: 1.5rem;
            right: 1.5rem;
            border-top: 1px solid #334155;
            padding-top: 1.5rem;
            font-size: 9pt;
            color: #94A3B8;
        }

        .cover-footer table {
            width: 100%;
            border-collapse: collapse;
        }

        .cover-footer td {
            border: none;
            padding: 0;
        }

        /* --- Content Layout --- */
        h1 {
            font-size: 18pt;
            color: #0F172A;
            border-bottom: 2px solid #FF7B00;
            padding-bottom: 5px;
            margin-top: 2rem;
            margin-bottom: 1.2rem;
        }

        h2 {
            font-size: 13pt;
            color: #1E293B;
            margin-top: 1.6rem;
            margin-bottom: 0.8rem;
            border-left: 4px solid #FF7B00;
            padding-left: 8px;
        }

        h3 {
            font-size: 10.5pt;
            color: #334155;
            margin-top: 1.1rem;
            margin-bottom: 0.4rem;
        }

        p {
            margin-bottom: 0.8rem;
            text-align: justify;
            color: #334155;
        }

        ol, ul {
            margin-bottom: 0.8rem;
            padding-left: 1.5rem;
        }

        li {
            margin-bottom: 0.3rem;
            color: #334155;
        }

        /* --- ERD Relationship Diagram Tables --- */
        .diagram-container {
            width: 100%;
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 1.5rem 1rem;
            margin: 1.5rem 0;
            box-sizing: border-box;
        }

        .diagram-title {
            font-weight: bold;
            font-size: 9pt;
            color: #475569;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 1.2rem;
            letter-spacing: 1px;
        }

        .erd-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }

        .erd-table td {
            border: none;
            padding: 6px;
            text-align: center;
            vertical-align: middle;
        }

        .erd-node {
            background-color: #ffffff;
            border: 2px solid #E2E8F0;
            border-radius: 6px;
            padding: 8px 12px;
            display: inline-block;
            font-size: 8.5pt;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            max-width: 250px;
        }

        .erd-node.entity {
            background-color: #0F172A;
            color: #ffffff;
            border-color: #0F172A;
            font-weight: bold;
        }

        .erd-node.entity-detail {
            border-color: #CBD5E1;
            background-color: #F8FAFC;
            text-align: left;
            font-family: monospace;
            font-size: 7.5pt;
        }

        .erd-node.highlight {
            border-color: #FF7B00;
            background-color: #FFF7ED; /* orange-50 */
            font-weight: 600;
        }

        .erd-arrow {
            font-size: 11pt;
            color: #FF7B00;
            font-weight: bold;
        }

        .erd-arrow-text {
            font-size: 7pt;
            color: #64748B;
            display: block;
        }

        /* --- Tables --- */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1rem 0;
            font-size: 8.5pt;
        }

        table.data-table th, table.data-table td {
            padding: 6px 8px;
            border: 1px solid #E2E8F0;
            text-align: left;
            vertical-align: top;
        }

        table.data-table th {
            background-color: #0F172A;
            color: #ffffff;
            font-weight: bold;
        }

        table.data-table tr:nth-child(even) {
            background-color: #F8FAFC;
        }

        /* --- Badges --- */
        .type-badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 7pt;
            font-weight: bold;
            font-family: monospace;
        }
        .badge-pk { background-color: #FEE2E2; color: #991B1B; }
        .badge-fk { background-color: #DBEAFE; color: #1D4ED8; }
        .badge-idx { background-color: #FEF3C7; color: #D97706; }
        .badge-uk { background-color: #D1FAE5; color: #047857; }

        .callout {
            border-left: 4px solid #FF7B00;
            background-color: #F8FAFC;
            padding: 8px 12px;
            margin: 1rem 0;
            border-radius: 0 6px 6px 0;
            font-size: 9pt;
        }

        .footer {
            margin-top: 2rem;
            border-top: 1px solid #E2E8F0;
            padding-top: 10px;
            font-size: 8pt;
            color: #94A3B8;
            text-align: right;
        }
    </style>
</head>
<body>

    <!-- Cover Page -->
    <div class="cover-page">
        <div class="cover-header">
            <span class="cover-logo">ORENS PRO</span>
        </div>

        <div class="cover-body">
            <p class="cover-tagline">Dokumentasi Skema Database & ERD</p>
            <h1 class="cover-title">Kamus Data & Spesifikasi ERD Lengkap</h1>
            <p class="cover-subtitle">
                Detail Struktur Tabel, Tipe Data, Relasi Entitas (1:N), Indeks Kinerja, Kunci Primer & Asing, serta Konfigurasi Infrastruktur Laravel DomPDF.
            </p>
        </div>

        <div class="cover-footer">
            <table>
                <tr>
                    <td>
                        <strong>SMK & SMA Prestasi Prima</strong><br>
                        Jakarta, Indonesia
                    </td>
                    <td style="text-align: right; vertical-align: bottom;">
                        Versi: 2.1 (Ekskul Context)<br>
                        Diperbarui: 23 Juni 2026
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- Section 1 -->
    <h1>1. Deskripsi Relasi Entitas & Alur Relasi (ERD)</h1>
    <p>
        Database <strong>Orens Pro</strong> dirancang dengan struktur berjenjang dari <strong>Ekstrakurikuler (Organisations) &rarr; Divisi (Divisions) &rarr; Anggota (Users)</strong>. Seluruh aktivitas absensi dinamis berbasis Geofencing dan kode QR tervalidasi secara relasional untuk menjamin integritas data kehadiran.
    </p>

    <h2>Visualisasi Hubungan Entitas (ERD Box Model)</h2>
    <div class="diagram-container">
        <div class="diagram-title">Hierarki Relasi Bisnis Utama</div>
        <table class="erd-table" style="max-width: 600px;">
            <tr>
                <td><div class="erd-node entity">organisations</div></td>
                <td class="erd-arrow">&harr; <span class="erd-arrow-text">1 : N (memiliki)</span></td>
                <td><div class="erd-node entity">divisions</div></td>
            </tr>
            <tr>
                <td class="erd-arrow">&darr; <span class="erd-arrow-text">1 : N</span></td>
                <td></td>
                <td class="erd-arrow">&darr; <span class="erd-arrow-text">1 : N</span></td>
            </tr>
            <tr>
                <td colspan="3"><div class="erd-node entity" style="width:100px;">users</div></td>
            </tr>
            <tr>
                <td class="erd-arrow">&darr; <span class="erd-arrow-text">1 : N (mencatat)</span></td>
                <td class="erd-arrow">&darr; <span class="erd-arrow-text">1 : N (hadir)</span></td>
                <td class="erd-arrow">&darr; <span class="erd-arrow-text">1 : N (membuat)</span></td>
            </tr>
            <tr>
                <td><div class="erd-node entity">attendance_logs</div></td>
                <td><div class="erd-node entity">attendances</div></td>
                <td><div class="erd-node entity">attendance_sessions</div></td>
            </tr>
            <tr>
                <td></td>
                <td class="erd-arrow">&uarr; <span class="erd-arrow-text">N : 1</span></td>
                <td></td>
            </tr>
            <tr>
                <td colspan="3"><div class="erd-node entity" style="width:180px;">audit_logs</div></td>
            </tr>
        </table>
    </div>

    <h2>Daftar Relasi Antar Tabel (Relational Mapping)</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Tabel Asal (Parent)</th>
                <th style="width: 25%;">Tabel Tujuan (Child)</th>
                <th style="width: 20%;">Kunci Asing (FK)</th>
                <th style="width: 30%;">Aturan Aksi Penghapusan (On Delete)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>organisations</strong></td>
                <td>divisions</td>
                <td><code>organisation_id</code></td>
                <td><span style="color:#991B1B; font-weight:bold;">CASCADE</span> (Divisi otomatis terhapus)</td>
            </tr>
            <tr>
                <td><strong>organisations</strong></td>
                <td>users</td>
                <td><code>organisation_id</code></td>
                <td><span style="color:#1D4ED8; font-weight:bold;">SET NULL</span> (Akun tetap ada tanpa organisasi)</td>
            </tr>
            <tr>
                <td><strong>divisions</strong></td>
                <td>users</td>
                <td><code>division_id</code></td>
                <td><span style="color:#1D4ED8; font-weight:bold;">SET NULL</span> (Akun tetap ada tanpa divisi)</td>
            </tr>
            <tr>
                <td><strong>users</strong></td>
                <td>attendance_sessions</td>
                <td><code>created_by</code></td>
                <td><span style="color:#991B1B; font-weight:bold;">CASCADE</span> (Sesi dihapus jika pembuat dihapus)</td>
            </tr>
            <tr>
                <td><strong>attendance_sessions</strong></td>
                <td>attendances</td>
                <td><code>session_id</code></td>
                <td><span style="color:#991B1B; font-weight:bold;">CASCADE</span> (Kehadiran dihapus jika sesi dihapus)</td>
            </tr>
            <tr>
                <td><strong>users</strong></td>
                <td>attendances</td>
                <td><code>user_id</code></td>
                <td><span style="color:#991B1B; font-weight:bold;">CASCADE</span> (Kehadiran dihapus jika siswa dihapus)</td>
            </tr>
            <tr>
                <td><strong>users</strong></td>
                <td>attendance_logs</td>
                <td><code>user_id</code></td>
                <td><span style="color:#1D4ED8; font-weight:bold;">SET NULL</span> (Log pindai QR tetap tersimpan)</td>
            </tr>
            <tr>
                <td><strong>users</strong></td>
                <td>audit_logs</td>
                <td><code>user_id</code></td>
                <td><span style="color:#1D4ED8; font-weight:bold;">SET NULL</span> (Audit log admin tetap tersimpan)</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Section 2 -->
    <h1>2. Kamus Data Tabel Inti Bisnis (Core Business)</h1>

    <h2>2.1. Tabel `organisations` (Ekstrakurikuler)</h2>
    <p>Menyimpan entitas organisasi ekskul di lingkungan SMK/SMA Prestasi Prima.</p>
    <ul>
        <li><strong>Model Laravel:</strong> <code>App\Models\Organisation</code></li>
    </ul>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Kolom</th>
                <th style="width: 22%;">Tipe Data</th>
                <th style="width: 10%;">Null</th>
                <th style="width: 13%;">Kunci</th>
                <th style="width: 35%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>bigint unsigned</td>
                <td>NO</td>
                <td><span class="type-badge badge-pk">PK</span></td>
                <td>ID Unik Auto-Increment</td>
            </tr>
            <tr>
                <td><strong>name</strong></td>
                <td>varchar(150)</td>
                <td>NO</td>
                <td>-</td>
                <td>Nama ekstrakurikuler</td>
            </tr>
            <tr>
                <td><strong>address</strong></td>
                <td>text</td>
                <td>YES</td>
                <td>-</td>
                <td>Alamat basecamp/sekretariat</td>
            </tr>
            <tr>
                <td><strong>last_grade_reset_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Tanggal terakhir reset keaktifan siswa</td>
            </tr>
            <tr>
                <td><strong>description</strong></td>
                <td>text</td>
                <td>YES</td>
                <td>-</td>
                <td>Deskripsi kegiatan/visi misi</td>
            </tr>
            <tr>
                <td><strong>has_division</strong></td>
                <td>tinyint(1)</td>
                <td>NO</td>
                <td>-</td>
                <td>Apakah memiliki divisi (Default: 0 / false)</td>
            </tr>
            <tr>
                <td><strong>created_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu record dibuat</td>
            </tr>
            <tr>
                <td><strong>updated_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu record diperbarui</td>
            </tr>
        </tbody>
    </table>

    <h2>2.2. Tabel `divisions` (Divisi Ekskul)</h2>
    <p>Menyimpan data sub-divisi/bidang spesialisasi di dalam organisasi ekskul.</p>
    <ul>
        <li><strong>Model Laravel:</strong> <code>App\Models\Division</code></li>
    </ul>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Kolom</th>
                <th style="width: 22%;">Tipe Data</th>
                <th style="width: 10%;">Null</th>
                <th style="width: 13%;">Kunci</th>
                <th style="width: 35%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>bigint unsigned</td>
                <td>NO</td>
                <td><span class="type-badge badge-pk">PK</span></td>
                <td>ID Unik Auto-Increment</td>
            </tr>
            <tr>
                <td><strong>organisation_id</strong></td>
                <td>bigint unsigned</td>
                <td>NO</td>
                <td><span class="type-badge badge-fk">FK</span></td>
                <td>Relasi ke <code>organisations.id</code> (Cascade)</td>
            </tr>
            <tr>
                <td><strong>name</strong></td>
                <td>varchar(150)</td>
                <td>NO</td>
                <td>-</td>
                <td>Nama divisi/bidang</td>
            </tr>
            <tr>
                <td><strong>description</strong></td>
                <td>text</td>
                <td>YES</td>
                <td>-</td>
                <td>Uraian fokus materi divisi</td>
            </tr>
            <tr>
                <td><strong>created_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu record dibuat</td>
            </tr>
            <tr>
                <td><strong>updated_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu record diperbarui</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <h2>2.3. Tabel `users` (Pengguna Sistem)</h2>
    <p>Menyimpan data autentikasi dan informasi profil seluruh peran dalam aplikasi.</p>
    <ul>
        <li><strong>Model Laravel:</strong> <code>App\Models\User</code></li>
        <li><strong>Indeks Khusus:</strong> Composite Index <code>users_org_div_role_idx</code> pada kolom <code>(organisation_id, division_id, role)</code>.</li>
    </ul>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Kolom</th>
                <th style="width: 22%;">Tipe Data</th>
                <th style="width: 10%;">Null</th>
                <th style="width: 13%;">Kunci</th>
                <th style="width: 35%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>bigint unsigned</td>
                <td>NO</td>
                <td><span class="type-badge badge-pk">PK</span></td>
                <td>ID Unik Auto-Increment</td>
            </tr>
            <tr>
                <td><strong>organisation_id</strong></td>
                <td>bigint unsigned</td>
                <td>YES</td>
                <td><span class="type-badge badge-fk">FK</span>, <span class="type-badge badge-idx">IDX</span></td>
                <td>Ekskul siswa (NullOnDelete)</td>
            </tr>
            <tr>
                <td><strong>division_id</strong></td>
                <td>bigint unsigned</td>
                <td>YES</td>
                <td><span class="type-badge badge-fk">FK</span>, <span class="type-badge badge-idx">IDX</span></td>
                <td>Divisi siswa (NullOnDelete)</td>
            </tr>
            <tr>
                <td><strong>name</strong></td>
                <td>varchar(255)</td>
                <td>NO</td>
                <td>-</td>
                <td>Nama lengkap pengguna</td>
            </tr>
            <tr>
                <td><strong>email</strong></td>
                <td>varchar(255)</td>
                <td>NO</td>
                <td><span class="type-badge badge-uk">UK</span></td>
                <td>Alamat email unik siswa/guru</td>
            </tr>
            <tr>
                <td><strong>email_verified_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu email diverifikasi</td>
            </tr>
            <tr>
                <td><strong>password</strong></td>
                <td>varchar(255)</td>
                <td>NO</td>
                <td>-</td>
                <td>Hash sandi (Bcrypt)</td>
            </tr>
            <tr>
                <td><strong>phone</strong></td>
                <td>varchar(30)</td>
                <td>YES</td>
                <td>-</td>
                <td>No HP WhatsApp aktif</td>
            </tr>
            <tr>
                <td><strong>role</strong></td>
                <td>varchar(30)</td>
                <td>NO</td>
                <td><span class="type-badge badge-idx">IDX</span></td>
                <td>Hak akses: superadmin, pembina, pengurus, member</td>
            </tr>
            <tr>
                <td><strong>is_active</strong></td>
                <td>tinyint(1)</td>
                <td>NO</td>
                <td>-</td>
                <td>Status aktif akun (1: aktif, 0: diblokir)</td>
            </tr>
            <tr>
                <td><strong>remember_token</strong></td>
                <td>varchar(100)</td>
                <td>YES</td>
                <td>-</td>
                <td>Token sesi Remember Me</td>
            </tr>
            <tr>
                <td><strong>created_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu record dibuat</td>
            </tr>
            <tr>
                <td><strong>updated_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu record diperbarui</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <h2>2.4. Tabel `attendance_sessions` (Sesi Presensi)</h2>
    <p>Menyimpan data sesi pertemuan absensi yang dibuka oleh Pengurus atau Pembina ekskul.</p>
    <ul>
        <li><strong>Model Laravel:</strong> <code>App\Models\AttendanceSession</code></li>
        <li><strong>Indeks Khusus:</strong> Composite Index <code>att_sess_org_div_date_idx</code> pada <code>(organisation_id, division_id, session_date)</code> dan Single Index <code>att_sess_active_idx</code> pada <code>is_active</code>.</li>
    </ul>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Kolom</th>
                <th style="width: 22%;">Tipe Data</th>
                <th style="width: 10%;">Null</th>
                <th style="width: 13%;">Kunci</th>
                <th style="width: 35%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>bigint unsigned</td>
                <td>NO</td>
                <td><span class="type-badge badge-pk">PK</span></td>
                <td>ID Unik Auto-Increment</td>
            </tr>
            <tr>
                <td><strong>organisation_id</strong></td>
                <td>bigint unsigned</td>
                <td>YES</td>
                <td><span class="type-badge badge-fk">FK</span>, <span class="type-badge badge-idx">IDX</span></td>
                <td>Ekskul penyelenggara (NullOnDelete)</td>
            </tr>
            <tr>
                <td><strong>division_id</strong></td>
                <td>bigint unsigned</td>
                <td>YES</td>
                <td><span class="type-badge badge-fk">FK</span>, <span class="type-badge badge-idx">IDX</span></td>
                <td>Divisi penyelenggara (NullOnDelete)</td>
            </tr>
            <tr>
                <td><strong>title</strong></td>
                <td>varchar(200)</td>
                <td>YES</td>
                <td>-</td>
                <td>Judul/kegiatan sesi pertemuan</td>
            </tr>
            <tr>
                <td><strong>qr_token</strong></td>
                <td>varchar(255)</td>
                <td>NO</td>
                <td><span class="type-badge badge-uk">UK</span></td>
                <td>Salt kunci kriptografi HMAC QR dinamis</td>
            </tr>
            <tr>
                <td><strong>session_date</strong></td>
                <td>date</td>
                <td>NO</td>
                <td><span class="type-badge badge-idx">IDX</span></td>
                <td>Tanggal pelaksanaan sesi</td>
            </tr>
            <tr>
                <td><strong>start_time</strong></td>
                <td>time</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu mulai dibuka check-in</td>
            </tr>
            <tr>
                <td><strong>end_time</strong></td>
                <td>time</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu ditutup/sesi berakhir</td>
            </tr>
            <tr>
                <td><strong>latitude</strong></td>
                <td>decimal(10,7)</td>
                <td>YES</td>
                <td>-</td>
                <td>Koordinat Latitude pusat Geofence</td>
            </tr>
            <tr>
                <td><strong>longitude</strong></td>
                <td>decimal(10,7)</td>
                <td>YES</td>
                <td>-</td>
                <td>Koordinat Longitude pusat Geofence</td>
            </tr>
            <tr>
                <td><strong>radius</strong></td>
                <td>int</td>
                <td>YES</td>
                <td>-</td>
                <td>Jarak aman toleransi presensi (meter)</td>
            </tr>
            <tr>
                <td><strong>is_active</strong></td>
                <td>tinyint(1)</td>
                <td>NO</td>
                <td><span class="type-badge badge-idx">IDX</span></td>
                <td>Status aktif scan QR (Default: 0 / false)</td>
            </tr>
            <tr>
                <td><strong>created_by</strong></td>
                <td>bigint unsigned</td>
                <td>NO</td>
                <td><span class="type-badge badge-fk">FK</span></td>
                <td>ID pembuat sesi dari <code>users.id</code> (Cascade)</td>
            </tr>
            <tr>
                <td><strong>created_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu record dibuat</td>
            </tr>
            <tr>
                <td><strong>updated_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu record diperbarui</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <h2>2.5. Tabel `attendances` (Data Kehadiran)</h2>
    <p>Menyimpan data transaksi kehadiran anggota pada sesi absensi tertentu.</p>
    <ul>
        <li><strong>Model Laravel:</strong> <code>App\Models\Attendance</code></li>
        <li><strong>Indeks Khusus:</strong> Composite Index <code>att_session_user_status_idx</code> pada kolom <code>(session_id, user_id, status)</code>.</li>
    </ul>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Kolom</th>
                <th style="width: 22%;">Tipe Data</th>
                <th style="width: 10%;">Null</th>
                <th style="width: 13%;">Kunci</th>
                <th style="width: 35%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>bigint unsigned</td>
                <td>NO</td>
                <td><span class="type-badge badge-pk">PK</span></td>
                <td>ID Unik Auto-Increment</td>
            </tr>
            <tr>
                <td><strong>user_id</strong></td>
                <td>bigint unsigned</td>
                <td>NO</td>
                <td><span class="type-badge badge-fk">FK</span>, <span class="type-badge badge-idx">IDX</span></td>
                <td>Siswa yang hadir (Cascade)</td>
            </tr>
            <tr>
                <td><strong>session_id</strong></td>
                <td>bigint unsigned</td>
                <td>NO</td>
                <td><span class="type-badge badge-fk">FK</span>, <span class="type-badge badge-idx">IDX</span></td>
                <td>Sesi terkait (Cascade)</td>
            </tr>
            <tr>
                <td><strong>checkin_time</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu presensi berhasil terkirim</td>
            </tr>
            <tr>
                <td><strong>latitude</strong></td>
                <td>decimal(10,7)</td>
                <td>YES</td>
                <td>-</td>
                <td>Latitude koordinat HP siswa saat check-in</td>
            </tr>
            <tr>
                <td><strong>longitude</strong></td>
                <td>decimal(10,7)</td>
                <td>YES</td>
                <td>-</td>
                <td>Longitude koordinat HP siswa saat check-in</td>
            </tr>
            <tr>
                <td><strong>distance</strong></td>
                <td>int</td>
                <td>YES</td>
                <td>-</td>
                <td>Jarak riil ke pusat Geofence (dalam meter)</td>
            </tr>
            <tr>
                <td><strong>status</strong></td>
                <td>varchar(30)</td>
                <td>YES</td>
                <td><span class="type-badge badge-idx">IDX</span></td>
                <td>Status: hadir, telat, sakit, izin, alpha</td>
            </tr>
            <tr>
                <td><strong>created_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu record dibuat</td>
            </tr>
            <tr>
                <td><strong>updated_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu record diperbarui</td>
            </tr>
        </tbody>
    </table>

    <h2>2.6. Tabel `attendance_logs` (Log Percobaan Absensi)</h2>
    <p>Menyimpan data log seluruh percobaan pindai QR Code oleh siswa untuk forensik audit.</p>
    <ul>
        <li><strong>Model Laravel:</strong> <code>App\Models\AttendanceLog</code></li>
        <li><strong>Indeks Khusus:</strong> Composite Index <code>att_logs_user_qr_idx</code> pada kolom <code>(user_id, qr_token)</code>.</li>
    </ul>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Kolom</th>
                <th style="width: 22%;">Tipe Data</th>
                <th style="width: 10%;">Null</th>
                <th style="width: 13%;">Kunci</th>
                <th style="width: 35%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>bigint unsigned</td>
                <td>NO</td>
                <td><span class="type-badge badge-pk">PK</span></td>
                <td>ID Unik Auto-Increment</td>
            </tr>
            <tr>
                <td><strong>user_id</strong></td>
                <td>bigint unsigned</td>
                <td>YES</td>
                <td><span class="type-badge badge-fk">FK</span>, <span class="type-badge badge-idx">IDX</span></td>
                <td>Siswa yang memindai (NullOnDelete)</td>
            </tr>
            <tr>
                <td><strong>qr_token</strong></td>
                <td>varchar(255)</td>
                <td>NO</td>
                <td><span class="type-badge badge-idx">IDX</span></td>
                <td>Token QR Code yang dikirim</td>
            </tr>
            <tr>
                <td><strong>latitude</strong></td>
                <td>decimal(10,7)</td>
                <td>YES</td>
                <td>-</td>
                <td>Latitude posisi HP siswa saat mencoba</td>
            </tr>
            <tr>
                <td><strong>longitude</strong></td>
                <td>decimal(10,7)</td>
                <td>YES</td>
                <td>-</td>
                <td>Longitude posisi HP siswa saat mencoba</td>
            </tr>
            <tr>
                <td><strong>result</strong></td>
                <td>text</td>
                <td>YES</td>
                <td>-</td>
                <td>Hasil validasi: sukses, or error message</td>
            </tr>
            <tr>
                <td><strong>created_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu kejadian log</td>
            </tr>
            <tr>
                <td><strong>updated_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu record diperbarui</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <h2>2.7. Tabel `audit_logs` (Log Audit Aktivitas Admin)</h2>
    <p>Menyimpan jejak audit (*audit trail*) setiap perubahan data penting di dashboard.</p>
    <ul>
        <li><strong>Model Laravel:</strong> <code>App\Models\AuditLog</code></li>
    </ul>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Kolom</th>
                <th style="width: 22%;">Tipe Data</th>
                <th style="width: 10%;">Null</th>
                <th style="width: 13%;">Kunci</th>
                <th style="width: 35%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>bigint unsigned</td>
                <td>NO</td>
                <td><span class="type-badge badge-pk">PK</span></td>
                <td>ID Unik Auto-Increment</td>
            </tr>
            <tr>
                <td><strong>user_id</strong></td>
                <td>bigint unsigned</td>
                <td>YES</td>
                <td><span class="type-badge badge-fk">FK</span></td>
                <td>ID Admin/Aktor pelaku aksi (NullOnDelete)</td>
            </tr>
            <tr>
                <td><strong>event</strong></td>
                <td>varchar(255)</td>
                <td>NO</td>
                <td>-</td>
                <td>Jenis aksi: created, updated, deleted, login, logout</td>
            </tr>
            <tr>
                <td><strong>auditable_type</strong></td>
                <td>varchar(255)</td>
                <td>YES</td>
                <td>-</td>
                <td>Namespace model class yang dimanipulasi</td>
            </tr>
            <tr>
                <td><strong>auditable_id</strong></td>
                <td>bigint unsigned</td>
                <td>YES</td>
                <td>-</td>
                <td>ID baris data model yang diubah</td>
            </tr>
            <tr>
                <td><strong>old_values</strong></td>
                <td>json</td>
                <td>YES</td>
                <td>-</td>
                <td>Data sebelum diubah (format JSON)</td>
            </tr>
            <tr>
                <td><strong>new_values</strong></td>
                <td>json</td>
                <td>YES</td>
                <td>-</td>
                <td>Data sesudah diubah (format JSON)</td>
            </tr>
            <tr>
                <td><strong>ip_address</strong></td>
                <td>varchar(255)</td>
                <td>YES</td>
                <td>-</td>
                <td>Alamat IP asal pengakses</td>
            </tr>
            <tr>
                <td><strong>user_agent</strong></td>
                <td>text</td>
                <td>YES</td>
                <td>-</td>
                <td>Informasi browser & OS admin</td>
            </tr>
            <tr>
                <td><strong>created_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu log terekam</td>
            </tr>
            <tr>
                <td><strong>updated_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>-</td>
                <td>Waktu record diperbarui</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <!-- Section 3 -->
    <h1>3. Kamus Data Tabel Framework & Infrastruktur</h1>
    <p>
        Tabel di bawah ini dikelola secara internal oleh framework Laravel untuk mendukung token pemulihan sandi, sesi server, cache data, dan antrean pekerjaan latar belakang (*queue*).
    </p>

    <h2>3.1. Tabel Sesi & Token</h2>
    
    <h3>A. Tabel `password_reset_tokens`</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Kolom</th>
                <th style="width: 30%;">Tipe Data</th>
                <th style="width: 15%;">Null</th>
                <th style="width: 30%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>email</strong></td>
                <td>varchar(255)</td>
                <td>NO (PK)</td>
                <td>Email penuntut pemulihan password</td>
            </tr>
            <tr>
                <td><strong>token</strong></td>
                <td>varchar(255)</td>
                <td>NO</td>
                <td>Token reset unik tervalidasi</td>
            </tr>
            <tr>
                <td><strong>created_at</strong></td>
                <td>timestamp</td>
                <td>YES</td>
                <td>Waktu pembuatan token</td>
            </tr>
        </tbody>
    </table>

    <h3>B. Tabel `sessions`</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Kolom</th>
                <th style="width: 30%;">Tipe Data</th>
                <th style="width: 15%;">Null</th>
                <th style="width: 30%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>varchar(255)</td>
                <td>NO (PK)</td>
                <td>ID Sesi unik Laravel</td>
            </tr>
            <tr>
                <td><strong>user_id</strong></td>
                <td>bigint unsigned</td>
                <td>YES (IDX)</td>
                <td>ID User terhubung (jika terautentikasi)</td>
            </tr>
            <tr>
                <td><strong>ip_address</strong></td>
                <td>varchar(45)</td>
                <td>YES</td>
                <td>Alamat IP browser pengguna</td>
            </tr>
            <tr>
                <td><strong>user_agent</strong></td>
                <td>text</td>
                <td>YES</td>
                <td>Informasi sistem perangkat klien</td>
            </tr>
            <tr>
                <td><strong>payload</strong></td>
                <td>longtext</td>
                <td>NO</td>
                <td>Data session terenkripsi/terserialisasi</td>
            </tr>
            <tr>
                <td><strong>last_activity</strong></td>
                <td>int</td>
                <td>NO (IDX)</td>
                <td>Unix timestamp aktivitas terakhir</td>
            </tr>
        </tbody>
    </table>

    <h2>3.2. Tabel Caching (`cache` & `cache_locks`)</h2>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Tabel</th>
                <th style="width: 25%;">Kolom</th>
                <th style="width: 25%;">Tipe Data</th>
                <th style="width: 30%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td rowspan="3" style="vertical-align: middle;"><strong>cache</strong></td>
                <td><strong>key</strong></td>
                <td>varchar(255) (PK)</td>
                <td>Key pencarian cache data</td>
            </tr>
            <tr>
                <td><strong>value</strong></td>
                <td>mediumtext</td>
                <td>Isi data cached</td>
            </tr>
            <tr>
                <td><strong>expiration</strong></td>
                <td>bigint (IDX)</td>
                <td>Waktu Unix berakhirnya cache</td>
            </tr>
            <tr>
                <td rowspan="3" style="vertical-align: middle; border-top: 2px solid #E2E8F0;"><strong>cache_locks</strong></td>
                <td style="border-top: 2px solid #E2E8F0;"><strong>key</strong></td>
                <td style="border-top: 2px solid #E2E8F0;">varchar(255) (PK)</td>
                <td style="border-top: 2px solid #E2E8F0;">Key status kunci/mutex lock</td>
            </tr>
            <tr>
                <td><strong>owner</strong></td>
                <td>varchar(255)</td>
                <td>Pemilik token kunci</td>
            </tr>
            <tr>
                <td><strong>expiration</strong></td>
                <td>bigint (IDX)</td>
                <td>Waktu Unix berakhirnya kunci lock</td>
            </tr>
        </tbody>
    </table>

    <div class="page-break"></div>

    <h2>3.3. Tabel Antrean Tugas (`jobs`, `job_batches`, `failed_jobs`)</h2>
    <p>Mendukung antrean proses asinkron untuk memperlancar muatan respons server.</p>

    <h3>A. Tabel `jobs` (Daftar Antrean Aktif)</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Kolom</th>
                <th style="width: 30%;">Tipe Data</th>
                <th style="width: 15%;">Null</th>
                <th style="width: 30%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>bigint unsigned</td>
                <td>NO (PK)</td>
                <td>ID Unik Auto-Increment</td>
            </tr>
            <tr>
                <td><strong>queue</strong></td>
                <td>varchar(255)</td>
                <td>NO (IDX)</td>
                <td>Nama saluran antrean (*channel*)</td>
            </tr>
            <tr>
                <td><strong>payload</strong></td>
                <td>longtext</td>
                <td>NO</td>
                <td>Detail instruksi job terserialisasi</td>
            </tr>
            <tr>
                <td><strong>attempts</strong></td>
                <td>tinyint unsigned</td>
                <td>NO</td>
                <td>Jumlah percobaan eksekusi asinkron</td>
            </tr>
            <tr>
                <td><strong>reserved_at</strong></td>
                <td>int unsigned</td>
                <td>YES</td>
                <td>Timestamp job mulai dikunci worker</td>
            </tr>
            <tr>
                <td><strong>available_at</strong></td>
                <td>int unsigned</td>
                <td>NO</td>
                <td>Timestamp job siap diproses</td>
            </tr>
            <tr>
                <td><strong>created_at</strong></td>
                <td>int unsigned</td>
                <td>NO</td>
                <td>Waktu job masuk ke antrean database</td>
            </tr>
        </tbody>
    </table>

    <h3>B. Tabel `job_batches` (Pengelompokan Antrean)</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Kolom</th>
                <th style="width: 30%;">Tipe Data</th>
                <th style="width: 15%;">Null</th>
                <th style="width: 30%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>varchar(255)</td>
                <td>NO (PK)</td>
                <td>ID Batch unik</td>
            </tr>
            <tr>
                <td><strong>name</strong></td>
                <td>varchar(255)</td>
                <td>NO</td>
                <td>Nama penanda batch pekerjaan</td>
            </tr>
            <tr>
                <td><strong>total_jobs</strong> / <strong>pending_jobs</strong></td>
                <td>int</td>
                <td>NO</td>
                <td>Statistik progres batch</td>
            </tr>
            <tr>
                <td><strong>failed_jobs</strong></td>
                <td>int</td>
                <td>NO</td>
                <td>Jumlah job gagal dalam batch</td>
            </tr>
            <tr>
                <td><strong>failed_job_ids</strong></td>
                <td>longtext</td>
                <td>NO</td>
                <td>Daftar ID job yang gagal</td>
            </tr>
            <tr>
                <td><strong>options</strong></td>
                <td>mediumtext</td>
                <td>YES</td>
                <td>Opsi konfigurasi tambahan</td>
            </tr>
            <tr>
                <td><strong>cancelled_at</strong> / <strong>finished_at</strong></td>
                <td>int</td>
                <td>YES</td>
                <td>Waktu Unix batal atau selesai</td>
            </tr>
            <tr>
                <td><strong>created_at</strong></td>
                <td>int</td>
                <td>NO</td>
                <td>Unix timestamp batch dibuat</td>
            </tr>
        </tbody>
    </table>

    <h3>C. Tabel `failed_jobs` (Log Kegagalan Antrean)</h3>
    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 25%;">Kolom</th>
                <th style="width: 30%;">Tipe Data</th>
                <th style="width: 15%;">Null</th>
                <th style="width: 30%;">Keterangan</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>id</strong></td>
                <td>bigint unsigned</td>
                <td>NO (PK)</td>
                <td>ID Unik Auto-Increment</td>
            </tr>
            <tr>
                <td><strong>uuid</strong></td>
                <td>varchar(255)</td>
                <td>NO (UK)</td>
                <td>UUID unik log kegagalan</td>
            </tr>
            <tr>
                <td><strong>connection</strong> / <strong>queue</strong></td>
                <td>text</td>
                <td>NO</td>
                <td>Koneksi dan nama antrean asal</td>
            </tr>
            <tr>
                <td><strong>payload</strong></td>
                <td>longtext</td>
                <td>NO</td>
                <td>Payload asli dari job yang gagal</td>
            </tr>
            <tr>
                <td><strong>exception</strong></td>
                <td>longtext</td>
                <td>NO</td>
                <td>Stack trace error / penyebab gagal</td>
            </tr>
            <tr>
                <td><strong>failed_at</strong></td>
                <td>timestamp</td>
                <td>NO</td>
                <td>Waktu kegagalan dicatat (default: CURRENT_TIMESTAMP)</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Orens Pro Database Schema Spec &bull; Halaman Akhir
    </div>

</body>
</html>
