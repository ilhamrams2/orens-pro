# 🚀 Panduan Deployment OrensPro ke Shared Hosting (cPanel / DirectAdmin / Plesk)

Dokumen ini berisi panduan langkah demi langkah (*step-by-step*) untuk mengunggah dan mempublikasikan aplikasi **OrensPro** ke **Shared Hosting** (seperti Hostinger, Niagahoster, DomaiNesia, RumahWeb, Cpanel biasa, dll).

---

## 📋 Metode Deployment yang Digunakan

Pada Shared Hosting, terdapat 2 opsi struktur folder:
* **Metode 1 (Rekomendasi Utama - Single Directory Upload)**:
  Unggah seluruh folder proyek **OrensPro** langsung ke dalam folder `public_html` (atau folder domain utama Anda). Aturan `.htaccess` yang telah dikonfigurasi di root proyek akan secara otomatis dan aman mengarahkan semua *traffic* ke folder `public/` serta **memblokir akses publik ke file sensitif** (seperti `.env`, `.git`, `composer.json`, `storage/`, `vendor/`).

---

## 🛠️ Langkah 1: Persiapan File di Komputer Lokal

1. **Jalankan Optimasi Autoloader & Compile Asset**:
   Buka terminal di komputer Anda pada folder proyek `OrensPro` dan jalankan perintah berikut:
   ```bash
   composer dump-autoload -o
   npm run build
   ```

2. **Kompres Proyek menjadi File ZIP**:
   Pilih seluruh file dan folder di dalam proyek `OrensPro` (**termasuk folder `vendor`, `public/build`, `.htaccess`, `.env.example`**), lalu kompres menjadi file zip (misalnya: `OrensPro_SharedHosting.zip`).
   > *Catatan: Jangan ikutkan folder `.git` atau file `.env` lokal Anda.*

---

## 🌐 Langkah 2: Pembuatan Database MySQL di cPanel

1. Login ke dashboard **cPanel** hosting Anda.
2. Masuk ke menu **MySQL® Databases**.
3. Buat database baru, misalnya: `usernamecpanel_orenspro`.
4. Buat pengguna database baru (*MySQL User*), misalnya: `usernamecpanel_userpro` dan masukkan kata sandi yang kuat.
5. Tambahkan pengguna tersebut ke database baru dengan mencentang **ALL PRIVILEGES**.
6. Catat 3 informasi penting ini:
   * **Nama Database**: `usernamecpanel_orenspro`
   * **Username Database**: `usernamecpanel_userpro`
   * **Password Database**: `(password yang Anda buat)`

---

## 📁 Langkah 3: Upload & Ekstrak di cPanel File Manager

1. Di cPanel, buka menu **File Manager**.
2. Masuk ke direktori `public_html` (atau folder subdomain/domain tujuan).
3. Klik tombol **Upload** di bagian atas, lalu pilih file `OrensPro_SharedHosting.zip`.
4. Setelah proses unggah selesai (100%), kembali ke File Manager dan klik kanan pada file zip tersebut $\rightarrow$ pilih **Extract**.
5. Pastikan struktur file proyek dan folder `public/` serta file `.htaccess` berada di dalam `public_html`.

---

## ⚙️ Langkah 4: Konfigurasi File `.env` & APP_KEY

1. Di cPanel File Manager, pastikan centang opsi *"Show Hidden Files (dotfiles)"* pada pengaturan (icon gerigi kanan atas).
2. Salin file `.env.example` menjadi `.env` (atau edit file `.env` jika sudah ada).
3. Isi konfigurasi `.env` sesuai dengan hosting Anda:

   ```env
   APP_NAME="Orens Pro"
   APP_ENV=production
   APP_KEY=base64:QMdKc4bJ4KViAPalQJVHwhFAN5p4ajE18nCEC3WtPLc=
   APP_DEBUG=false
   APP_URL=https://domain-anda.com

   APP_TIMEZONE=Asia/Jakarta

   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=usernamecpanel_orenspro
   DB_USERNAME=usernamecpanel_userpro
   DB_PASSWORD=password_database_anda

   SESSION_DRIVER=file
   CACHE_STORE=file
   QUEUE_CONNECTION=sync
   ```

4. Simpan perubahan file `.env`.

---

## 🗄️ Langkah 5: Migrasi Database & Seeding

### Opsi A (Jika cPanel memiliki fitur Terminal SSH):
Buka menu **Terminal** di cPanel, lalu jalankan:
```bash
php artisan migrate:fresh --seed --force
```

### Opsi B (Jika tidak ada Terminal - via phpMyAdmin):
1. Buka menu **phpMyAdmin** di cPanel.
2. Pilih database `usernamecpanel_orenspro`.
3. Klik tab **Import** $\rightarrow$ Pilih file `database/schema.sql` (jika mengekspor SQL dump) atau jalankan migrasi lokal terlebih dahulu lalu export dari lokal ke cPanel.

---

## 🔗 Langkah 6: Mengaktifkan Storage Link & Caching via Browser

Karena Shared Hosting sering kali tidak menyediakan Terminal SSH untuk menjalankan `php artisan storage:link`, kami telah menyediakan script helper di `public/symlink_helper.php`.

1. Buka peramban (browser) Anda dan akses URL berikut:
   ```text
   https://domain-anda.com/symlink_helper.php?secret=OrensProSecret123!&cache_all=1
   ```
2. Anda akan melihat laporan sukses berupa:
   - `✔ SUCCESS: Symbol link public/storage -> storage/app/public berhasil dibuat!`
   - `✔ SUCCESS: Config, Route, & View pre-compiled for Production!`

3. ⚠️ **PENTING DARI SEGI KEAMANAN**:
   Setelah langkah ini berhasil, buka **File Manager** cPanel dan **HAPUS** file `public/symlink_helper.php` agar tidak bisa diakses oleh pihak yang tidak berwenang.

---

## 🔒 Langkah 7: Pengujian & Keamanan Tambahan

1. Akses nama domain Anda di browser: `https://domain-anda.com`.
2. Coba uji login dengan akun default:
   * **Superadmin**: `superadmin@smkprestasiprima.sch.id` (Password: `password`)
   * **Pembina**: `pembina1@smkprestasiprima.sch.id` (Password: `password`)
   * **Pengurus**: `game@smkprestasiprima.sch.id` (Password: `password`)
3. Uji coba proteksi file `.env` dengan mengakses `https://domain-anda.com/.env` di browser. Pastikan server menampilkan halaman **403 Forbidden** (Berhasil Terlindungi).

---

🎉 **Selamat! Aplikasi OrensPro telah berhasil di-hosting dan berjalan optimal di Shared Hosting.**
