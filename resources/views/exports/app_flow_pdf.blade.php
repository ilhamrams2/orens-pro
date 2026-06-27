<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Orens Pro - Alur Kerja & Flowchart Aplikasi</title>
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
            font-size: 11pt;
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
            font-size: 32pt;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 1.5rem;
        }

        .cover-subtitle {
            font-size: 13pt;
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
            font-size: 20pt;
            color: #0F172A;
            border-bottom: 2px solid #FF7B00;
            padding-bottom: 5px;
            margin-top: 2rem;
            margin-bottom: 1.5rem;
        }

        h2 {
            font-size: 14pt;
            color: #1E293B;
            margin-top: 1.8rem;
            margin-bottom: 0.8rem;
            border-left: 4px solid #FF7B00;
            padding-left: 8px;
        }

        h3 {
            font-size: 11pt;
            color: #334155;
            margin-top: 1.2rem;
            margin-bottom: 0.5rem;
        }

        p {
            margin-bottom: 1rem;
            text-align: justify;
            color: #334155;
        }

        ol, ul {
            margin-bottom: 1rem;
            padding-left: 1.5rem;
        }

        li {
            margin-bottom: 0.4rem;
            color: #334155;
        }

        /* --- Flowchart Diagram Tables --- */
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
            font-size: 10pt;
            color: #475569;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
            letter-spacing: 1px;
        }

        .flowchart-table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
        }

        .flowchart-table td {
            border: none;
            padding: 4px;
            text-align: center;
            vertical-align: middle;
        }

        .flow-node {
            background-color: #ffffff;
            border: 2px solid #E2E8F0;
            border-radius: 6px;
            padding: 8px 12px;
            display: inline-block;
            font-size: 9pt;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            max-width: 250px;
        }

        .flow-node.start-end {
            background-color: #0F172A;
            color: #ffffff;
            border-color: #0F172A;
            border-radius: 20px;
            font-weight: bold;
        }

        .flow-node.process {
            border-color: #CBD5E1;
            background-color: #F8FAFC;
        }

        .flow-node.highlight {
            border-color: #FF7B00;
            background-color: #FFF7ED; /* orange-50 */
            font-weight: 600;
        }

        .flow-node.decision {
            background-color: #FEF3C7; /* amber-100 */
            border-color: #F59E0B; /* amber-500 */
            border-radius: 6px;
            font-weight: 600;
            padding: 10px 14px;
        }

        .flow-node.success {
            background-color: #D1FAE5; /* emerald-100 */
            border-color: #10B981; /* emerald-500 */
            color: #065F46;
        }

        .flow-node.danger {
            background-color: #FEE2E2; /* red-100 */
            border-color: #EF4444; /* red-500 */
            color: #991B1B;
        }

        .flow-arrow {
            font-size: 14pt;
            color: #FF7B00;
            font-weight: bold;
            padding: 8px 0 !important;
        }

        .flow-arrow-horizontal {
            font-size: 14pt;
            color: #FF7B00;
            font-weight: bold;
            padding: 0 8px !important;
        }

        .arrow-text {
            font-size: 7.5pt;
            color: #64748B;
            display: block;
            margin-top: -3px;
        }

        /* --- Custom Grid columns for PDF --- */
        .row {
            width: 100%;
            margin-bottom: 1.5rem;
        }
        .col-50 {
            width: 48%;
            display: inline-block;
            vertical-align: top;
        }
        .col-50-spacer {
            width: 3%;
            display: inline-block;
        }

        /* --- Tables --- */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.2rem 0;
            font-size: 9pt;
        }

        table.data-table th, table.data-table td {
            padding: 8px 10px;
            border: 1px solid #E2E8F0;
            text-align: left;
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
        .role-badge {
            display: inline-block;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 7.5pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .badge-superadmin { background-color: #EDE9FE; color: #6D28D9; }
        .badge-pembina { background-color: #DBEAFE; color: #1D4ED8; }
        .badge-pengurus { background-color: #FEF3C7; color: #D97706; }
        .badge-member { background-color: #D1FAE5; color: #047857; }

        .callout {
            border-left: 4px solid #FF7B00;
            background-color: #F8FAFC;
            padding: 10px 15px;
            margin: 1rem 0;
            border-radius: 0 6px 6px 0;
            font-size: 9.5pt;
        }

        .footer {
            margin-top: 3rem;
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
            <p class="cover-tagline">Dokumentasi Alur & Flowchart</p>
            <h1 class="cover-title">Peta Alur Kerja & Daftar Fitur Sistem Absensi</h1>
            <p class="cover-subtitle">
                Visualisasi Alur Autentikasi RBAC, Generator QR Dinamis, Validasi Geofencing GPS, dan Otomasi Penutupan Sesi Alpha Secara Komprehensif.
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
                        Diperbarui: 18 Juni 2026
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- Section 1 -->
    <h1>1. Deskripsi Alur & Hak Akses Pengguna (RBAC)</h1>
    <p>
        Sistem <strong>Orens Pro</strong> menerapkan kontrol akses berbasis peran (Role-Based Access Control / RBAC) yang ketat untuk mengamankan tata kelola absensi ekstrakurikuler. Terdapat empat peran utama dengan hak akses yang terdistribusi secara bertingkat:
    </p>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 20%;">Peran Pengguna</th>
                <th style="width: 30%;">Ruang Lingkup Akses</th>
                <th style="width: 50%;">Fitur Utama</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><span class="role-badge badge-superadmin">Superadmin</span></td>
                <td>Sistem Global (Multi-Sekolah/Ekskul)</td>
                <td>Mengelola seluruh data ekskul, divisi, audit trail global, reset nilai, dan manajemen akun guru pembina.</td>
            </tr>
            <tr>
                <td><span class="role-badge badge-pembina">Pembina</span></td>
                <td>Spesifik Satu Ekskul (Induk)</td>
                <td>Mengelola divisi ekskulnya, mendaftarkan/mengimpor data siswa, reset nilai keaktifan semesteran, membuat sesi, dan mengunduh laporan komulatif multi-sesi.</td>
            </tr>
            <tr>
                <td><span class="role-badge badge-pengurus">Pengurus</span></td>
                <td>Spesifik Divisi di dalam Ekskul</td>
                <td>Membuat sesi pertemuan divisi, mengatur titik koordinat GPS & radius geofence, menampilkan layar QR dinamis, dan menandai absensi siswa secara manual (Marking Sheet).</td>
            </tr>
            <tr>
                <td><span class="role-badge badge-member">Member</span></td>
                <td>Personal (Siswa)</td>
                <td>Melakukan check-in mandiri dengan memindai kode QR dinamis di basecamp ekskul dan melihat riwayat kehadiran pribadi.</td>
            </tr>
        </tbody>
    </table>

    <h2>Flowchart 1: Alur Autentikasi & Pengalihan Dashboard</h2>
    <p>
        Setiap pengguna wajib melakukan login menggunakan email sekolah resmi. Sistem akan menolak email dengan domain umum dan langsung mengarahkan user ke dashboard yang sesuai setelah terautentikasi.
    </p>

    <div class="diagram-container">
        <div class="diagram-title">Diagram Alur Autentikasi Pengguna</div>
        <table class="flowchart-table" style="max-width: 500px;">
            <tr>
                <td colspan="3"><div class="flow-node start-end">Mulai</div></td>
            </tr>
            <tr>
                <td colspan="3" class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="flow-node process">
                        Siswa/Guru membuka halaman <strong>/login</strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="flow-node process">
                        Input Email & Password<br>
                        <span style="font-size: 8pt; color: #64748B;">(Wajib domain @smkprestasiprima.sch.id / @smaprestasiprima.sch.id)</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="flow-node decision">
                        Kredensial Valid &<br>Akun Aktif?
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 25%;"></td>
                <td style="width: 50%;" class="flow-arrow">
                    ↓ <span class="arrow-text">Ya</span>
                </td>
                <td style="width: 25%; text-align: left; vertical-align: top; padding-top: 10px;">
                    <span class="flow-arrow-horizontal">→</span> <span class="arrow-text" style="display:inline;">Tidak</span>
                    <div class="flow-node danger" style="margin-top: 5px;">Tampilkan Error Login</div>
                </td>
            </tr>
            <tr>
                <td colspan="3">
                    <div class="flow-node process">
                        Sistem mengidentifikasi <strong>User Role</strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td colspan="3" style="padding: 10px 0;">
                    <table style="width: 100%; border-collapse: collapse;">
                        <tr>
                            <td style="width: 25%; vertical-align: top;">
                                <div class="flow-node highlight" style="font-size: 8pt;">Role: Superadmin</div>
                                <div class="flow-arrow">↓</div>
                                <div class="flow-node process" style="font-size: 8pt;">Redirect Dashboard Superadmin</div>
                            </td>
                            <td style="width: 25%; vertical-align: top;">
                                <div class="flow-node highlight" style="font-size: 8pt;">Role: Pembina</div>
                                <div class="flow-arrow">↓</div>
                                <div class="flow-node process" style="font-size: 8pt;">Redirect Dashboard Pembina</div>
                            </td>
                            <td style="width: 25%; vertical-align: top;">
                                <div class="flow-node highlight" style="font-size: 8pt;">Role: Pengurus</div>
                                <div class="flow-arrow">↓</div>
                                <div class="flow-node process" style="font-size: 8pt;">Redirect Dashboard Pengurus</div>
                            </td>
                            <td style="width: 25%; vertical-align: top;">
                                <div class="flow-node highlight" style="font-size: 8pt;">Role: Member</div>
                                <div class="flow-arrow">↓</div>
                                <div class="flow-node process" style="font-size: 8pt;">Redirect Dashboard Member</div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td colspan="3" class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td colspan="3"><div class="flow-node start-end">Selesai</div></td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Section 2 -->
    <h1>2. Alur Pembuatan Sesi & Generator QR Dinamis</h1>
    <p>
        Sesi absensi dibuat oleh Pengurus atau Pembina. Untuk mencegah pembagian kode QR (titip absen), sistem mengimplementasikan kode QR dinamis berbasis waktu (HMAC-SHA256) yang berubah setiap 30 detik.
    </p>

    <div class="diagram-container">
        <div class="diagram-title">Diagram Alur Pembuatan Sesi & Rotasi QR Code</div>
        <table class="flowchart-table" style="max-width: 550px;">
            <tr>
                <td><div class="flow-node start-end">Mulai</div></td>
            </tr>
            <tr>
                <td class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td>
                    <div class="flow-node process">
                        Pembina/Pengurus membuat Sesi Baru di <strong>/sessions/create</strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td>
                    <div class="flow-node process">
                        Input: Judul Sesi, Tanggal, Jam Mulai, Jam Selesai,<br>
                        Koordinat GPS Basecamp (Lat & Lng), dan Radius Geofence (meter)
                    </div>
                </td>
            </tr>
            <tr>
                <td class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td>
                    <div class="flow-node process">
                        Sistem menyimpan data & meng-generate <strong>qr_token</strong> statis sebagai kunci rahasia (secret key)
                    </div>
                </td>
            </tr>
            <tr>
                <td class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td>
                    <div class="flow-node process">
                        Pengurus membuka halaman tampilan QR: <strong>/sessions/{id}/qr</strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td>
                    <div class="flow-node highlight">
                        Looping Proses di Layar QR (Setiap 30 Detik):
                    </div>
                    <div style="border: 1px dashed #FF7B00; border-radius: 6px; padding: 10px; background-color: #FFFDFB; margin-top: 5px;">
                        <ol style="margin-bottom: 0; text-align: left; font-size: 8.5pt;">
                            <li>Sistem mengambil Unix Timestamp saat ini.</li>
                            <li>Menghitung index window waktu saat ini: <code>Window = floor(time() / 30)</code>.</li>
                            <li>Meng-generate token QR dinamis dengan rumus: <code>HMAC-SHA256(secret_key, Window)</code>.</li>
                            <li>Memperbarui gambar kode QR di layar proyektor dengan token baru.</li>
                        </ol>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td><div class="flow-node start-end">Sesi Siap Dipindai</div></td>
            </tr>
        </table>
    </div>

    <div class="callout">
        <strong>Keamanan HMAC-SHA256:</strong> Token QR yang ditampilkan di depan basecamp ekskul tidak mengandung ID atau data sensitif mentah, melainkan tanda tangan kriptografi berstempel waktu yang hanya valid selama 30 detik untuk menghindari duplikasi tangkapan layar (screenshot).
    </div>

    <div class="page-break"></div>

    <!-- Section 3 -->
    <h1>3. Alur Presensi Mandiri (Scan QR & Geofencing)</h1>
    <p>
        Member yang hadir di lokasi basecamp memindai kode QR dinamis menggunakan kamera smartphone mereka. Aplikasi secara otomatis mengambil koordinat GPS member saat pemindaian dan mengirimkannya ke server untuk proses verifikasi berlapis.
    </p>

    <div class="diagram-container">
        <div class="diagram-title">Diagram Validasi Presensi di Sisi Server</div>
        <table class="flowchart-table" style="max-width: 600px;">
            <tr>
                <td colspan="4"><div class="flow-node start-end">Mulai Scan QR</div></td>
            </tr>
            <tr>
                <td colspan="4" class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="flow-node process">
                        Siswa klik "Scan Kehadiran" di HP. Browser meminta izin Kamera dan Lokasi (GPS).
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td colspan="4">
                    <div class="flow-node process">
                        Kamera mendeteksi URL QR Code & mengambil koordinat GPS Siswa.<br>
                        Mengirim data POST ke <strong>/sessions/{session}/checkin</strong>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="flow-arrow">↓</td>
            </tr>
            <!-- Validasi 1: Sesi Aktif -->
            <tr>
                <td colspan="4">
                    <div class="flow-node decision">
                        1. Apakah Sesi Aktif & Waktu Masih Valid?
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 15%;"></td>
                <td style="width: 50%;" class="flow-arrow">↓ <span class="arrow-text">Ya</span></td>
                <td colspan="2" style="width: 35%; text-align: left; vertical-align: top; padding-top: 10px;">
                    <span class="flow-arrow-horizontal">→</span> <span class="arrow-text" style="display:inline;">Tidak</span>
                    <div class="flow-node danger" style="margin-top: 5px; font-size: 8pt;">Gagal: "Sesi tidak aktif / berakhir"</div>
                </td>
            </tr>
            <!-- Validasi 2: Token QR -->
            <tr>
                <td colspan="4">
                    <div class="flow-node decision">
                        2. Apakah Token QR Valid (Window saat ini ATAU Window - 1)?
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 15%;"></td>
                <td style="width: 50%;" class="flow-arrow">↓ <span class="arrow-text">Ya</span></td>
                <td colspan="2" style="width: 35%; text-align: left; vertical-align: top; padding-top: 10px;">
                    <span class="flow-arrow-horizontal">→</span> <span class="arrow-text" style="display:inline;">Tidak</span>
                    <div class="flow-node danger" style="margin-top: 5px; font-size: 8pt;">Gagal: "Kode QR kedaluwarsa"</div>
                </td>
            </tr>
            <!-- Validasi 3: Geofence -->
            <tr>
                <td colspan="4">
                    <div class="flow-node decision">
                        3. Apakah Jarak GPS Siswa ke Basecamp &le; Radius Toleransi Sesi?<br>
                        <span style="font-size: 8pt; font-weight: normal; color: #475569;">(Dihitung dengan rumus Haversine di server)</span>
                    </div>
                </td>
            </tr>
            <tr>
                <td style="width: 15%;"></td>
                <td style="width: 50%;" class="flow-arrow">↓ <span class="arrow-text">Ya</span></td>
                <td colspan="2" style="width: 35%; text-align: left; vertical-align: top; padding-top: 10px;">
                    <span class="flow-arrow-horizontal">→</span> <span class="arrow-text" style="display:inline;">Tidak</span>
                    <div class="flow-node danger" style="margin-top: 5px; font-size: 8pt;">Gagal: "Jarak terlalu jauh (X meter)"</div>
                </td>
            </tr>
            <!-- Sukses -->
            <tr>
                <td colspan="4">
                    <div class="flow-node success">
                        <strong>PRESENSI BERHASIL</strong><br>
                        Sistem menyimpan status "hadir" di tabel <code>attendances</code><br>
                        dan mencatat histori ke <code>attendance_logs</code>
                    </div>
                </td>
            </tr>
            <tr>
                <td colspan="4" class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td colspan="4"><div class="flow-node start-end">Selesai</div></td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Section 4 -->
    <h1>4. Alur Penutupan Sesi & Otomasi Status Alpha</h1>
    <p>
        Ketika waktu selesai sesi absensi terlampaui, sistem secara otomatis menonaktifkan status sesi tersebut. Secara retroaktif, seluruh anggota terdaftar yang tidak melakukan check-in mandiri akan ditandai dengan status <strong>Alpha</strong> oleh sistem.
    </p>

    <div class="diagram-container">
        <div class="diagram-title">Diagram Alur Penutupan Sesi & Auto-Alpha</div>
        <table class="flowchart-table" style="max-width: 500px;">
            <tr>
                <td><div class="flow-node start-end">Mulai Sesi Aktif</div></td>
            </tr>
            <tr>
                <td class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td>
                    <div class="flow-node process">
                        Waktu saat ini melewati batas <strong>end_time</strong> sesi absensi
                    </div>
                </td>
            </tr>
            <tr>
                <td class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td>
                    <div class="flow-node process">
                        Pengguna membuka Dashboard atau Cron Trigger memicu:<br>
                        <code>AttendanceSession::deactivateExpiredSessions()</code>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td>
                    <div class="flow-node process">
                        Sistem memperbarui status sesi: <code>is_active = false</code>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td>
                    <div class="flow-node highlight">
                        Fungsi fillAbsentMembersWithAlpha() Berjalan:
                    </div>
                    <div style="border: 1px dashed #FF7B00; border-radius: 6px; padding: 10px; background-color: #FFFDFB; margin-top: 5px;">
                        <ol style="margin-bottom: 0; text-align: left; font-size: 8.5pt;">
                            <li>Mengambil daftar ID semua siswa yang terdaftar di ekskul/divisi penyelenggara.</li>
                            <li>Mengambil daftar ID siswa yang sudah memiliki transaksi kehadiran (hadir, sakit, izin) untuk sesi ini.</li>
                            <li>Menghitung selisih (diff) untuk mendapatkan ID siswa yang tidak melakukan absensi.</li>
                            <li>Membuat record absensi baru secara massal dengan status <strong>alpha</strong> untuk siswa-siswa tersebut.</li>
                        </ol>
                    </div>
                </td>
            </tr>
            <tr>
                <td class="flow-arrow">↓</td>
            </tr>
            <tr>
                <td><div class="flow-node start-end">Rekapitulasi Kehadiran Sesi Lengkap</div></td>
            </tr>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Section 5 -->
    <h1>5. Alur Rekapitulasi Laporan & Penilaian Akhir</h1>
    <p>
        Akumulasi kehadiran siswa dihitung secara kumulatif dari semua sesi pertemuan wajib. Nilai akhir keaktifan siswa (Grade) ditentukan berdasarkan aturan konversi berikut:
    </p>

    <div class="row">
        <div class="col-50">
            <h3 style="margin-top: 0;">Aturan Konversi Nilai Keaktifan (Grade):</h3>
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Total Hadir</th>
                        <th>Grade</th>
                        <th>Kategori</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>&ge; 4 Sesi</td>
                        <td><strong style="color: green;">A</strong></td>
                        <td>Sangat Aktif</td>
                    </tr>
                    <tr>
                        <td>2 s.d 3 Sesi</td>
                        <td><strong style="color: blue;">B</strong></td>
                        <td>Aktif</td>
                    </tr>
                    <tr>
                        <td>&lt; 2 Sesi</td>
                        <td><strong style="color: gray;">-</strong></td>
                        <td>Kurang Aktif</td>
                    </tr>
                </tbody>
            </table>
        </div>
        
        <div class="col-50-spacer"></div>

        <div class="col-50">
            <h3 style="margin-top: 0;">Alur Evaluasi Nilai Akhir Semester:</h3>
            <ol style="font-size: 9pt; line-height: 1.6;">
                <li>Siswa mengikuti rangkaian sesi ekskul selama satu semester.</li>
                <li>Sistem menjumlahkan status kehadiran <code>hadir</code> untuk setiap siswa.</li>
                <li>Pembina membuka menu anggota dan mengekspor nilai ke dalam format Excel/PDF untuk diserahkan ke kesiswaan.</li>
                <li>Setelah pembagian nilai, Pembina menekan tombol <strong>"Reset Nilai Keaktifan"</strong> untuk mengosongkan riwayat nilai menyambut semester baru.</li>
                <li>Aksi reset memperbarui kolom <code>last_grade_reset_at</code> di tabel <code>organisations</code>. Perhitungan hadir siswa berikutnya hanya menghitung kehadiran setelah tanggal reset tersebut.</li>
            </ol>
        </div>
    </div>

    <h2>Kamus Data Database (Relasi ERD Ringkas)</h2>
    <p>
        Hubungan tabel dalam Orens Pro dirancang bertingkat untuk mengakomodasi struktur: 
        <strong>Organisations (Ekskul) &rarr; Divisions (Divisi) &rarr; Users (Pengguna) &rarr; Attendances (Kehadiran)</strong>.
    </p>

    <table class="data-table" style="font-size: 8.5pt;">
        <thead>
            <tr>
                <th>Tabel</th>
                <th>Primary Key</th>
                <th>Foreign Keys</th>
                <th>Kolom Penting</th>
                <th>Keterangan / Fungsi Utama</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td><strong>organisations</strong></td>
                <td><code>id</code></td>
                <td>-</td>
                <td><code>name</code>, <code>last_grade_reset_at</code></td>
                <td>Menyimpan data induk nama ekskul sekolah.</td>
            </tr>
            <tr>
                <td><strong>divisions</strong></td>
                <td><code>id</code></td>
                <td><code>organisation_id</code></td>
                <td><code>name</code>, <code>description</code></td>
                <td>Subdivisi spesifik ekskul (contoh: Cyber Security).</td>
            </tr>
            <tr>
                <td><strong>users</strong></td>
                <td><code>id</code></td>
                <td><code>organisation_id</code>, <code>division_id</code></td>
                <td><code>email</code>, <code>role</code>, <code>is_active</code></td>
                <td>Akun pengguna sistem (RBAC 4 Role).</td>
            </tr>
            <tr>
                <td><strong>attendance_sessions</strong></td>
                <td><code>id</code></td>
                <td><code>organisation_id</code>, <code>division_id</code>, <code>created_by</code></td>
                <td><code>qr_token</code>, <code>latitude</code>, <code>longitude</code>, <code>radius</code></td>
                <td>Sesi presensi geofenced yang dibuat pengurus.</td>
            </tr>
            <tr>
                <td><strong>attendances</strong></td>
                <td><code>id</code></td>
                <td><code>user_id</code>, <code>session_id</code></td>
                <td><code>status</code> (hadir/sakit/izin/alpha), <code>checkin_time</code></td>
                <td>Tabel transaksi absensi riil siswa.</td>
            </tr>
            <tr>
                <td><strong>attendance_logs</strong></td>
                <td><code>id</code></td>
                <td><code>user_id</code></td>
                <td><code>qr_token</code>, <code>result</code></td>
                <td>Log audit hasil percobaan scan (sukses/gagal GPS).</td>
            </tr>
            <tr>
                <td><strong>audit_logs</strong></td>
                <td><code>id</code></td>
                <td><code>user_id</code></td>
                <td><code>event</code>, <code>old_values</code>, <code>new_values</code></td>
                <td>Log forensik aktivitas perubahan data oleh admin.</td>
            </tr>
        </tbody>
    </table>

    <div class="footer">
        Dicetak secara otomatis oleh sistem OrensPro pada {{ now()->format('d M Y H:i') }}
    </div>

</body>
</html>
