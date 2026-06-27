# Orens Pro - UML Class Diagram (Sederhana)

Dokumen ini berisi spesifikasi diagram kelas (Class Diagram) yang disederhanakan untuk aplikasi **Orens Pro**. Diagram ini berfokus pada entitas utama (Core Models) dan relasi penting di dalam sistem.

---

## 1. Kode Mermaid Class Diagram

```mermaid
classDiagram
    %% --- Relasi Antar Model Utama ---
    Organisation "1" --> "*" Division : Memiliki banyak Divisi
    Organisation "1" --> "*" User : Memiliki banyak Anggota
    Organisation "1" --> "*" AttendanceSession : Memiliki banyak Sesi Absensi
    Division "1" --> "*" User : Memiliki banyak Anggota
    Division "1" --> "*" AttendanceSession : Memiliki banyak Sesi Absensi
    User "1" --> "*" Attendance : Memiliki banyak Catatan Kehadiran
    AttendanceSession "1" --> "*" Attendance : Memiliki banyak Catatan Kehadiran

    %% --- Detail Kelas Utama ---
    class User {
        +int id
        +int organisation_id
        +int division_id
        +string name
        +string email
        +string role
        +boolean is_active
    }

    class Organisation {
        +int id
        +string name
        +boolean has_division
    }

    class Division {
        +int id
        +int organisation_id
        +string name
    }

    class AttendanceSession {
        +int id
        +int organisation_id
        +int division_id
        +string title
        +date session_date
        +time start_time
        +time end_time
        +decimal latitude
        +decimal longitude
        +int radius
        +boolean is_active
    }

    class Attendance {
        +int id
        +int user_id
        +int session_id
        +timestamp checkin_time
        +string status
    }
```

---

## 2. Penjelasan Singkat Entitas

Berikut adalah penjelasan singkat mengenai 5 entitas utama dalam sistem **Orens Pro**:

1. **Organisation (Organisasi)**:
   * Entitas tingkat teratas yang mewakili organisasi atau institusi.
   * Dapat memiliki beberapa divisi atau langsung memiliki anggota (User) jika tidak menggunakan divisi.

2. **Division (Divisi)**:
   * Entitas di bawah organisasi untuk mengelompokkan anggota berdasarkan divisi/departemen tertentu.

3. **User (Pengguna/Anggota)**:
   * Anggota dari organisasi yang memiliki peran tertentu (`superadmin`, `pembina`, `pengurus`, `member`).
   * Terikat pada satu organisasi dan satu divisi (opsional).

4. **AttendanceSession (Sesi Absensi)**:
   * Sesi presensi yang dibuat oleh pengurus/pembina untuk tanggal, waktu, dan lokasi koordinat (GPS) tertentu dengan radius toleransi tertentu.

5. **Attendance (Kehadiran)**:
   * Catatan kehadiran anggota pada sesi absensi tertentu, menyimpan waktu check-in serta status kehadiran (`hadir`, `terlambat`, `izin`, `sakit`, `alpha`).
