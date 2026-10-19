# Maijek Finance - Monitoring Keuangan & Investasi Dinamis

Sistem informasi dan monitoring keuangan, arus kas operasional, dan portofolio bagi hasil investor untuk ekosistem **Maijek Transportasi Nusantara**.

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777bb4?style=flat-square&logo=php)
![CodeIgniter](https://img.shields.io/badge/CodeIgniter-4.7.5-ef4223?style=flat-square&logo=codeigniter)
![Bootstrap](https://img.shields.io/badge/Bootstrap-5.3.3-7952b3?style=flat-square&logo=bootstrap)
![ApexCharts](https://img.shields.io/badge/ApexCharts-3.45-00e396?style=flat-square)
![License](https://img.shields.io/badge/License-MIT-blue?style=flat-square)

---

## 📌 Fitur Utama

1. **Dashboard Eksekutif & KPI Real-Time**
   - 6 Kartu ringkasan metrik: Modal Investasi, Saldo Kas & Bank, Pemasukan, Pengeluaran, Laba Bersih Kumulatif, dan Total Dividen.
   - Grafik interaktif ApexCharts: Tren Arus Kas Bulanan, Proporsi Kepemilikan Investor, dan Rincian Pengeluaran per Kategori.
   - Shortcut aksi cepat: Catat Mutasi, Suntik Modal, dan Pembagian Dividen.

2. **Manajemen Investor & Pembagian Dividen**
   - Master data investor lengkap (kontak, rekening bank, persentase bagi hasil / share).
   - **Suntik Modal Baru**: Menambah modal investor dan otomatis mencatat mutasi pemasukan kas.
   - **Distribusi Dividen Otomatis**: Alokasi dana dividen secara proporsional sesuai persentase share masing-masing investor, pemotongan kas, dan pencatatan riwayat transaksi.
   - Halaman detail investor beserta riwayat pencairan dividen & ROI realized.

3. **Pencatatan Transaksi & Arus Kas**
   - Input transaksi pemasukan dan pengeluaran dengan kategori dinamis, akun rekening kas, dan unggah bukti transaksi.
   - Multi-filter transaksi berdasarkan tipe, kategori, rekening kas, investor, dan rentang tanggal.
   - Ekspor data transaksi ke format CSV / Excel.

4. **Kas & Rekening Bank Dinamis**
   - Pengelolaan multi-rekening (Kas Tunai, Rekening BCA, Rekening Mandiri, Rekening BNI, dll.).
   - Fitur **Recalculate Saldo** untuk rekonsiliasi dan sinkronisasi saldo otomatis berdasarkan riwayat transaksi riil.

5. **Kategori Keuangan Fleksibel**
   - Kategori pemasukan dan pengeluaran dapat disesuaikan (kode unik, warna badge hex, dan ikon Bootstrap Icons).

6. **Laporan Laba Rugi & Siap Cetak**
   - Laporan laba rugi komprehensif berdasarkan rentang tanggal.
   - Format cetak (*print-friendly view*) bersih siap dicetak atau diekspor ke PDF.

7. **Multi-Role & Autentikasi**
   - **Super Admin**: Akses menyeluruh ke semua modul.
   - **Staff Finance**: Akses operasional keuangan dan pencatatan transaksi.
   - **Portal Investor**: Transparansi khusus portofolio modal dan riwayat dividen bagi investor.

---

## 🚀 Panduan Instalasi Lokal

### 1. Prasyarat Sistem
- PHP >= 8.2 (ekstensi `intl`, `mbstring`, `mysqli`, `curl` aktif)
- MySQL / MariaDB (via Laragon / XAMPP)
- Composer 2.x

### 2. Kloning Repository
```bash
git clone https://github.com/90fathur/financemaijek.git
cd financemaijek
```

### 3. Install Dependensi (Jika Diperlukan)
```bash
composer install
```

### 4. Konfigurasi Lingkungan (.env)
Salin berkas `env` menjadi `.env` lalu sesuaikan konfigurasi basis data Anda:
```env
CI_ENVIRONMENT = development

app.baseURL = 'http://localhost/monitoringmaijek/public/'
app.indexPage = ''

database.default.hostname = localhost
database.default.database = monitoring_maijek
database.default.username = root
database.default.password = 
database.default.DBDriver = MySQLi
database.default.port = 3306
```

### 5. Jalankan Migrasi & Data Bersih
```bash
# Jalankan migrasi tabel
php spark migrate

# Jalankan seeder akun & master kategori dasar
php spark db:seed ResetToFreshSeeder
```

### 6. Akses Aplikasi
Buka browser dan arahkan ke:
`http://localhost/monitoringmaijek/public/auth/login`

**Akun Login Default:**
- **Super Admin**: `admin` / `admin123`
- **Staff Finance**: `finance` / `finance123`

---

## 📁 Struktur Direktori Utama
```
monitoringmaijek/
├── app/
│   ├── Config/          # Konfigurasi aplikasi & rute (Routes.php)
│   ├── Controllers/     # Kontroller (Auth, Dashboard, Investor, Transaksi, dll)
│   ├── Database/
│   │   ├── Migrations/  # Skema struktur tabel
│   │   └── Seeds/       # Seeder inisialisasi data
│   ├── Helpers/         # Helper format rupiah & tanggal
│   ├── Models/          # Model ORM data
│   └── Views/           # Tampilan antarmuka & template
├── public/              # Web root (index.php, aset, unggahan)
└── writable/            # Log & cache sementara
```

---

&copy; 2026 PT Maijek Transportasi Nusantara. All rights reserved.
