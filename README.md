# 🛒 Sistem Kasir Minimarket (POS)

Aplikasi Point of Sale (POS) dan manajemen minimarket berbasis web yang dibangun dengan **Laravel 13** dan **Filament 5**.

Dirancang untuk mengoptimalkan operasional kasir, manajemen inventaris, dan pemantauan performa penjualan secara real-time melalui panel admin profesional.

---

## ✨ Fitur Utama

### 📊 Dashboard & Analitik
* Ringkasan performa penjualan (Total Transaksi, Pendapatan, Transaksi Hari Ini).
* Grafik tren penjualan interaktif.
* Widget produk terlaris.

### 🛒 Point of Sale (POS) & Transaksi
* Form transaksi terpadu untuk pencatatan penjualan.
* Pencatatan data pelanggan, keranjang produk, subtotal, dan total otomatis.
* Validasi stok dan pembaruan inventaris otomatis saat transaksi disimpan.

### 📦 Manajemen Produk
* Manajemen data produk (Tambah, Ubah, Hapus).
* Kontrol harga dan pemantauan stok real-time.
* Fitur pencarian produk cepat.

### 👤 Manajemen Pengguna & Hak Akses
* Autentikasi dan manajemen hak akses berbasis peran (**Admin** & **Kasir**).
* Pembatasan akses fitur sesuai dengan role pengguna.

---

## 🖥️ Teknologi yang Digunakan

| Komponen | Teknologi |
| :--- | :--- |
| **Backend Framework** | Laravel 13 |
| **Admin Panel & POS** | Filament 5 |
| **Bahasa Pemrograman** | PHP 8.3+ |
| **Database** | MySQL / SQLite |
| **ORM** | Eloquent ORM |
| **Frontend UI Engine** | Blade, Tailwind CSS 4, Vite |
| **Ekspor Data** | pxlrbt/filament-excel |

---

## 🏗️ Skema & Relasi Database

### Entitas Utama
* **User**: Pengguna sistem (Admin/Kasir).
* **Produk**: Katalog barang dagangan dan stok.
* **Penjualan**: Data header transaksi penjualan.
* **DetailPenjualan**: Item produk yang dibeli dalam suatu transaksi.

### Relasi
* `User` 1-to-Many `Penjualan`
* `Penjualan` Many-to-1 `User`
* `Penjualan` 1-to-Many `DetailPenjualan`
* `DetailPenjualan` Many-to-1 `Penjualan`
* `DetailPenjualan` Many-to-1 `Produk`
* `Produk` 1-to-Many `DetailPenjualan`

---

## 📁 Struktur Project

Struktur utama aplikasi:

```text
app/
├── Filament/
│   ├── Resources/
│   │   ├── PenjualanResource.php
│   │   └── ProdukResource.php
│   │
│   └── Widgets/
│       ├── StatistikPenjualan.php
│       ├── GrafikPenjualan.php
│       └── ProdukTerlaris.php
│
├── Models/
│   ├── User.php
│   ├── Penjualan.php
│   ├── DetailPenjualan.php
│   └── Produk.php
│
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php
```

---

## ⚙️ Panduan Instalasi

### 1. Clone Repository & Masuk Direktori
```bash
git clone <repository-url>
cd minimarket
```

### 2. Install Dependensi PHP & Node.js
```bash
composer install
npm install
```

### 3. Konfigurasi Environment
```bash
cp .env.example .env
php artisan key:generate
```
Sesuaikan konfigurasi database pada file `.env`:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=minimarket
DB_USERNAME=root
DB_PASSWORD=
```

### 4. Jalankan Migrasi & Seeder
```bash
php artisan migrate --seed
```

### 5. Jalankan Server Development
Di terminal pertama (Backend):
```bash
php artisan serve
```

Di terminal kedua (Frontend Vite):
```bash
npm run dev
```

Akses aplikasi melalui `http://127.0.0.1:8000` dan panel admin melalui `http://127.0.0.1:8000/admin`.

---

## 🚀 Rencana Pengembangan

- [ ] Cetak struk transaksi fisik (Thermal Printer)
- [ ] Ekspor laporan penjualan ke PDF / Excel
- [ ] Filter laporan berdasarkan rentang tanggal
- [ ] Integrasi Barcode Scanner
- [ ] Notifikasi batas minimum stok barang

---

## 📄 Lisensi

Project ini dirilis di bawah [MIT License](https://opensource.org/licenses/MIT).
