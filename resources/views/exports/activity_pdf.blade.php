<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Orens Pro - Spesifikasi & Rincian Activity Diagram</title>
    <style>
        @page {
            size: A4;
            margin: 1.5cm 1.5cm 1.5cm 2cm;
        }
        
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            color: #0F172A; /* slate-900 */
            line-height: 1.4;
            background-color: #ffffff;
            font-size: 10pt;
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
            font-size: 28pt;
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
            margin-top: 1rem;
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

        /* --- Flowchart / Activity Diagram Tables --- */
        .diagram-container {
            width: 100%;
            background-color: #F8FAFC;
            border: 1px solid #E2E8F0;
            border-radius: 8px;
            padding: 1.2rem 1rem;
            margin: 1.2rem 0;
            box-sizing: border-box;
        }

        .diagram-title {
            font-weight: bold;
            font-size: 9.5pt;
            color: #475569;
            text-align: center;
            text-transform: uppercase;
            margin-bottom: 1.2rem;
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
            border: 1.5px solid #E2E8F0;
            border-radius: 6px;
            padding: 6px 10px;
            display: inline-block;
            font-size: 8.5pt;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            max-width: 280px;
        }

        .flow-node.start-end {
            background-color: #0F172A;
            color: #ffffff;
            border-color: #0F172A;
            border-radius: 20px;
            font-weight: bold;
            padding: 6px 16px;
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
            padding: 8px 12px;
        }

        .flow-node.success {
            background-color: #D1FAE5; /* emerald-100 */
            border-color: #10B981; /* emerald-500 */
            color: #065F46;
            font-weight: bold;
        }

        .flow-node.danger {
            background-color: #FEE2E2; /* red-100 */
            border-color: #EF4444; /* red-500 */
            color: #991B1B;
        }

        .flow-arrow {
            font-size: 12pt;
            color: #FF7B00;
            font-weight: bold;
            padding: 4px 0 !important;
        }

        .flow-arrow-horizontal {
            font-size: 12pt;
            color: #FF7B00;
            font-weight: bold;
            padding: 0 6px !important;
        }

        .arrow-text {
            font-size: 7.5pt;
            color: #64748B;
            display: block;
            margin-top: -2px;
        }

        /* --- Column Grid for Activity Diagram (Swimlanes) --- */
        .swimlanes-container {
            width: 100%;
            margin: 1rem 0;
            border: 1px solid #CBD5E1;
            border-radius: 8px;
            background-color: #F8FAFC;
        }

        table.swimlane-table {
            width: 100%;
            border-collapse: collapse;
        }

        table.swimlane-table th {
            background-color: #0F172A;
            color: #ffffff;
            font-weight: bold;
            font-size: 9.5pt;
            padding: 8px;
            border-bottom: 2px solid #FF7B00;
            text-align: center;
        }

        table.swimlane-table td {
            border-right: 1px solid #CBD5E1;
            border-bottom: 1px solid #E2E8F0;
            padding: 10px 8px;
            vertical-align: top;
            width: 33.33%;
            font-size: 8.5pt;
        }

        table.swimlane-table td:last-child {
            border-right: none;
        }

        .lane-step {
            background-color: #ffffff;
            border: 1px solid #E2E8F0;
            border-radius: 4px;
            padding: 6px;
            margin-bottom: 8px;
            box-shadow: 0 1px 2px rgba(0,0,0,0.02);
        }

        .lane-step.highlight {
            border-left: 3px solid #FF7B00;
            background-color: #FFF7ED;
        }

        .lane-step.success {
            border-left: 3px solid #10B981;
            background-color: #F0FDF4;
        }

        .lane-step.danger {
            border-left: 3px solid #EF4444;
            background-color: #FEF2F2;
        }

        .lane-arrow {
            text-align: center;
            color: #FF7B00;
            font-weight: bold;
            margin: 4px 0;
            font-size: 11pt;
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
        }

        table.data-table th {
            background-color: #0F172A;
            color: #ffffff;
            font-weight: bold;
        }

        table.data-table tr:nth-child(even) {
            background-color: #F8FAFC;
        }

        .callout {
            border-left: 4px solid #FF7B00;
            background-color: #F8FAFC;
            padding: 8px 12px;
            margin: 0.8rem 0;
            border-radius: 0 6px 6px 0;
            font-size: 9pt;
        }

        .footer {
            margin-top: 2rem;
            border-top: 1px solid #E2E8F0;
            padding-top: 8px;
            font-size: 7.5pt;
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
            <p class="cover-tagline">Dokumentasi Rencana Desain</p>
            <h1 class="cover-title">Rincian Komprehensif Activity Diagram Aplikasi</h1>
            <p class="cover-subtitle">
                Spesifikasi Alur Aktivitas Aktor (Superadmin, Pembina, Pengurus, Member) dan Sistem untuk Proses Autentikasi, Pembuatan Sesi Geofence, Rotasi QR Code Dinamis, Validasi Haversine, dan Otomasi Penutupan Sesi Alpha.
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
                        Versi: 2.2 (Activity Diagram)<br>
                        Tanggal Rilis PDF: 22 Juni 2026
                    </td>
                </tr>
            </table>
        </div>
    </div>

    <div class="page-break"></div>

    <!-- Section 1 -->
    <h1>1. Pendahuluan</h1>
    <p>
        <strong>Activity Diagram</strong> adalah representasi grafis dari alur kerja (workflow) langkah demi langkah dari aktivitas dan tindakan operasional dalam sistem Orens Pro. Diagram ini memodelkan interaksi antara pengguna (aktor) dengan sistem serta bagaimana data diubah dan disimpan dalam database MySQL.
    </p>
    <p>
        Dokumen ini menyediakan rincian langkah aktivitas untuk 5 proses bisnis utama di sistem Orens Pro:
    </p>
    <ol>
        <li><strong>Autentikasi & Pengalihan Dashboard (RBAC)</strong></li>
        <li><strong>Pembuatan Sesi Pertemuan & Inisialisasi GPS</strong></li>
        <li><strong>Looping Generator & Rotasi QR Code Dinamis</strong></li>
        <li><strong>Presensi Mandiri & Validasi Keamanan Server-Side (Haversine & Time Window)</strong></li>
        <li><strong>Tutup Sesi & Otomasi Status Alpha Retroaktif</strong></li>
    </ol>
    <p>
        Setiap proses dirinci menggunakan tabel <strong>Swimlane (Alur Aktivitas Berdasarkan Aktor)</strong> untuk memisahkan tanggung jawab Aktor Pengguna, Antarmuka Aplikasi (Frontend), dan Server/Sistem (Backend/Database).
    </p>

    <!-- Section 2 -->
    <h1>2. Activity Diagram 1: Autentikasi & Pengalihan Dashboard</h1>
    <p>
        Diagram ini menjelaskan alur aktivitas saat pengguna (Superadmin, Pembina, Pengurus, Member) melakukan login dengan email sekolah resmi.
    </p>

    <div class="swimlanes-container">
        <table class="swimlane-table">
            <thead>
                <tr>
                    <th>Aktor Pengguna</th>
                    <th>Antarmuka Aplikasi (Frontend)</th>
                    <th>Sistem / Server (Backend)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="lane-step highlight">1. Membuka halaman login di browser</div>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">3. Mengisi Email & Password, lalu menekan tombol "Login"</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">2. Menampilkan formulir input email & password</div>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">4. Mengirimkan kredensial (POST request) ke server</div>
                    </td>
                    <td>
                        <br><br><br><br>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">5. Memeriksa domain email sekolah (@smkprestasiprima.sch.id / @smaprestasiprima.sch.id)</div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <br><br>
                        <div class="lane-step danger">6b. Menampilkan pesan kesalahan (Email Harus Menggunakan Domain Sekolah)</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step highlight">6a. [Decision] Apakah Domain Email Valid?</div>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">7. Memeriksa kredensial di database (Bcrypt password check) & status keaktifan akun</div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <br><br>
                        <div class="lane-step danger">8b. Menampilkan pesan kesalahan (Email atau password salah / Akun non-aktif)</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step highlight">8a. [Decision] Apakah Akun Cocok & Aktif?</div>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">9. Membuat session login pengguna & Mengidentifikasi Role Akun</div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="lane-step success">11. Masuk ke halaman Dashboard spesifik role (melihat fitur khusus)</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">10. Mengarahkan (Redirect) pengguna sesuai Role:
                            <ul>
                                <li>Superadmin &rarr; /superadmin</li>
                                <li>Pembina &rarr; /pembina</li>
                                <li>Pengurus &rarr; /pengurus</li>
                                <li>Member &rarr; /member</li>
                            </ul>
                        </div>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Section 3 -->
    <h1>3. Activity Diagram 2: Pembuatan Sesi Pertemuan</h1>
    <p>
        Diagram ini memodelkan aktivitas Pembina atau Pengurus saat menginisialisasi sesi absensi baru lengkap dengan parameter batas geofence.
    </p>

    <div class="swimlanes-container">
        <table class="swimlane-table">
            <thead>
                <tr>
                    <th>Aktor (Pembina / Pengurus)</th>
                    <th>Antarmuka Aplikasi (Frontend)</th>
                    <th>Sistem / Server (Backend)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="lane-step highlight">1. Menekan tombol "Sesi Baru" di menu Sesi</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">2. Membuka form pembuatan sesi baru & memicu HTML5 Geolocation browser</div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <div class="lane-step">3. Mengizinkan browser mengakses sensor GPS perangkat</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">4. Mendeteksi koordinat latitude & longitude admin saat ini dan mengisinya ke input lokasi</div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <div class="lane-step">5. Mengisi data form:
                            <ul>
                                <li>Judul Sesi Pertemuan</li>
                                <li>Tanggal & Jam Sesi</li>
                                <li>Penyesuaian titik GPS & Radius Toleransi (meter)</li>
                            </ul>
                            Lalu mengklik tombol "Simpan Sesi"
                        </td>
                        <td>
                            <div class="lane-arrow">↓</div>
                            <div class="lane-step">6. Mengirim data form sesi (POST request) ke server</div>
                        </td>
                        <td>
                            <div class="lane-arrow">↓</div>
                            <div class="lane-step">7. Melakukan validasi input form (wajib terisi dan format sesuai)</div>
                        </td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td>
                            <div class="lane-arrow">↓</div>
                            <div class="lane-step">8. Meng-generate kunci acak unik (`qr_token`) sebagai Secret Key statis sesi</div>
                            <div class="lane-arrow">↓</div>
                            <div class="lane-step highlight">9. Menyimpan data sesi pertemuan ke tabel <code>attendance_sessions</code></div>
                            <div class="lane-arrow">↓</div>
                            <div class="lane-step">10. Mencatat log pembuatan sesi oleh admin ke tabel <code>audit_logs</code></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <div class="lane-step success">12. Melihat daftar sesi baru berhasil dibuat & siap dimulai</div>
                        </td>
                        <td>
                            <div class="lane-arrow">↓</div>
                            <div class="lane-step">11. Menampilkan notifikasi sukses berwarna hijau "Sesi berhasil dibuat!"</div>
                        </td>
                        <td></td>
                    </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Section 4 -->
    <h1>4. Activity Diagram 3: Generator & Rotasi QR Code Dinamis</h1>
    <p>
        Diagram loop berulang ini menjelaskan bagaimana kode QR yang ditampilkan di depan basecamp berubah otomatis setiap 30 detik untuk memblokir aksi manipulasi tangkapan layar (screenshot).
    </p>

    <div class="swimlanes-container">
        <table class="swimlane-table">
            <thead>
                <tr>
                    <th>Aktor (Pengurus / Pembina)</th>
                    <th>Antarmuka Aplikasi (Frontend)</th>
                    <th>Sistem / Server (Backend)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="lane-step highlight">1. Membuka halaman QR Sesi: <code>/sessions/{id}/qr</code></div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">2. Mengirim GET request halaman QR ke server</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">3. Mengambil record data sesi (mengakses <code>qr_token</code> statis sesi)</div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">4. Membuka view tampilan QR dan memicu interval JavaScript (timer 30 detik)</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">5. Menghitung Token HMAC perdana:
                            <ul>
                                <li>Ambil UNIX timestamp server</li>
                                <li><code>Window = floor(timestamp / 30)</code></li>
                                <li><code>Token = HMAC-SHA256(qr_token, Window)</code></li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="lane-step">7. Memproyeksikan layar ke siswa di basecamp</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step highlight">6. Merender gambar QR Code perdana berisi token HMAC & menampilkan countdown timer</div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <div class="lane-step highlight">8. [Loop Interval] Ketika countdown mencapai 0 (30 detik berlalu)</div>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">9. Melakukan request Ajax / fetch token QR terbaru ke server</div>
                    </td>
                    <td>
                        <br><br><br>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">10. Menghitung Window waktu baru & meng-generate token HMAC-SHA256 baru</div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step success">11. Memperbarui visual QR Code di layar dan mereset countdown timer ke 30 detik</div>
                    </td>
                    <td></td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Section 5 -->
    <h1>5. Activity Diagram 4: Presensi Mandiri (Scan QR & Geofencing)</h1>
    <p>
        Merupakan alur terpenting yang memvalidasi waktu presensi dan jarak geografis (geofencing) siswa ke titik koordinat basecamp ekskul secara real-time.
    </p>

    <div class="swimlanes-container">
        <table class="swimlane-table">
            <thead>
                <tr>
                    <th>Aktor (Siswa / Member)</th>
                    <th>Antarmuka Aplikasi (HP)</th>
                    <th>Sistem / Server (Backend)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="lane-step highlight">1. Menekan tombol "Scan Kehadiran" di dashboard HP</div>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">3. Menyetujui izin akses kamera dan lokasi (GPS)</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">2. Membuka modul kamera pemindai & meminta izin akses Geolocation browser</div>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">4. Memulai pemindaian kode QR di layar proyektor</div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <div class="lane-step">5. Mengarahkan kamera HP ke QR Code dinamis hingga terdeteksi</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">6. Mengambil koordinat GPS sensor HP siswa secara presisi & mengirim POST data presensi</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">7. Memeriksa status sesi absensi (harus aktif dan waktu saat ini belum melewati end_time)</div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <br><br>
                        <div class="lane-step danger">8b. Menampilkan notifikasi gagal: "Sesi tidak aktif / telah ditutup!"</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step highlight">8a. [Decision] Apakah Sesi Masih Aktif?</div>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">9. Memverifikasi Token QR yang dikirim siswa:
                            <ul>
                                <li>Hitung token HMAC server untuk <code>Window</code> saat ini & <code>Window - 1</code></li>
                                <li>Bandingkan token dari HP siswa dengan kedua token server tersebut</li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <br><br>
                        <div class="lane-step danger">10b. Menampilkan notifikasi gagal: "Kode QR kedaluwarsa atau tidak valid!"</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step highlight">10a. [Decision] Apakah Token QR Valid?</div>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">11. Memverifikasi radius lokasi GPS menggunakan rumus Haversine:
                            <ul>
                                <li>Hitung jarak linear (d) antara koordinat GPS siswa dengan koordinat basecamp sesi</li>
                                <li>Periksa apakah jarak (d) &le; radius toleransi sesi</li>
                            </ul>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td></td>
                    <td>
                        <br><br>
                        <div class="lane-step danger">12b. Menampilkan notifikasi gagal: "Anda berada di luar radius lokasi absensi!"</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step highlight">12a. [Decision] Apakah Jarak &le; Radius Sesi?</div>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">13. Menyimpan transaksi absensi berstatus <strong>"hadir"</strong> ke tabel <code>attendances</code></div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="lane-step success">16. Melihat riwayat absensi berubah menjadi "Hadir" lengkap dengan jam masuk</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step success">15. Menampilkan halaman sukses berwarna hijau: "Presensi berhasil! Selamat datang"</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">14. Mencatat log keberhasilan transaksi absensi ke tabel <code>attendance_logs</code></div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="page-break"></div>

    <!-- Section 6 -->
    <h1>6. Activity Diagram 5: Tutup Sesi & Otomasi Status Alpha</h1>
    <p>
        Diagram ini menjelaskan alur penutupan otomatis sesi absensi yang berakhir, diikuti dengan pengisian status Alpha secara retroaktif untuk anggota yang membolos.
    </p>

    <div class="swimlanes-container">
        <table class="swimlane-table">
            <thead>
                <tr>
                    <th>Sistem Otomatis (Cron Job)</th>
                    <th>Database (MySQL)</th>
                    <th>Hak Akses Pembina (Dashboard)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div class="lane-step highlight">1. Memicu pengecekan sesi kedaluwarsa (berkala setiap menit)</div>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">2. Mengambil sesi dengan status <code>is_active = true</code> dan waktu selesai <code>end_time</code> < waktu server saat ini</div>
                    </td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">3. Melakukan update data: menonaktifkan sesi absensi</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step highlight">4. Mengubah kolom <code>is_active = false</code> di tabel <code>attendance_sessions</code></div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">5. Memulai proses retroaktif pengisian Alpha:<br>
                            <code>fillAbsentMembersWithAlpha()</code>
                        </div>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">6. Menarik data ID siswa terdaftar di organisasi ekskul / divisi sesi tersebut</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">7. Melakukan query select ke tabel <code>users</code></div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">8. Menarik data ID siswa yang sudah melakukan presensi (hadir, sakit, izin) untuk sesi tersebut</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">9. Melakukan query select ke tabel <code>attendances</code></div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">10. Membandingkan (array_diff) data ID siswa terdaftar dengan ID siswa hadir untuk memperoleh ID siswa membolos</div>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step">11. Melakukan bulk insert record kehadiran berstatus <strong>"alpha"</strong> untuk siswa membolos</div>
                    </td>
                    <td>
                        <div class="lane-arrow">↓</div>
                        <div class="lane-step highlight">12. Menyimpan data baru ke tabel <code>attendances</code> secara massal</div>
                    </td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td></td>
                    <td>
                        <div class="lane-step success">13. Pembina membuka dashboard dan melihat visual rekapitulasi kehadiran sesi telah lengkap (termasuk status Alpha)</div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <div class="footer">
        Orens Pro System - Spesifikasi Rencana Desain &copy; 2026 SMK & SMA Prestasi Prima, Jakarta
    </div>

</body>
</html>
