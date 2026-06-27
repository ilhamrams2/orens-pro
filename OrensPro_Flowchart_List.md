# Alur Kerja & Flowchart Aplikasi - Orens Pro

Sistem Absensi Ekstrakurikuler Cerdas Terverifikasi (Geofenced & Dynamic Rotating QR Code)
Sekolah: SMK & SMA Prestasi Prima, Jakarta
Versi: 2.1 (Ekskul Context)
Tanggal Diperbarui: 18 Juni 2026

---

## 🗺️ 1. Gambaran Umum Peran Pengguna (RBAC Matrix)

Sistem Orens Pro menerapkan kontrol akses berbasis peran (Role-Based Access Control / RBAC) yang terdistribusi secara bertingkat:

| Peran Pengguna | Ruang Lingkup Akses | Fitur Utama |
| :--- | :--- | :--- |
| **Superadmin** | Sistem Global (Multi-Sekolah/Ekskul) | Mengelola data seluruh ekskul, divisi, audit logs global, reset nilai, dan manajemen akun pembina. |
| **Pembina** | Spesifik Satu Ekskul (Induk) | Mengelola divisi ekskulnya, mendaftarkan/mengimpor data siswa, reset nilai keaktifan semesteran, membuat sesi, dan mengunduh laporan komulatif multi-sesi. |
| **Pengurus** | Spesifik Divisi di dalam Ekskul | Membuat sesi pertemuan divisi, mengatur koordinat & radius geofencing, menampilkan layar QR dinamis, dan marking absensi manual. |
| **Member** | Personal (Siswa) | Melakukan check-in mandiri dengan memindai kode QR dinamis di basecamp ekskul dan melihat riwayat kehadiran pribadi. |

---

## 📋 2. Flowchart Alur Utama Aplikasi

### 2.1. Flowchart 1: Alur Autentikasi & Pengalihan Dashboard
Membatasi hak login hanya untuk pengguna dengan email resmi sekolah, lalu mengarahkan mereka ke dashboard khusus peran masing-masing.

```mermaid
flowchart TD
    Start([Mulai]) --> LoginRequest[Siswa/Guru membuka Halaman Login]
    LoginRequest --> InputData[Input Email & Password]
    InputData --> CheckDomain{Domain Email Valid?\n@smkprestasiprima.sch.id\n/ @smaprestasiprima.sch.id}
    
    CheckDomain -- Tidak --> ErrorDomain[Tampilkan Error: 'Email harus menggunakan domain sekolah']
    ErrorDomain --> InputData
    
    CheckDomain -- Ya --> Authenticate{Kredensial Valid & \nAkun Aktif?}
    
    Authenticate -- Tidak --> ErrorAuth[Tampilkan Error: 'Email atau password salah']
    ErrorAuth --> InputData
    
    Authenticate -- Ya --> CheckRole{Identifikasi Role}
    
    CheckRole -->|superadmin| RedirectSuper[Redirect ke Dashboard Superadmin]
    CheckRole -->|pembina| RedirectPembina[Redirect ke Dashboard Pembina]
    CheckRole -->|pengurus| RedirectPengurus[Redirect ke Dashboard Pengurus]
    CheckRole -->|member| RedirectMember[Redirect ke Dashboard Member]
    
    RedirectSuper --> End([Selesai])
    RedirectPembina --> End
    RedirectPengurus --> End
    RedirectMember --> End
```

---

### 2.2. Flowchart 2: Alur Pembuatan Sesi & Generator QR Dinamis
Proses pembuatan sesi latihan/pertemuan ekskul dan penayangan kode QR terenkripsi yang berotasi otomatis setiap 30 detik.

```mermaid
flowchart TD
    Start([Mulai]) --> CreateSession[Pembina/Pengurus klik Sesi Baru]
    CreateSession --> FillForm[Isi Form: Judul, Tanggal, Jam Mulai/Selesai, Koordinat GPS Basecamp, & Radius Geofence]
    FillForm --> SaveSession[Simpan Sesi ke Database]
    SaveSession --> GenerateToken[Sistem otomatis membuat qr_token statis sebagai Secret Key]
    GenerateToken --> DisplayQR[Pengurus membuka halaman QR: /sessions/id/qr]
    
    subgraph Looping_Rotasi_QR [Rotasi QR Otomatis - Setiap 30 Detik]
        GetTime[Ambil UNIX Timestamp Server saat ini] --> CalcWindow[Hitung Window: Window = floorTime / 30]
        CalcWindow --> GenerateHMAC[Generate Token HMAC: HMAC-SHA256Secret, Window]
        GenerateHMAC --> UpdateImage[Render QR Code baru dengan Token HMAC di Layar]
    end
    
    DisplayQR --> GetTime
    UpdateImage --> End([Sesi Siap Dipindai Member])
```

---

### 2.3. Flowchart 3: Alur Presensi Mandiri & Validasi Keamanan (Server-Side)
Verifikasi berlapis untuk memastikan siswa berada di lokasi basecamp ekskul dan tidak menggunakan screenshot QR hasil titip absen.

```mermaid
flowchart TD
    Start([Mulai]) --> OpenScanner[Member buka Dashboard HP -> Klik Scan Kehadiran]
    OpenScanner --> CheckPermission[Berikan Izin Kamera & Lokasi GPS]
    CheckPermission --> ScanQR[Pindai Kode QR Dinamis di Layar]
    ScanQR --> GetGPS[Ambil Koordinat GPS HP Member]
    GetGPS --> PostCheckin[Kirim Token QR & GPS Member ke Server via POST]
    
    subgraph Validasi_Server [Validasi Keamanan di Server]
        CheckActive{1. Sesi Masih Aktif\n& Waktu Valid?}
        CheckActive -- Tidak --> ErrActive[Gagal: Sesi tidak aktif / berakhir]
        
        CheckActive -- Ya --> CheckToken{2. Token QR Cocok dengan\nHMAC Server Window\nsaat ini / Window - 1?}
        CheckToken -- Tidak --> ErrToken[Gagal: Kode QR kedaluwarsa / tidak valid]
        
        CheckToken -- Ya --> CheckGeofence{3. Jarak GPS HP ke Basecamp\n<= Radius Toleransi Sesi?\nFormula Haversine}
        CheckGeofence -- Tidak --> ErrGeofence[Gagal: Berada di luar radius absensi]
    end
    
    ErrActive --> LogFail[Catat Hasil Gagal di attendance_logs]
    ErrToken --> LogFail
    ErrGeofence --> LogFail
    LogFail --> ShowError[Tampilkan Notifikasi Merah Gagal]
    
    CheckGeofence -- Ya --> SaveAttendance[Simpan Status 'hadir' di tabel attendances]
    SaveAttendance --> LogSuccess[Catat Hasil Sukses di attendance_logs]
    LogSuccess --> ShowSuccess[Tampilkan Notifikasi Hijau Sukses]
    
    ShowError --> End([Selesai])
    ShowSuccess --> End
```

---

### 2.4. Flowchart 4: Mekanisme Tutup Sesi & Otomasi Status Alpha
Proses pemeliharaan status absensi terotomatisasi ketika sesi pertemuan berakhir untuk menutup peluang manipulasi data.

```mermaid
flowchart TD
    Start([Mulai]) --> TimeOut[Waktu saat ini melewati batas end_time sesi]
    TimeOut --> TriggerDeactivate[Dashboard dibuka / Cron memicu deactiveExpiredSessions]
    TriggerDeactivate --> SetInactive[Perbarui status sesi: is_active = false]
    SetInactive --> TriggerAlpha[Panggil fungsi fillAbsentMembersWithAlpha]
    
    subgraph Auto_Alpha_Process [Pengisian Status Alpha Retroaktif]
        GetMembers[Ambil daftar ID seluruh anggota ekskul/divisi terkait] --> GetAttended[Ambil ID anggota yang sudah check-in hadir/sakit/izin]
        GetAttended --> DiffMembers[Bandingkan selisih ID anggota yang tidak melakukan absensi]
        DiffMembers --> BulkInsert[Insert record absensi baru massal dengan status 'alpha']
    end
    
    TriggerAlpha --> GetMembers
    BulkInsert --> End([Selesai: Rekap Sesi Lengkap])
```

---

## 📈 3. Alur Rekapitulasi Laporan & Penilaian Keaktifan

Kehadiran siswa dihitung secara kumulatif untuk menentukan nilai akhir keaktifan ekstrakurikuler (Grade) per semester.

### 3.1. Tabel Konversi Nilai Akhir (Grade)
*   **Total Hadir &ge; 4 Sesi:** Grade **A** (Sangat Aktif)
*   **Total Hadir 2 s.d 3 Sesi:** Grade **B** (Aktif)
*   **Total Hadir < 2 Sesi:** Grade **-** (Kurang Aktif / Kurang Kehadiran)

### 3.2. Flowchart 5: Alur Rekapitulasi & Reset Nilai Keaktifan
```mermaid
flowchart TD
    Start([Mulai Semester Baru]) --> AttendedSessions[Siswa mengikuti rangkaian pertemuan ekskul]
    AttendedSessions --> QueryAttendance[Sistem menjumlahkan kehadiran status 'hadir' per siswa]
    QueryAttendance --> GenerateGrade[Sistem melakukan konversi total hadir menjadi Grade A / B / -]
    GenerateGrade --> ExportReport[Pembina mengunduh rekapitulasi Excel / PDF untuk raport]
    ExportReport --> ResetGrade[Pembina menekan tombol 'Reset Nilai Keaktifan' di akhir semester]
    ResetGrade --> UpdateResetTime[Update kolom last_grade_reset_at di tabel organisations]
    UpdateResetTime --> AuditLog[Catat aksi reset oleh pembina di audit_logs]
    AuditLog --> End([Mulai Semester Baru - Hanya Kehadiran setelah last_grade_reset_at yang dihitung])
```

---

## 💾 4. Kamus Data & Relasi Skema Database

Struktur tabel di database MySQL/MariaDB yang mendukung operasional sistem Orens Pro:

1.  **organisations (Ekstrakurikuler):** Tabel induk ekskul (contoh: *Orens Solution*).
2.  **divisions (Divisi):** Sub-bidang di bawah ekskul (contoh: *Cyber Security*). Relasi *1-to-many* dari `organisations`.
3.  **users (Pengguna):** Data akun dengan multi-role (`superadmin`, `pembina`, `pengurus`, `member`). Relasi ke `organisations` dan `divisions`.
4.  **attendance_sessions (Sesi Presensi):** Sesi absensi geofenced yang dibuat pengurus. Relasi ke `organisations`, `divisions`, dan `users` (pembuat).
5.  **attendances (Presensi Anggota):** Log status presensi utama (`hadir`, `sakit`, `izin`, `alpha`). Relasi ke `users` (member) dan `attendance_sessions`.
6.  **attendance_logs (Log Pemindaian):** Log audit percobaan scan QR untuk forensik jika terjadi kegagalan sistem.
7.  **audit_logs (Log Aktivitas Admin):** Log audit forensik dari setiap perubahan data yang dipicu oleh admin.
