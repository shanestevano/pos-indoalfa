# POS Indoalfa 🛒🏪
Sistem Point of Sale (POS) & Manajemen Retail Minimarket Modern berbasis Web.

---

## 📌 Deskripsi Program

**POS Indoalfa** adalah aplikasi sistem kasir dan manajemen ritel terintegrasi yang dirancang dengan alur kerja minimarket modern (seperti Indomaret / Alfamart). Aplikasi ini mencakup seluruh siklus operasional toko, mulai dari penerimaan barang di gudang, transaksi kasir di meja depan, hingga pelaporan dan analisis penjualan oleh manajer toko.

### ✨ Fitur Utama:
- 🛒 **Modul Kasir (Point of Sale)**:
  - Pencarian produk cepat dengan nama, SKU, atau scan barcode.
  - Keranjang belanja real-time berbasis Livewire.
  - Berbagai metode pembayaran (Tunai, QRIS, Transfer, Debit).
  - Perhitungan kembalian otomatis & cetak struk belanja.
  - Manajemen shift kasir (buka dan tutup kasir).
- 📦 **Modul Gudang (Inventory & Warehouse)**:
  - Pencatatan stok masuk via *Purchase Order* (PO) dari supplier.
  - Penyesuaian stok fisik (*Stock Adjustment*) untuk barang rusak/hilang.
  - Peringatan dini untuk stok produk yang menipis.
- 📊 **Modul Manajer Toko (Admin Dashboard)**:
  - Ringkasan omset dan grafik penjualan harian/bulanan secara real-time.
  - Analisis produk terlaris (*Top Selling Products*).
  - Manajemen katalog produk, kategori, unit/satuan, dan supplier.
  - Pengaturan informasi toko (nama gerai, alamat, NPWP, footer struk).
- ⚡ **Demo Quick-Login**:
  - Tombol akses cepat di halaman login untuk beralih role tanpa harus mengetik manual saat pengujian demo.

---

## 🔑 Akun & Kredensial Demo

Aplikasi ini sudah dilengkapi dengan akun bawaan (*seeder*) untuk menguji berbagai hak akses:

| Role / Peran | Nama Pengguna | Email Login | Kode Pegawai (NIK) | Password | Akses Halaman |
| :--- | :--- | :--- | :--- | :--- | :--- |
| **Admin / Manajer** | Head Store Manager | `admin@indomaret.co.id` | `ADM001` | `password` | `/admin` (Semua Modul) |
| **Kasir** | Siti Rahmawati | `kasir@indomaret.co.id` | `KSR001` | `password` | `/pos` (Transaksi Kasir) |
| **Staff Gudang** | Budi Santoso | `gudang@indomaret.co.id` | `GDG001` | `password` | `/warehouse` (Manajemen Stok) |

> [!TIP]
> Di halaman `/login`, Anda juga bisa menggunakan fitur **Quick Login** dengan sekali klik pada role yang diinginkan untuk mempermudah demonstrasi.

---

## 🛠️ Tech Stack

- **Backend Framework**: [Laravel](https://laravel.com/) (PHP 8.4)
- **Frontend Interactivity**: [Livewire](https://livewire.laravel.com/)
- **Styling**: [Tailwind CSS](https://tailwindcss.com/) (v4) & [Vite](https://vitejs.dev/)
- **Role & Permission**: [Spatie Laravel-Permission](https://spatie.be/docs/laravel-permission/)
- **Database**: MySQL / PostgreSQL / SQLite
- **Deployment & Container**: Docker + [FrankenPHP](https://frankenphp.dev/) (Ready for Railway & Render)

---

## 🚀 Panduan Menjalankan di Lokal (Development)

1. **Clone Repository & Masuk ke Folder**:
   ```bash
   git clone https://github.com/shanestevano/pos-indoalfa.git
   cd pos-indoalfa
   ```

2. **Install Dependensi PHP & Node.js**:
   ```bash
   composer install
   npm install
   ```

3. **Salin File Environment & Generate Key**:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Konfigurasi Database di `.env` lalu Jalankan Migrasi & Seeder**:
   ```bash
   php artisan migrate --seed
   ```

5. **Jalankan Aplikasi**:
   ```bash
   npm run build
   php artisan serve
   ```
   Aplikasi dapat diakses melalui `http://127.0.0.1:8000`.

---

## ☁️ Deployment ke Cloud (Railway / Render)

Repositori ini sudah dilengkapi dengan:
- `Dockerfile` (*multi-stage build* untuk Vite asset & FrankenPHP runtime).
- `docker/entrypoint.sh` (otomatisasi caching, symbolic link, dan auto-migration).

Tinggal hubungkan repositori ini ke platform pilihan Anda:
- **Railway**: Pilih *New Project* > *Deploy from GitHub* > tambahkan database *MySQL* > atur *Environment Variables* (`APP_KEY`, dll.).
- **Render**: Pilih *New Web Service* (Docker Runtime) dan hubungkan database PostgreSQL.
