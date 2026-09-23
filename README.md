# 🛒 Sistem Kasir Minimarket

Aplikasi manajemen kasir dan penjualan minimarket berbasis web yang dibangun menggunakan **Laravel** dan **Filament**.

Aplikasi ini dirancang untuk membantu kasir dalam melakukan transaksi penjualan serta membantu pengelola dalam memantau produk, transaksi, pendapatan, dan statistik penjualan melalui dashboard.

---

## ✨ Fitur Utama

### 📊 Dashboard

Dashboard menyediakan informasi ringkas mengenai kondisi penjualan minimarket, seperti:

* Total transaksi
* Total pendapatan
* Jumlah transaksi hari ini
* Grafik penjualan
* Produk terlaris

### 🛒 Transaksi Kasir

Kasir dapat melakukan proses transaksi melalui satu form transaksi yang mencakup:

* Data pelanggan
* Pemilihan produk
* Jumlah produk
* Harga produk
* Subtotal
* Total transaksi
* Pencatatan tanggal transaksi
* Pencatatan kasir yang melakukan transaksi

### 📦 Manajemen Produk

Pengelolaan data produk meliputi:

* Tambah produk
* Edit produk
* Hapus produk
* Harga produk
* Stok produk
* Pencarian produk

### 👤 Manajemen Pengguna

Sistem memiliki pengguna dengan hak akses berdasarkan role, seperti:

* **Admin**
* **Kasir**

Hak akses dapat digunakan untuk membedakan fitur yang dapat diakses oleh masing-masing pengguna.

### 📈 Statistik Penjualan

Sistem menyediakan informasi penjualan untuk membantu melihat performa minimarket, termasuk:

* Total transaksi
* Total pendapatan
* Transaksi harian
* Grafik penjualan
* Produk dengan jumlah penjualan tertinggi

---

## 🖥️ Teknologi yang Digunakan

| Teknologi    | Keterangan           |
| ------------ | -------------------- |
| PHP          | Bahasa pemrograman   |
| Laravel      | Framework backend    |
| Filament     | Admin panel dan UI   |
| MySQL        | Database             |
| Eloquent ORM | Pengelolaan database |
| Blade        | Template engine      |
| Tailwind CSS | Styling antarmuka    |
| Heroicons    | Ikon antarmuka       |

---

## 🏗️ Arsitektur Data

Sistem menggunakan beberapa entitas utama yang saling berhubungan.

```text
User
 │
 │ 1
 ▼
Penjualan
 │
 │ 1
 ▼
DetailPenjualan
 │
 │ *
 ▼
Produk
```

### Relasi

```text
User
 └── hasMany → Penjualan

Penjualan
 ├── belongsTo → User
 └── hasMany → DetailPenjualan

DetailPenjualan
 ├── belongsTo → Penjualan
 └── belongsTo → Produk

Produk
 └── hasMany → DetailPenjualan
```

Struktur ini memungkinkan satu transaksi memiliki beberapa produk.

Contohnya:

```text
Transaksi #1001

├── Indomie
│   ├── Jumlah: 2
│   └── Subtotal: Rp 6.000
│
├── Aqua
│   ├── Jumlah: 3
│   └── Subtotal: Rp 9.000
│
└── Teh Botol
    ├── Jumlah: 1
    └── Subtotal: Rp 4.000

Total: Rp 19.000
```

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

## 📊 Dashboard

Dashboard aplikasi dirancang untuk memberikan gambaran kondisi penjualan secara cepat.

```text
┌────────────────────┐ ┌────────────────────┐ ┌────────────────────┐
│ Total Transaksi    │ │ Pendapatan         │ │ Transaksi Hari Ini │
│                    │ │                    │ │                    │
│ 1.250              │ │ Rp 25.500.000      │ │ 35                 │
└────────────────────┘ └────────────────────┘ └────────────────────┘


┌──────────────────────────────────────────────────────────────────┐
│                     Grafik Penjualan                             │
│                                                                  │
│             ╭──╮                                                 │
│        ╭────╯  ╰──╮                                              │
│   ╭────╯           ╰────╮                                        │
│ ──╯                     ╰────────                                │
│                                                                  │
└──────────────────────────────────────────────────────────────────┘


┌─────────────────────────────────────────────────────┐
│ Produk Terlaris                                     │
│                                                     │
│ 1. Indomie                         120 terjual       │
│ 2. Aqua                             95 terjual       │
│ 3. Teh Botol                        72 terjual       │
│ 4. Roti                             61 terjual       │
│ 5. Kopi                             48 terjual       │
└─────────────────────────────────────────────────────┘
```

> Data pada tampilan di atas merupakan contoh ilustrasi.

---

## 🔐 Role & Hak Akses

Sistem menggunakan role untuk membedakan akses pengguna.

### Admin

Admin dapat mengelola data utama sistem, seperti:

* Produk
* Pengguna
* Transaksi
* Dashboard
* Data penjualan

### Kasir

Kasir berfokus pada operasional transaksi:

* Melakukan transaksi
* Melihat data yang diperlukan untuk transaksi
* Melihat informasi penjualan sesuai hak akses

---

## ⚙️ Instalasi

### 1. Clone Repository

```bash
git clone <repository-url>
cd minimarket
```

### 2. Install Dependency

Install dependency PHP:

```bash
composer install
```

Jika project menggunakan dependency frontend:

```bash
npm install
```

### 3. Konfigurasi Environment

Salin file `.env.example`:

```bash
cp .env.example .env
```

Pada Windows PowerShell dapat menggunakan:

```powershell
Copy-Item .env.example .env
```

Kemudian konfigurasi database pada `.env`:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=minimarket
DB_USERNAME=root
DB_PASSWORD=
```

Sesuaikan konfigurasi dengan database lokal.

### 4. Generate Application Key

```bash
php artisan key:generate
```

### 5. Jalankan Migration

```bash
php artisan migrate
```

Jika project menyediakan seeder:

```bash
php artisan db:seed
```

Atau:

```bash
php artisan migrate --seed
```

### 6. Jalankan Frontend

```bash
npm run dev
```

### 7. Jalankan Laravel

Pada terminal lain:

```bash
php artisan serve
```

Aplikasi dapat diakses melalui:

```text
http://127.0.0.1:8000
```

Panel admin:

```text
http://127.0.0.1:8000/admin
```

---

## 🗃️ Database

Database utama menggunakan MySQL.

Tabel utama:

```text
users
   │
   ▼
penjualans
   │
   ▼
detail_penjualans
   │
   ▼
produks
```

### `users`

Menyimpan informasi pengguna sistem.

### `produks`

Menyimpan data produk yang dijual.

Contoh:

```text
id
nama
harga
stok
created_at
updated_at
```

### `penjualans`

Menyimpan informasi utama transaksi.

Contoh:

```text
id
user_id
nama_pelanggan
no_telepon
alamat
tanggal_penjualan
total_harga
created_at
updated_at
```

### `detail_penjualans`

Menyimpan detail produk yang terdapat dalam transaksi.

Contoh:

```text
id
penjualan_id
produk_id
jumlah
harga
subtotal
created_at
updated_at
```

---

## 🔄 Alur Transaksi

```text
Kasir Login
     │
     ▼
Dashboard
     │
     ▼
Transaksi Kasir
     │
     ▼
Pilih Produk
     │
     ▼
Tentukan Jumlah
     │
     ▼
Hitung Subtotal
     │
     ▼
Hitung Total
     │
     ▼
Simpan Transaksi
     │
     ├──────────────► penjualans
     │
     └──────────────► detail_penjualans
                              │
                              ▼
                           Produk
                              │
                              ▼
                         Stok diperbarui
```

---

## 🎯 Tujuan Project

Project ini dibuat sebagai aplikasi simulasi sistem kasir minimarket yang menerapkan konsep:

* CRUD
* Authentication
* Role-based access
* Relasi database
* Eloquent ORM
* Form transaksi
* Pengelolaan stok
* Dashboard
* Statistik penjualan
* Data visualization
* Admin panel menggunakan Filament

Project ini juga menjadi sarana untuk mempraktikkan pengembangan aplikasi web menggunakan Laravel dengan pendekatan yang terstruktur.

---

## 🚀 Pengembangan Selanjutnya

Beberapa fitur yang dapat dikembangkan:

* [ ] Cetak struk transaksi
* [ ] Export laporan penjualan ke PDF
* [ ] Export laporan ke Excel
* [ ] Filter laporan berdasarkan tanggal
* [ ] Notifikasi stok menipis
* [ ] Riwayat perubahan stok
* [ ] Barcode scanner
* [ ] Pencarian produk dengan barcode
* [ ] Diskon transaksi
* [ ] Pembayaran dan kembalian
* [ ] Laporan keuntungan
* [ ] Grafik penjualan berdasarkan periode
* [ ] Dashboard yang berbeda berdasarkan role

---

## 🛠️ Development

Project dikembangkan menggunakan:

```text
Laravel
+
Filament
+
MySQL
```

Laravel digunakan sebagai fondasi aplikasi dan pengelolaan backend, sedangkan Filament digunakan untuk membangun panel administrasi dan antarmuka pengelolaan data.

---

## 📄 License

Project ini dibuat untuk keperluan pembelajaran dan pengembangan aplikasi sistem kasir.

Laravel merupakan software open-source yang dilisensikan di bawah **MIT License**.
