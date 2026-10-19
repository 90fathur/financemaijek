<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ResetToFreshSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        $db->disableForeignKeyChecks();

        // 1. Kosongkan transaksi dan log investasi
        $db->table('transactions')->truncate();
        $db->table('investment_logs')->truncate();

        // 2. Kosongkan seluruh data investor dummy
        $db->table('investors')->truncate();

        // 3. Bersihkan akun user ber-role investor, pertahankan superadmin dan admin finance
        $db->query("DELETE FROM users WHERE role = 'investor' OR investor_id IS NOT NULL");

        // Pastikan akun Super Admin ('admin') tetap ada
        $adminCount = $db->table('users')->where('username', 'admin')->countAllResults();
        if ($adminCount === 0) {
            $db->table('users')->insert([
                'nama'        => 'Super Administrator Maijek',
                'username'    => 'admin',
                'email'       => 'admin@maijek.id',
                'password'    => password_hash('admin123', PASSWORD_BCRYPT),
                'role'        => 'superadmin',
                'investor_id' => null,
                'avatar'      => null,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        } else {
            $db->table('users')->where('username', 'admin')->update([
                'investor_id' => null,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        // Pastikan akun Staff Finance ('finance') tetap ada
        $financeCount = $db->table('users')->where('username', 'finance')->countAllResults();
        if ($financeCount === 0) {
            $db->table('users')->insert([
                'nama'        => 'Staff Finance & Accounting',
                'username'    => 'finance',
                'email'       => 'finance@maijek.id',
                'password'    => password_hash('finance123', PASSWORD_BCRYPT),
                'role'        => 'admin',
                'investor_id' => null,
                'avatar'      => null,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
        }

        // 4. Reset seluruh saldo kas dan rekening bank ke Rp 0.00
        $kasCount = $db->table('kas_accounts')->countAllResults();
        if ($kasCount === 0) {
            $defaultKas = [
                [
                    'id'             => 1,
                    'nama_akun'      => 'Bank BCA Bisnis Maijek',
                    'nomor_akun'     => '8219082311',
                    'saldo_awal'     => 0.00,
                    'saldo_saat_ini' => 0.00,
                    'keterangan'     => 'Rekening utama penampungan operasional',
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ],
                [
                    'id'             => 2,
                    'nama_akun'      => 'Bank Mandiri Operasional',
                    'nomor_akun'     => '152009812344',
                    'saldo_awal'     => 0.00,
                    'saldo_saat_ini' => 0.00,
                    'keterangan'     => 'Rekening pembayaran server, vendor & operasional',
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ],
                [
                    'id'             => 3,
                    'nama_akun'      => 'Kas Tunai / Petty Cash Kantor',
                    'nomor_akun'     => 'CASH-POLEWALI',
                    'saldo_awal'     => 0.00,
                    'saldo_saat_ini' => 0.00,
                    'keterangan'     => 'Kas kecil tunai untuk operasional harian kantor',
                    'created_at'     => date('Y-m-d H:i:s'),
                    'updated_at'     => date('Y-m-d H:i:s'),
                ],
            ];
            $db->table('kas_accounts')->insertBatch($defaultKas);
        } else {
            $db->table('kas_accounts')->update([
                'saldo_awal'     => 0.00,
                'saldo_saat_ini' => 0.00,
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);
        }

        // 5. Pastikan master kategori dasar tetap tersedia
        $categoryCount = $db->table('categories')->countAllResults();
        if ($categoryCount === 0) {
            $defaultCategories = [
                ['id' => 1, 'nama_kategori' => 'Bagi Hasil Driver (Ride & Delivery)', 'tipe' => 'pemasukan', 'kode_kategori' => 'IN-RIDE', 'icon' => 'bi-bicycle', 'warna' => '#10b981', 'deskripsi' => 'Fee transaksi ride & pengantaran barang', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 2, 'nama_kategori' => 'Komisi Penjualan Resto & Merchant (MaiFood)', 'tipe' => 'pemasukan', 'kode_kategori' => 'IN-FOOD', 'icon' => 'bi-shop', 'warna' => '#06b6d4', 'deskripsi' => 'Komisi pesanan makanan merchant rekanan', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 3, 'nama_kategori' => 'Biaya Layanan Platform & Jasa Titip', 'tipe' => 'pemasukan', 'kode_kategori' => 'IN-PLATFORM', 'icon' => 'bi-box-seam', 'warna' => '#3b82f6', 'deskripsi' => 'Biaya aplikasi per transaksi konsumen', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 4, 'nama_kategori' => 'Topup Fee Saldo Dompet Driver & Pelanggan', 'tipe' => 'pemasukan', 'kode_kategori' => 'IN-TOPUP', 'icon' => 'bi-wallet2', 'warna' => '#8b5cf6', 'deskripsi' => 'Biaya admin isi ulang saldo', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 5, 'nama_kategori' => 'Suntikan Modal Investasi Masuk', 'tipe' => 'pemasukan', 'kode_kategori' => 'IN-INVEST', 'icon' => 'bi-cash-coin', 'warna' => '#059669', 'deskripsi' => 'Penerimaan dana investasi baru dari investor', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 6, 'nama_kategori' => 'Cloud Server & Database Hosting', 'tipe' => 'pengeluaran', 'kode_kategori' => 'OUT-SERVER', 'icon' => 'bi-hdd-network', 'warna' => '#ef4444', 'deskripsi' => 'Biaya server backend dan database hosting', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 7, 'nama_kategori' => 'Google Maps Platform & Location API', 'tipe' => 'pengeluaran', 'kode_kategori' => 'OUT-GMAPS', 'icon' => 'bi-geo-alt', 'warna' => '#f97316', 'deskripsi' => 'Biaya API maps, directions, dan tracking driver', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 8, 'nama_kategori' => 'WhatsApp OTP Gateway & Notifikasi', 'tipe' => 'pengeluaran', 'kode_kategori' => 'OUT-WA', 'icon' => 'bi-chat-dots', 'warna' => '#ec4899', 'deskripsi' => 'Biaya gateway OTP login WhatsApp', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 9, 'nama_kategori' => 'Gaji & Insentif Tim Dispatcher / CS', 'tipe' => 'pengeluaran', 'kode_kategori' => 'OUT-GAJI', 'icon' => 'bi-people', 'warna' => '#6366f1', 'deskripsi' => 'Gaji bulanan customer care dan operasional', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 10, 'nama_kategori' => 'Marketing, Jaket & Merchandise Mitra', 'tipe' => 'pengeluaran', 'kode_kategori' => 'OUT-MKTG', 'icon' => 'bi-gift', 'warna' => '#84cc16', 'deskripsi' => 'Pengadaan helm, jaket mitra, dan promosi', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 11, 'nama_kategori' => 'Pencairan Dividen / Bagi Hasil Investor', 'tipe' => 'pengeluaran', 'kode_kategori' => 'OUT-DIVIDEN', 'icon' => 'bi-coin', 'warna' => '#eab308', 'deskripsi' => 'Pembagian keuntungan bagi hasil investor', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
                ['id' => 12, 'nama_kategori' => 'Operasional Kantor & Biaya Umum', 'tipe' => 'pengeluaran', 'kode_kategori' => 'OUT-OPS', 'icon' => 'bi-building', 'warna' => '#64748b', 'deskripsi' => 'Biaya listrik, wifi, sewa basecamp & ATK', 'created_at' => date('Y-m-d H:i:s'), 'updated_at' => date('Y-m-d H:i:s')],
            ];
            $db->table('categories')->insertBatch($defaultCategories);
        }

        $db->enableForeignKeyChecks();

        // 6. Bersihkan file berkas sampel dummy di public/uploads/bukti/
        $uploadDir = FCPATH . 'uploads/bukti/';
        if (is_dir($uploadDir)) {
            $files = glob($uploadDir . 'bukti_*.*');
            if ($files) {
                foreach ($files as $file) {
                    if (is_file($file)) {
                        @unlink($file);
                    }
                }
            }
        }
    }
}
