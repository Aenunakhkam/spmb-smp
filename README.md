# Sistem Penerimaan Peserta Didik Baru (SPMB) SMP

SPMB (Sistem Penerimaan Mahasiswa/Murid Baru) adalah sebuah aplikasi berbasis web yang dirancang khusus untuk mempermudah proses pendaftaran, verifikasi, seleksi, hingga pelaporan calon siswa baru di tingkat Sekolah Menengah Pertama (SMP). Aplikasi ini dibangun dengan teknologi modern, cepat, dan antarmuka yang sangat ramah pengguna (user-friendly).

## 🚀 Teknologi yang Digunakan

Aplikasi ini dikembangkan menggunakan *stack* teknologi yang modern dan kuat:

- **Backend:** [Laravel 11](https://laravel.com/) (PHP Framework)
- **Frontend:** [Vue.js 3](https://vuejs.org/) (Composition API) via [Inertia.js](https://inertiajs.com/)
- **UI Framework:** [Vuetify 3](https://vuetifyjs.com/) & [Tailwind CSS](https://tailwindcss.com/)
- **Database:** MySQL
- **Eksport Laporan:** DomPDF (Untuk Cetak PDF) & Maatwebsite Excel (Untuk Cetak Excel)

---

## 🛠️ Panduan Instalasi Lokal

Ikuti langkah-langkah di bawah ini untuk menginstal dan menjalankan proyek SPMB secara lokal di komputer Anda (seperti menggunakan Laragon, XAMPP, atau Laravel Valet).

### 1. Persyaratan Sistem
Pastikan komputer Anda sudah terpasang perangkat lunak berikut:
- **PHP** versi 8.2 atau yang lebih baru.
- **Composer** (untuk manajemen dependensi PHP).
- **Node.js** (versi 18+) dan **NPM** (untuk *build* aset *frontend* Vue.js).
- **MySQL** (atau MariaDB).
- **Git** (untuk kloning repositori).

### 2. Kloning Repositori
Buka terminal/CMD Anda, arahkan ke folder web server (seperti `C:\laragon\www` atau `C:\xampp\htdocs`), lalu jalankan:

```bash
git clone git@github.com:Aenunakhkam/spmb-smp.git
cd spmb-smp
```

### 3. Instalasi Dependensi
Instal seluruh paket (library) pendukung untuk *backend* PHP dan *frontend* JavaScript:

```bash
# Instal dependensi backend (Laravel)
composer install

# Instal dependensi frontend (Vue/Inertia)
npm install
```

### 4. Konfigurasi Lingkungan (.env)
Buat file konfigurasi `.env` dengan menyalin template yang sudah disediakan:

```bash
cp .env.example .env
```
*(Bagi pengguna Windows, jika menggunakan CMD ketik: `copy .env.example .env`)*

Selanjutnya, buka file `.env` di teks editor Anda, lalu sesuaikan bagian konfigurasi database. Pastikan Anda sudah membuat database kosong (misal bernama `spmb`) di aplikasi seperti HeidiSQL, phpMyAdmin, dsb:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=spmb       # Nama database Anda
DB_USERNAME=root       # Username database Anda
DB_PASSWORD=           # Password database Anda (kosongkan jika tidak ada)
```

### 5. Konfigurasi Kunci Aplikasi & Database
Jalankan perintah berikut secara berurutan untuk men-*generate* kunci aplikasi, menautkan penyimpanan file (foto/dokumen), dan membangun tabel-tabel di database sekaligus mengisi data *dummy* awal (seperti Akun Admin *default*).

```bash
# Men-generate APP_KEY
php artisan key:generate

# Menautkan folder penyimpanan (storage) agar gambar dapat diakses
php artisan storage:link

# Melakukan migrasi database dan mengisi data awal (Seeder)
php artisan migrate --seed
```

### 6. Menjalankan Aplikasi
Aplikasi membutuhkan dua server yang berjalan berbarengan saat fase pengembangan (*development*). Buka **2 Terminal yang berbeda**, dan jalankan perintah berikut:

**Terminal 1 (Menjalankan Frontend Vue.js):**
```bash
npm run dev
```

**Terminal 2 (Menjalankan Backend Laravel):**
```bash
php artisan serve
```

Aplikasi sekarang sudah berjalan! Silakan buka browser Anda dan akses:
👉 **[http://localhost:8000](http://localhost:8000)**

---

## 🔒 Akses Default Akun Admin
Jika Anda telah menjalankan perintah `--seed` pada tahap instalasi di atas, Anda dapat mengakses dashboard admin menggunakan kredensial berikut:

- **URL Login Admin:** `http://localhost:8000/login`
- **Email:** `admin@spmb.com`
- **Password:** `password`

> **Catatan:** Setelah berhasil masuk (login), kami sangat menyarankan untuk segera menuju menu "Kelola Akun Admin" dan mengubah *password* *default* tersebut demi keamanan sistem Anda.

---

## 🏗️ Mengemas ke Production (Build & Hosting)

Apabila Anda hendak mengunggah (hosting) sistem ini ke server nyata (production) seperti cPanel atau Shared Hosting, ikuti langkah berikut:

### 1. Build Aset Frontend (Lokal)
Sebelum di-upload, pastikan Anda melakukan *build* terhadap file Vue.js di komputer lokal Anda agar file menjadi statis dan ukurannya optimal:
```bash
npm run build
```
*(Proses ini akan menghasilkan folder `public/build`)*

### 2. Konfigurasi File .env untuk Production
Ubah nilai `.env` sesuai dengan *database* pada hosting Anda:
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_db_hosting
DB_USERNAME=user_db_hosting
DB_PASSWORD=password_db_hosting
```

### 3. Upload File ke Hosting (cPanel)
- Kompres (ZIP) seluruh folder proyek SPMB (Kecuali folder `node_modules` karena ukurannya besar dan tidak diperlukan di production).
- Buka **File Manager** di cPanel Anda.
- Jika ini domain utama, arahkan Document Root/Web Root ke folder `public/` milik aplikasi SPMB. Anda bisa meletakkan *source code* di `public_html` dan menyesuaikan struktur folder atau meletakkannya di luar `public_html` lalu membuat *symlink*.

### 4. Menjalankan Migrasi di Hosting
Jika Anda memiliki akses terminal (SSH) di cPanel, jalankan:
```bash
php artisan migrate --seed
php artisan storage:link
php artisan optimize:clear
```
*(Jika tidak ada akses terminal, ekspor database dari lokal via phpMyAdmin, lalu impor ke database hosting Anda)*

---
*Dikembangkan secara eksklusif untuk kemajuan pendidikan SMP Indonesia.*
