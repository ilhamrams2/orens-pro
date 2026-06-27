# Dokumentasi Teknis, Spesifikasi & Panduan Operasional - Orens Pro

Sistem Absensi Ekstrakurikuler Cerdas Terverifikasi (Geofenced & Dynamic Rotating QR Code)
Sekolah: SMK & SMA Prestasi Prima, Jakarta
Versi: 2.1 (Ekskul Context)
Tanggal Diperbarui: 12 Juni 2026

---

## 1. Pendahuluan & Tujuan Sistem

### 1.1. Latar Belakang & Deskripsi Umum
Kegiatan ekstrakurikuler (ekskul) di sekolah menengah seperti SMK & SMA Prestasi Prima memegang peranan krusial dalam pembentukan karakter, bakat, dan kedisiplinan siswa. Namun, pencatatan absensi yang masih bersifat manual (kertas) memiliki kerentanan tinggi terhadap manipulasi (titip absen), data hilang, dan ketidakakuratan rekapitulasi data keaktifan.

**Orens Pro** hadir sebagai platform absensi digital cerdas terintegrasi yang dirancang khusus untuk mengelola absensi kegiatan ekskul secara real-time. Melalui kombinasi teknologi pelacakan lokasi berbasis satelit (Geofencing GPS) dan Kode QR Dinamis (Dynamic Rotating QR), sistem memastikan bahwa siswa yang tercatat hadir benar-benar berada di lokasi basecamp/kegiatan ekskul pada waktu yang ditentukan.

*Catatan Migrasi:* Sistem ini telah dimigrasi secara penuh dari sistem administrasi organisasi umum menjadi sistem yang terfokus pada tata kelola **Ekstrakurikuler (Ekskul)** dan **Subdivisi Ekskul** demi keselarasan kurikulum kesiswaan di sekolah Prestasi Prima.

### 1.2. Tujuan Pengembangan Sistem
1. **Automasi Rekapitulasi:** Memangkas waktu rekap data kehadiran bulanan dari berhari-hari menjadi hitungan detik dengan fitur ekspor multi-sesi dalam format Excel dan PDF.
2. **Integritas Presensi:** Mencegah manipulasi kehadiran siswa melalui pembatasan radius lokasi GPS dan rotasi kode QR setiap 30 detik untuk menghindari duplikasi screenshot.
3. **Evaluasi Keaktifan Siswa:** Menyediakan data akurat bagi Pembina ekskul untuk menentukan kelayakan nilai ekstrakurikuler berdasarkan persentase kehadiran riil.
4. **Audit Trail Berkeamanan Tinggi:** Melacak setiap aktivitas krusial admin (audit logs) dan mendokumentasikan setiap percobaan pindai QR (attendance logs) untuk kebutuhan investigasi.

---

## 2. Spesifikasi Perangkat Keras & Lunak

### 2.1. Spesifikasi Minimum Perangkat Keras (Hardware)
*   **Server (Hosting / VPS):**
    *   Minimum: 1 vCPU, 2 GB RAM, 20 GB SSD Storage
    *   Rekomendasi: 2 vCPU, 4 GB RAM, 40 GB SSD Storage
*   **Siswa (Client - Mobile):**
    *   Minimum: Android 8.0 / iOS 12, Kamera 8 MP, Modul GPS
    *   Rekomendasi: Android 11 / iOS 15, Kamera 12 MP, GPS & A-GPS
*   **Operator (Dashboard Admin):**
    *   Minimum: Intel Core i3, RAM 4 GB, Layar 1366x768
    *   Rekomendasi: Intel Core i5, RAM 8 GB, Layar 1920x1080

### 2.2. Spesifikasi Perangkat Lunak (Software Stack)
*   **Bahasa Pemrograman Utama:** PHP 8.2+ (Backend) & JavaScript ES6 (Frontend)
*   **Framework Backend:** Laravel 11.x (Model-View-Controller architecture)
*   **Template Engine:** Blade Template Engine (Laravel)
*   **Database Management System:** MySQL 8.0+ atau MariaDB 10.4+
*   **Web Server Engine:** Laragon (Local Development) / Apache 2.4 / Nginx
*   **Paket Pendukung Utama:** Composer (Dependency Manager), NPM (Node Package Manager v22.17.0+)

### 2.3. Konfigurasi Lingkungan Sistem (`.env`)
Aplikasi dikonfigurasi melalui berkas `.env` dengan pengaturan konektivitas database MySQL default tanpa password pada lingkungan Laragon lokal:
```env
APP_NAME="Orens Pro"
APP_ENV=local
APP_KEY=base64:h6yJ...
APP_DEBUG=true
APP_URL=http://orens-pro.test

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=orens-pro
DB_USERNAME=root
DB_PASSWORD=

CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database
```

---

## 3. Arsitektur, Infrastruktur & Keamanan

### 3.1. Alur Komunikasi Arsitektur
Sistem ini menggunakan arsitektur monolitik terdistribusi di mana client (browser HP siswa) berinteraksi langsung dengan Laravel Server menggunakan HTTPS request:
1.  **Client-Side:** Mengambil koordinat via HTML5 Geolocation API dan memindai QR Code menggunakan Web Camera.
2.  **Server-Side (Laravel Service):** Memvalidasi koordinat menggunakan rumus matematika Haversine, memvalidasi waktu scan, dan mendekode token HMAC-SHA256.
3.  **Database-Side (MySQL):** Menyimpan transaksi kehadiran secara atomik serta log audit.

### 3.2. Algoritma Perhitungan Radius Lokasi (Geofencing)
Untuk memverifikasi kehadiran siswa, server menghitung jarak linear antara posisi latitude & longitude perangkat siswa dengan posisi latitude & longitude titik absensi yang dibuat oleh pengurus. Perhitungan ini menggunakan **Haversine Formula**:
```
dLat = rad(lat2 - lat1)
dLon = rad(lon2 - lon1)
a = sin²(dLat/2) + cos(lat1) * cos(lat2) * sin²(dLon/2)
c = 2 * atan2(√a, √(1-a))
Jarak (m) = R * c  (dimana R = 6.371.000 meter)
```
Jika hasil perhitungan jarak (dalam satuan meter) lebih besar daripada radius yang ditentukan (misal 100 meter), maka server otomatis menolak check-in dan mengembalikan pesan kesalahan.

### 3.3. Algoritma QR Code Dinamis Terenkripsi (HMAC-SHA256)
Untuk menghindari screenshot kode QR disebarkan ke siswa yang tidak hadir, Orens Pro mengimplementasikan kode QR yang berotasi otomatis setiap 30 detik. Sistem menggunakan algoritma **HMAC-SHA256** (Hash-based Message Authentication Code).
*   **Kunci Rahasia (Secret):** Nilai statis unik `qr_token` sesi absensi yang disimpan di database.
*   **Interval Waktu (Window):** Detik waktu server unix saat ini dibagi 30 (`floor(time() / 30)`). Setiap 30 detik, hasil pembagian ini bertambah 1, menghasilkan hash HMAC baru.
*   **Toleransi Latensi Jaringan:** Server memberikan kompensasi 30 detik ke belakang (`Window - 1`) agar siswa yang melakukan scanning tepat pada saat perubahan token tidak mengalami kegagalan akibat keterlambatan koneksi internet.

### 3.4. Mekanisme Otomasi Penutupan Sesi & Status Alpha (Retroaktif)
Ketika sebuah sesi absensi melewati waktu selesai (`end_time`) yang ditentukan, sistem akan mematikan status aktif sesi tersebut secara otomatis dan langsung melakukan pengisian status secara massal untuk anggota ekskul yang tidak hadir:
*   Fungsi `fillAbsentMembersWithAlpha()` pada model `AttendanceSession.php` secara otomatis mendeteksi semua siswa terdaftar di ekskul/divisi penyelenggara, membandingkannya dengan tabel `attendances`, dan secara instan menyisipkan record berstatus `alpha` untuk siswa yang tidak melakukan check-in mandiri.

---

## 4. Kamus Data & Struktur Database

### A. Tabel `organisations` (Ekstrakurikuler)
Menyimpan data identitas ekstrakurikuler yang terdaftar.
*   `id` (bigint unsigned, PK): ID unik organisasi ekskul.
*   `name` (varchar(150)): Nama ekstrakurikuler (contoh: Orens Solution).
*   `address` (text, Nullable): Lokasi basecamp / sekretariat ekskul.
*   `last_grade_reset_at` (timestamp, Nullable): Waktu reset penilaian keaktifan terakhir.
*   `description` (text, Nullable): Deskripsi visi & misi ekskul.
*   `has_division` (boolean): Status penunjuk apakah ekskul memiliki subdivisi.

### B. Tabel `divisions` (Divisi Ekskul)
Divisi spesifik di dalam suatu ekskul (contoh: Cyber Security pada ekskul Orens Solution).
*   `id` (bigint unsigned, PK): ID unik divisi ekskul.
*   `organisation_id` (bigint unsigned, FK): Relasi ke tabel `organisations` (Cascade).
*   `name` (varchar(150)): Nama divisi.
*   `description` (text, Nullable): Fokus tugas divisi.

### C. Tabel `users` (Pengguna Sistem)
Tabel induk seluruh akun pengguna dengan sistem RBAC (Role-Based Access Control).
*   `id` (bigint unsigned, PK): ID unik pengguna.
*   `organisation_id` (bigint unsigned, FK, Nullable): Relasi ke tabel `organisations`.
*   `division_id` (bigint unsigned, FK, Nullable): Relasi ke tabel `divisions`.
*   `name` (varchar(255)): Nama lengkap pengguna.
*   `email` (varchar(255)): Alamat email unik (@smkprestasiprima.sch.id / @smaprestasiprima.sch.id).
*   `password` (varchar(255)): Hash password Bcrypt.
*   `role` (varchar(30)): Hak akses: `superadmin`, `pembina`, `pengurus`, `member`.
*   `is_active` (boolean): Status login aktif/blokir.

### D. Tabel `attendance_sessions` (Sesi Absensi)
Sesi presensi yang di-generate untuk kegiatan pertemuan ekskul.
*   `id` (bigint unsigned, PK): ID unik sesi.
*   `organisation_id` (bigint unsigned, FK, Nullable): Ekskul penyelenggara sesi.
*   `division_id` (bigint unsigned, FK, Nullable): Divisi penyelenggara (Null jika sesi global ekskul).
*   `title` (varchar(200), Nullable): Judul kegiatan (contoh: Latihan Dasar Server).
*   `qr_token` (varchar(255)): Kunci secret statis untuk generator HMAC QR Code.
*   `session_date` (date): Tanggal kegiatan.
*   `start_time` / `end_time` (time, Nullable): Waktu mulai dibuka dan batas waktu selesai absen.
*   `latitude` / `longitude` (decimal(10,7), Nullable): Koordinat tengah lokasi basecamp kegiatan ekskul.
*   `radius` (integer, Nullable): Jarak toleransi GPS dalam meter (default: 100).
*   `is_active` (boolean): Status keaktifan penerimaan check-in.
*   `created_by` (bigint unsigned, FK): ID user pembuat sesi (Cascade).

### E. Tabel `attendances` (Presensi Anggota)
Log kehadiran transaksi utama yang memverifikasi kehadiran siswa.
*   `id` (bigint unsigned, PK): ID unik transaksi.
*   `user_id` (bigint unsigned, FK): ID siswa (Cascade).
*   `session_id` (bigint unsigned, FK): ID Sesi (Cascade).
*   `checkin_time` (timestamp, Nullable): Waktu tepat check-in dipencet siswa.
*   `latitude` / `longitude` (decimal(10,7), Nullable): Koordinat GPS riil perangkat siswa saat scan.
*   `distance` (integer, Nullable): Jarak riil ke koordinat sesi dalam meter saat checkin.
*   `status` (varchar(30), Nullable): Status: `hadir`, `sakit`, `izin`, `alpha`.

### F. Tabel `attendance_logs` (Log Percobaan Absensi)
Mencatat riwayat log proses verifikasi scan QR, mempermudah forensik audit jika terjadi klaim gagal scan.
*   `id` (bigint unsigned, PK): ID unik log.
*   `user_id` (bigint unsigned, FK, Nullable): Siswa pencoba absensi.
*   `qr_token` (varchar(255)): Token QR yang dikirim oleh device.
*   `latitude` / `longitude` (decimal(10,7), Nullable): Posisi GPS koordinat siswa saat mencoba scanning.
*   `result` (text, Nullable): Status log hasil (contoh: "Presensi berhasil" atau kegagalan radius GPS).

### G. Tabel `audit_logs` (Log Audit Aktivitas Admin)
Mencatat setiap transaksi modifikasi data di platform oleh admin untuk kepatuhan operasional.
*   `id` (bigint unsigned, PK): ID unik log audit.
*   `user_id` (bigint unsigned, FK, Nullable): ID admin pelaku.
*   `event` (varchar(255)): Tipe event: `created`, `updated`, `deleted`, `login`.
*   `old_values` / `new_values` (json, Nullable): State data sebelum dan sesudah modifikasi.
*   `ip_address` (varchar(45), Nullable): Alamat IP admin.

---

## 5. Alur Kerja & Panduan Operasional Pengguna

### 5.1. Matriks Hak Akses Pengguna (RBAC Matrix)
*   **Superadmin:** Mengelola seluruh ekskul, divisi, log audit trail global, reset nilai, dan manajemen semua admin (pembina).
*   **Pembina:** Mengelola spesifik satu organisasi ekskul, melihat divisi, mengelola data siswa/reset keaktifan ekskulnya, membuat sesi absensi, dan mengekspor laporan komulatif.
*   **Pengurus:** Membuat sesi absensi khusus divisi masing-masing, menentukan koordinat & radius lokasi, menampilkan layar QR dinamis, melakukan marking manual.
*   **Member:** Melakukan check-in mandiri menggunakan kamera web browser dan sensor GPS handphone dalam radius absensi.

### 5.2. Langkah Operasional Pembina Ekskul
1.  **Login ke Dashboard:** Akses halaman login di `/login` dengan email resmi pembina, misalnya `pembina1@smkprestasiprima.sch.id`.
2.  **Manajemen Divisi:** Pembina dapat menambahkan divisi baru (misal: "Web Development" atau "Game Development") melalui menu Divisi.
3.  **Reset Nilai Keaktifan (Grade Reset):** Di akhir semester, Pembina dapat meriset nilai keaktifan seluruh siswa di ekskulnya dengan satu kali klik. Aksi ini tercatat secara ketat di `audit_logs`.
4.  **Ekspor Laporan Komulatif:** Buka menu Sesi, pilih beberapa sesi absensi sekaligus (multi-session report), dan klik "Generate Report". Pembina dapat mengunduh berkas rekap PDF atau Excel yang menampilkan persentase kehadiran masing-masing siswa.

### 5.3. Langkah Operasional Pengurus (Leader/Admin Divisi)
1.  **Membuat Sesi Pertemuan:** Klik "Sesi Baru", isi nama pertemuan (misal: "Latihan Dasar Web"), tentukan tanggal, jam buka dan jam tutup sesi absensi.
2.  **Menentukan Titik GPS & Radius Geofencing:** Tentukan lokasi basecamp ekskul melalui peta/koordinat dan radius toleransi (contoh: 50 meter).
3.  **Menampilkan Layar QR Code Dinamis:** Proyeksikan atau tampilkan halaman QR Code Sesi di depan ruang latihan. Token QR akan berotasi setiap 30 detik.
4.  **Marking Manual:** Jika ada member yang sakit/izin/tidak membawa hp, Pengurus dapat menandai secara manual status `sakit`, `izin`, atau `hadir` lewat panel "Marking Sheet".

### 5.4. Langkah Operasional Member (Siswa/Anggota Ekskul)
1.  **Akses Menggunakan Handphone:** Login ke `/login` menggunakan akun email member (contoh: `game1@smkprestasiprima.sch.id`).
2.  **Buka Panel Kamera:** Pada Dashboard utama, klik tombol "Scan Kehadiran". Berikan izin akses kamera dan lokasi GPS (Geolocation API) pada browser smartphone.
3.  **Pindai Kode QR:** Arahkan kamera smartphone ke kode QR dinamis yang ditampilkan oleh pengurus di depan kelas.
4.  **Konfirmasi Kehadiran:** Sistem secara otomatis mengirimkan token QR terenkripsi beserta titik GPS siswa saat memindai ke server. Jika lokasi siswa berada di dalam radius toleransi, notifikasi sukses hijau "Presensi berhasil! Selamat datang" akan muncul dan data masuk ke tabel database.

---

## 6. Analisis Keamanan & Pencegahan Kecurangan

### 6.1. Mekanisme Anti-Proxy & Titip Absen
Orens Pro menggunakan metode **Dual-Factor Verification** untuk meminimalkan celah manipulasi absensi:
1.  **Pembatasan Berbagi Screenshot (Dynamic QR):** Karena token QR berubah setiap 30 detik berdasarkan timestamp server terenkripsi HMAC-SHA256, siswa tidak dapat men-screenshot kode QR lalu membagikannya ke grup chat untuk dipindai oleh temannya dari rumah. Jika screenshot dipindai setelah melewati 30-60 detik, server langsung menolak dengan status `Kode QR tidak valid atau kedaluwarsa`.
2.  **Verifikasi Posisi Geografis Riil (Geofencing):** Meskipun siswa mendapatkan salinan token QR dinamis langsung, server tetap melakukan validasi koordinat GPS yang dikirim oleh perangkat klien. Jika siswa memindai QR dinamis di luar radius basecamp ekskul, sistem langsung membatalkan absensi dan mencatat percobaan kecurangan tersebut ke dalam tabel log.

### 6.2. Forensik & Akuntabilitas Data
*   **Log Percobaan Absensi (`attendance_logs`):** Menyimpan metadata lengkap setiap pemindaian, termasuk parameter latitude/longitude siswa yang gagal melakukan absensi. Pembina ekskul dapat mengecek log ini apabila ada siswa yang mengklaim "sudah hadir tapi tidak terinput di sistem".
*   **Audit Trail Transaksi (`audit_logs`):** Seluruh tindakan manipulasi data pengguna atau perubahan database oleh admin (Superadmin, Pembina, Pengurus) dicatat lengkap dengan data sebelum diubah (old values), data sesudah diubah (new values), alamat IP, dan User Agent perangkat. Fitur ini menjamin transparansi mutlak dan mencegah penyalahgunaan wewenang admin.
