# Dashboard Eksekutif Pengelolaan Dana Organisasi Kepemudaan

Aplikasi web berbasis **PHP Native + MySQL + Bootstrap 5** untuk mengelola data organisasi kepemudaan, pengajuan dana, dan pencairan dana. Dibuat sebagai proyek tugas mahasiswa.

---

## Teknologi yang Digunakan

| Teknologi | Versi | Keterangan |
|-----------|-------|------------|
| PHP | 7.4+ / 8.x | Backend, tanpa framework |
| MySQL | 5.7+ / 8.x | Database |
| Bootstrap | 5.3 | UI Framework |
| Font Awesome | 6.5 | Icon |
| DataTables | 1.13 | Tabel interaktif |
| Chart.js | 4.4 | Grafik dashboard |
| SweetAlert2 | 11 | Dialog konfirmasi |
| jsPDF + AutoTable | 2.5 / 3.7 | Export PDF |
| jQuery | 3.7 | DOM & AJAX |

> Semua library di-load via CDN, tidak perlu instalasi tambahan.

---

## Prasyarat

Pastikan komputer sudah terinstal salah satu dari:

- **Laragon** (direkomendasikan) — sudah include Apache + MySQL + PHP
- **XAMPP** — Apache + MySQL + PHP
- **WAMP** — hanya Windows

---

## Cara Instalasi

### 1. Letakkan Folder Proyek

Salin folder `dashboardeksekutif` ke dalam direktori web server:

```
Laragon  →  C:\laragon\www\dashboardeksekutif\
XAMPP    →  C:\xampp\htdocs\dashboardeksekutif\
WAMP     →  C:\wamp64\www\dashboardeksekutif\
```

### 2. Jalankan Web Server

Buka **Laragon** (atau XAMPP/WAMP), lalu klik **Start All**.

Pastikan service **Apache** dan **MySQL** berstatus **Running**.

### 3. Buat Database

Buka browser, akses:

```
http://localhost/phpmyadmin
```

Kemudian:

1. Klik **New** di sidebar kiri
2. Beri nama database: `dashboard_eksekutif`
3. Klik **Create**
4. Pilih database `dashboard_eksekutif` yang baru dibuat
5. Klik tab **Import**
6. Klik **Choose File** → pilih file `database.sql` dari folder proyek
7. Klik tombol **Import** (atau **Go**)

Setelah berhasil, database akan berisi 4 tabel beserta data contoh.

### 4. Konfigurasi Koneksi Database

Buka file `config/database.php`, sesuaikan dengan konfigurasi MySQL Anda:

```php
define('DB_HOST', 'localhost');   // Host database
define('DB_NAME', 'dashboard_eksekutif'); // Nama database
define('DB_USER', 'root');        // Username MySQL (default: root)
define('DB_PASS', '');            // Password MySQL (default: kosong di Laragon/XAMPP)
```

> Jika Anda menggunakan password MySQL, isi `DB_PASS` dengan password Anda.

### 5. Buka Aplikasi

Akses aplikasi melalui browser:

```
http://localhost/dashboardeksekutif
```

Anda akan otomatis diarahkan ke halaman login.

---

## Akun Login

| Role | Username | Password | Hak Akses |
|------|----------|----------|-----------|
| **Admin** | `admin` | `password` | Full access (CRUD semua data + laporan) |
| **Pimpinan** | `pimpinan` | `password` | Read-only (dashboard + laporan) |

---

## Fitur Aplikasi

### Untuk Admin

| Fitur | Keterangan |
|-------|------------|
| Dashboard | Statistik ringkas + 2 grafik interaktif |
| Master Organisasi | Tambah, edit, hapus data organisasi |
| Pengajuan Dana | Kelola proposal dana (Menunggu / Disetujui / Ditolak) |
| Pencairan Dana | Catat realisasi pencairan dana yang disetujui |
| Laporan | Cetak & export PDF semua data |

### Untuk Pimpinan

| Fitur | Keterangan |
|-------|------------|
| Dashboard | Lihat statistik dan grafik |
| Laporan Organisasi | Lihat & export laporan organisasi |
| Laporan Pengajuan | Lihat & export laporan pengajuan |
| Laporan Pencairan | Lihat & export laporan pencairan |

---

## Panduan Penggunaan

### Login

1. Buka `http://localhost/dashboardeksekutif`
2. Masukkan **username** dan **password**
3. Klik tombol **Masuk**

![Login](assets/img/login-preview.png)

---

### Dashboard

Setelah login, halaman dashboard menampilkan:

- **4 kartu statistik** — Total Organisasi, Total Pengajuan, Dana Disetujui, Dana Dicairkan
- **Bar Chart** — Jumlah pengajuan dana per bulan (tahun berjalan)
- **Doughnut Chart** — Distribusi status pengajuan (Menunggu / Disetujui / Ditolak)
- **Tabel Terbaru** — 5 pengajuan dan pencairan terbaru

---

### Kelola Organisasi (Admin)

1. Klik menu **Organisasi** di sidebar
2. Untuk **menambah**: klik tombol **+ Tambah Organisasi** → isi form → klik **Simpan**
3. Untuk **mengedit**: klik ikon pensil (🖊) di kolom Aksi → ubah data → klik **Perbarui**
4. Untuk **menghapus**: klik ikon tempat sampah (🗑) → konfirmasi → klik **Ya, Hapus**

---

### Pengajuan Dana (Admin)

1. Klik menu **Pengajuan Dana** di sidebar
2. Nomor proposal di-generate otomatis (`PROP/YYYY/NNN`)
3. Pilih organisasi dari dropdown
4. Isi nama kegiatan dan dana yang diajukan
5. Atur **status**: `Menunggu`, `Disetujui`, atau `Ditolak`
6. Klik **Simpan**

> Hanya pengajuan berstatus **Disetujui** yang bisa dipilih saat membuat pencairan dana.

---

### Pencairan Dana (Admin)

1. Klik menu **Pencairan Dana** di sidebar
2. Klik **+ Tambah Pencairan**
3. Pilih **Nomor Proposal** dari dropdown (hanya yang berstatus Disetujui)
4. Nominal akan terisi otomatis sesuai dana yang diajukan (bisa diubah)
5. Isi keterangan jika perlu → klik **Simpan**

---

### Laporan

1. Klik salah satu menu laporan di sidebar:
   - **Lap. Organisasi**
   - **Lap. Pengajuan**
   - **Lap. Pencairan**
2. Gunakan **filter** (tahun, bulan, status) untuk menyaring data
3. Klik **Cetak** untuk mencetak via browser (Ctrl+P)
4. Klik **Export PDF** untuk mengunduh file PDF langsung

---

### Logout

- Klik nama pengguna di pojok kanan atas → pilih **Keluar**
- Atau klik **Keluar** di bagian bawah sidebar
- Konfirmasi dengan klik **Ya, Keluar**

---

## Struktur Folder

```
dashboardeksekutif/
│
├── assets/
│   └── css/
│       └── style.css          ← Stylesheet kustom
│
├── config/
│   ├── database.php           ← Konfigurasi & koneksi PDO
│   ├── session.php            ← Manajemen session & flash message
│   └── functions.php         ← Helper functions (formatRupiah, dll.)
│
├── controllers/
│   ├── AuthController.php    ← Login & logout
│   ├── OrganisasiController.php
│   ├── PengajuanController.php
│   └── PencairanController.php
│
├── models/
│   ├── OrganisasiModel.php
│   ├── PengajuanModel.php
│   └── PencairanModel.php
│
├── views/
│   ├── layouts/
│   │   ├── header.php        ← HTML head, load CSS
│   │   ├── sidebar.php       ← Navigasi sidebar
│   │   ├── navbar.php        ← Top navbar
│   │   └── footer.php        ← Load JS, tutup HTML
│   ├── auth/
│   │   └── login.php
│   ├── dashboard/
│   │   └── index.php         ← Dashboard + grafik
│   ├── organisasi/
│   │   └── index.php         ← CRUD organisasi
│   ├── pengajuan/
│   │   └── index.php         ← CRUD pengajuan dana
│   ├── pencairan/
│   │   └── index.php         ← CRUD pencairan dana
│   └── laporan/
│       ├── organisasi.php
│       ├── pengajuan.php
│       └── pencairan.php
│
├── uploads/                   ← Folder upload file (opsional)
├── index.php                  ← Entry point (auto-redirect)
├── logout.php                 ← Shortcut logout
├── database.sql               ← SQL schema + data awal
└── README.md                  ← Dokumentasi ini
```

---

## Struktur Database

```sql
users           → id, nama, username, password, role
organisasi      → id, nama_organisasi, ketua, alamat, no_hp, jumlah_anggota
pengajuan_dana  → id, organisasi_id, nomor_proposal, tanggal, nama_kegiatan,
                  dana_diajukan, status, catatan
pencairan_dana  → id, pengajuan_id, nomor_pencairan, tanggal, nominal, keterangan
```

---

## Keamanan yang Diterapkan

- Password di-hash menggunakan `password_hash()` (bcrypt)
- Verifikasi password dengan `password_verify()`
- Semua query menggunakan **PDO Prepared Statement** (mencegah SQL Injection)
- Akses halaman dilindungi session — otomatis redirect ke login jika belum masuk
- Hak akses berbasis role — halaman admin tidak bisa diakses pimpinan
- Input di-sanitasi dengan `htmlspecialchars()` sebelum ditampilkan
- Session ID di-regenerate setelah login sukses (`session_regenerate_id`)

---

## Troubleshooting

### Halaman tidak bisa diakses / redirect loop

Pastikan `BASE_URL` di `config/functions.php` sesuai nama folder:

```php
define('BASE_URL', '/dashboardeksekutif');
```

Jika nama folder Anda berbeda (misal `dashboard`), ubah menjadi `/dashboard`.

---

### Error koneksi database

```
Koneksi Database Gagal! ...
```

Periksa:
1. Apakah MySQL sudah berjalan?
2. Apakah nama database sudah `dashboard_eksekutif`?
3. Apakah username dan password di `config/database.php` benar?

---

### Halaman tampil tanpa style (CSS tidak load)

Pastikan `BASE_URL` sudah benar dan folder diletakkan langsung di dalam `www` / `htdocs`.

---

### Export PDF tidak berfungsi

Pastikan browser memiliki koneksi internet aktif, karena jsPDF di-load dari CDN.

---

## Informasi Proyek

| | |
|---|---|
| **Nama Proyek** | Dashboard Eksekutif Pengelolaan Dana Organisasi Kepemudaan |
| **Jenis** | Proyek Tugas Mahasiswa |
| **Mata Kuliah** | Sistem Informasi / Manajemen Basis Data |
| **Teknologi** | PHP Native, MySQL, Bootstrap 5 |
| **Versi** | 1.0.0 |

---

> Dibuat dengan PHP Native — tanpa framework Laravel atau CodeIgniter.
#   d a s h b o a r d e k s e k u t i f  
 