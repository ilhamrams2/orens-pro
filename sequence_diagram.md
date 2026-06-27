# Orens Pro - UML Sequence Diagrams

Dokumen ini berisi spesifikasi 6 diagram urutan (**Sequence Diagrams**) untuk alur utama aplikasi **Orens Pro**. Diagram ini dirancang berdasarkan alur kerja (activity/flowchart) sistem absensi eksternal pintar terverifikasi.

Anda dapat menyalin kode Mermaid di bawah ini ke editor Markdown pendukung atau [Mermaid Live Editor](https://mermaid.live) untuk visualisasi interaktif.

---

## 1. Sequence Diagram: Autentikasi & Pengalihan Dashboard (Login)

Menggambarkan alur autentikasi pengguna berdasarkan domain email sekolah dan pengalihan ke dashboard yang sesuai dengan peran (Role-Based Access Control).

```mermaid
sequenceDiagram
    autonumber
    actor Pengguna as Siswa / Guru (User)
    participant Browser as Browser (Client)
    participant Ctrl as AuthController
    participant DB as Database

    Pengguna->>Browser: Masukkan Email & Password
    Browser->>Ctrl: POST /login (email, password)
    
    rect rgb(240, 240, 240)
        Note over Ctrl: Validasi Domain Email
        alt Domain bukan @smkprestasiprima.sch.id / @smaprestasiprima.sch.id
            Ctrl-->>Browser: Kembalikan Error (Domain tidak valid)
            Browser-->>Pengguna: Tampilkan "Email harus menggunakan domain sekolah"
        else Domain Valid
            Ctrl->>DB: Query User (email)
            DB-->>Ctrl: Data User & Password Hash
            
            Note over Ctrl: Verifikasi Password & Status Akun
            alt Kredensial Salah / Akun Tidak Aktif
                Ctrl-->>Browser: Kembalikan Error (Kredensial salah)
                Browser-->>Pengguna: Tampilkan "Email atau password salah"
            else Kredensial Valid & Akun Aktif
                Ctrl->>Ctrl: Buat Session Pengguna
                
                alt Role = superadmin
                    Ctrl-->>Browser: Redirect ke /superadmin/dashboard
                else Role = pembina
                    Ctrl-->>Browser: Redirect ke /pembina/dashboard
                else Role = pengurus
                    Ctrl-->>Browser: Redirect ke /pengurus/dashboard
                else Role = member
                    Ctrl-->>Browser: Redirect ke /member/dashboard
                end
                
                Browser-->>Pengguna: Tampilkan Halaman Dashboard sesuai Role
            end
        end
    end
```

---

## 2. Sequence Diagram: Pembuatan Sesi (Membuat Sesi)

Menggambarkan alur ketika Pembina atau Pengurus membuat sesi pertemuan ekskul baru lengkap dengan konfigurasi lokasi geofencing.

```mermaid
sequenceDiagram
    autonumber
    actor Admin as Pembina / Pengurus
    participant Browser as Browser (Client)
    participant Ctrl as AttendanceSessionController
    participant Policy as AttendanceSessionPolicy
    participant DB as Database

    Admin->>Browser: Klik "Sesi Baru" & Isi Form
    Note over Admin, Browser: Form: Judul, Tanggal, Jam, GPS Basecamp, Radius
    
    Browser->>Ctrl: POST /sessions (data_sesi)
    
    rect rgb(240, 240, 240)
        Ctrl->>Policy: authorize('create', AttendanceSession)
        Policy-->>Ctrl: true (Diizinkan)
        
        Note over Ctrl: Generate Static qr_token (Secret Key)
        Ctrl->>DB: Simpan Sesi Baru (dengan qr_token)
        DB-->>Ctrl: Berhasil (Session ID)
        
        Ctrl-->>Browser: Redirect ke /sessions/{id}/qr
        Browser-->>Admin: Tampilkan Halaman Penayangan QR Code
    end
```

---

## 3. Sequence Diagram: Generator QR Dinamis (QR Code)

Menggambarkan alur pembuatan token QR dinamis berbasis waktu (berotasi otomatis setiap 30 detik menggunakan rahasia `qr_token`).

```mermaid
sequenceDiagram
    autonumber
    actor Pengurus as Pengurus Ekskul
    participant Browser as Projector / Screen (Client)
    participant Ctrl as AttendanceSessionController
    participant DB as Database

    Pengurus->>Browser: Buka halaman /sessions/{id}/qr
    Browser->>Ctrl: GET /sessions/{id}/qr
    Ctrl->>DB: Ambil detail sesi & qr_token (Secret Key)
    DB-->>Ctrl: Data Sesi & qr_token
    Ctrl-->>Browser: Render halaman utama QR (menyimpan secret key secara aman di server-side)

    loop Setiap 30 Detik (Rotasi QR)
        Browser->>Ctrl: GET /sessions/{id}/token (Minta Token Baru)
        
        Note over Ctrl: Ambil UNIX Timestamp Server saat ini
        Note over Ctrl: Hitung Window = floor(Timestamp / 30)
        Note over Ctrl: Generate HMAC = HMAC-SHA256(qr_token, Window)
        
        Ctrl-->>Browser: Kembalikan Token HMAC Baru
        Note over Browser: Render QR Code baru di layar berdasarkan Token HMAC
        Browser-->>Pengurus: Tampilan QR Code diperbarui secara dinamis
    end
```

---

## 4. Sequence Diagram: Presensi Mandiri & Geofencing

Menggambarkan proses verifikasi berlapis di sisi server ketika anggota (Member) melakukan check-in menggunakan GPS dan memindai QR dinamis.

```mermaid
sequenceDiagram
    autonumber
    actor Member as Anggota (Member)
    participant Mobile as Smartphone (Browser/App)
    participant Ctrl as AttendanceController
    participant Service as AttendanceService
    participant DB as Database

    Member->>Mobile: Buka Halaman Presensi & Klik "Scan Kehadiran"
    Mobile->>Mobile: Minta Izin Kamera & Lokasi GPS
    Mobile-->>Member: Kamera Aktif
    
    Member->>Mobile: Pindai (Scan) QR Code Dinamis di Layar
    Mobile->>Mobile: Ambil Koordinat GPS HP (Latitude, Longitude)
    
    Mobile->>Ctrl: POST /attendances/checkin (scanned_token, lat, lon)
    
    rect rgb(240, 240, 240)
        Note over Ctrl, Service: Validasi Keamanan Server-Side
        Ctrl->>Service: processSelfCheckIn(user, session, scanned_token, lat, lon)
        
        %% 1. Validasi Waktu Sesi
        Service->>DB: Ambil Data Sesi (Waktu Mulai/Selesai, Koordinat Basecamp, Radius)
        DB-->>Service: Detail Sesi
        
        alt Sesi Tidak Aktif / Waktu Selesai Terlewati
            Service->>DB: Simpan ke attendance_logs (status: gagal, alasan: sesi tidak aktif)
            Service-->>Ctrl: Return Error ("Sesi tidak aktif")
            Ctrl-->>Mobile: Response Error
            Mobile-->>Member: Tampilkan Notifikasi Merah (Gagal)
        else Sesi Aktif
            %% 2. Validasi Token QR
            Note over Service: Hitung HMAC Window Saat Ini & Window - 1
            alt scanned_token tidak cocok dengan HMAC Window
                Service->>DB: Simpan ke attendance_logs (status: gagal, alasan: token kedaluwarsa)
                Service-->>Ctrl: Return Error ("Kode QR kedaluwarsa")
                Ctrl-->>Mobile: Response Error
                Mobile-->>Member: Tampilkan Notifikasi Merah (Gagal)
            else Token QR Valid
                %% 3. Validasi Geofencing
                Note over Service: Hitung Jarak Jarak GPS HP ke Basecamp (Formula Haversine)
                alt Jarak > Radius Toleransi Sesi
                    Service->>DB: Simpan ke attendance_logs (status: gagal, alasan: luar radius)
                    Service-->>Ctrl: Return Error ("Di luar radius absensi")
                    Ctrl-->>Mobile: Response Error
                    Mobile-->>Member: Tampilkan Notifikasi Merah (Gagal)
                else Jarak <= Radius (Valid)
                    Service->>DB: Simpan ke attendances (status: 'hadir')
                    Service->>DB: Simpan ke attendance_logs (status: sukses)
                    DB-->>Service: Konfirmasi Berhasil
                    
                    Service-->>Ctrl: Return Success
                    Ctrl-->>Mobile: Response Success (Status: 'hadir')
                    Mobile-->>Member: Tampilkan Notifikasi Hijau (Sukses Absen)
                end
            end
        end
    end
```

---

## 5. Sequence Diagram: Penutupan Sesi Otomatis (Otomasi Status Alpha)

Menggambarkan alur otomatisasi pengisian status 'alpha' secara retroaktif bagi siswa yang tidak melakukan check-in setelah sesi presensi berakhir.

```mermaid
sequenceDiagram
    autonumber
    participant System as System (Cron / Dashboard Trigger)
    participant Ctrl as AttendanceSessionController
    participant Session as AttendanceSession Model
    participant DB as Database

    System->>Ctrl: Trigger Pemeliharaan Sesi (Sesi kedaluwarsa)
    Ctrl->>Session: deactivateExpiredSessions()
    
    rect rgb(240, 240, 240)
        Session->>DB: UPDATE attendance_sessions SET is_active = false WHERE end_time < NOW()
        DB-->>Session: Konfirmasi Nonaktifkan Sesi
        
        Session->>Session: fillAbsentMembersWithAlpha(session_id)
        
        Session->>DB: Ambil semua ID Anggota Ekskul/Divisi terkait
        DB-->>Session: Daftar ID Anggota
        
        Session->>DB: Ambil ID Anggota yang sudah melakukan absensi (hadir/sakit/izin)
        DB-->>Session: Daftar ID Anggota yang Hadir
        
        Note over Session: Hitung Selisih (Daftar ID Anggota yang Absen)
        
        Session->>DB: Bulk INSERT ke attendances (user_id, session_id, status: 'alpha')
        DB-->>Session: Konfirmasi Batch Insert
    end
    
    Ctrl-->>System: Selesai (Rekap Sesi Terkunci & Lengkap)
```

---

## 6. Sequence Diagram: Rekapitulasi Laporan & Reset Nilai (Export Laporan)

Menggambarkan proses ekspor rekapitulasi kehadiran kumulatif menjadi nilai keaktifan (Grade A / B / -) serta mekanisme pembersihan (reset) di akhir semester.

```mermaid
sequenceDiagram
    autonumber
    actor Pembina as Pembina Ekskul
    participant Browser as Browser (Client)
    participant Ctrl as UserController / OrganisationController
    participant Export as MembersExport
    participant DB as Database
    participant Audit as AuditService

    %% --- BAGIAN 1: EKSPOR LAPORAN ---
    Note over Pembina, Browser: Bagian 1: Ekspor Laporan Keaktifan
    Pembina->>Browser: Klik "Export Laporan Keaktifan"
    Browser->>Ctrl: GET /users/export (Ekspor)
    Ctrl->>Export: new MembersExport(organisation_id)
    
    Export->>DB: Ambil total kehadiran 'hadir' setiap siswa sejak last_grade_reset_at
    DB-->>Export: Data Kehadiran per Siswa
    
    Note over Export: Konversi Kehadiran ke Grade:<br/>>= 4 Sesi -> Grade A<br/>2-3 Sesi -> Grade B<br/>< 2 Sesi -> Grade -
    
    Export-->>Ctrl: Data Rekapitulasi Matang
    Ctrl-->>Browser: Unduh File Excel / PDF Laporan
    Browser-->>Pembina: File Laporan Terunduh

    %% --- BAGIAN 2: RESET SEMESTER ---
    Note over Pembina, Browser: Bagian 2: Reset Nilai Keaktifan Semester
    Pembina->>Browser: Klik "Reset Nilai Keaktifan" di Halaman Organisasi
    Browser->>Ctrl: POST /organisations/reset-grade
    
    rect rgb(240, 240, 240)
        Ctrl->>DB: UPDATE organisations SET last_grade_reset_at = NOW()
        DB-->>Ctrl: Konfirmasi Update
        
        Ctrl->>Audit: log('reset_grades', organisation_model)
        Audit->>DB: INSERT INTO audit_logs (user_id, event, auditable_type, ...)
        DB-->>Audit: Konfirmasi Simpan Audit Log
        
        Ctrl-->>Browser: Response Sukses (Nilai berhasil di-reset)
        Browser-->>Pembina: Tampilkan "Nilai keaktifan berhasil disetel ulang untuk semester baru"
    end
```
