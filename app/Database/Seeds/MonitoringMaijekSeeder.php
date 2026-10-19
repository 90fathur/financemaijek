<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MonitoringMaijekSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();

        // 1. Clear existing records safely
        $db->disableForeignKeyChecks();
        $db->table('investment_logs')->truncate();
        $db->table('transactions')->truncate();
        $db->table('kas_accounts')->truncate();
        $db->table('categories')->truncate();
        $db->table('investors')->truncate();
        $db->table('users')->truncate();
        $db->table('system_settings')->truncate();
        $db->enableForeignKeyChecks();

        // 2. Generate Receipt Sample Images in public/uploads/bukti/
        $uploadDir = FCPATH . 'uploads/bukti/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        $receiptList = [
            'bukti_investor_bahar.jpg' => [
                'BUKTI TRANSFER DANA INVESTASI',
                'Pengirim: H. Baharuddin',
                'Penerima: Rekening Penampungan BNI Maijek',
                'Nominal: Rp 150.000.000',
                'Status: BERHASIL / DITERIMA',
            ],
            'bukti_investor_mandar.jpg' => [
                'PENYERAHAN MODAL VENTURE',
                'Pengirim: CV. Mandar Mitra Usaha',
                'Penerima: PT MAIJEK NUSANTARA',
                'Nominal: Rp 100.000.000',
                'Status: VERIFIKASI SELESAI',
            ],
            'bukti_server_cloud.jpg' => [
                'INVOICE CLOUD INFRASTRUCTURE',
                'Provider: DigitalOcean Droplets & DB',
                'Periode: September 2026',
                'Total Biaya: Rp 4.250.000',
                'Status: LUNAS OTOMATIS (CC)',
            ],
            'bukti_gmaps_api.jpg' => [
                'GOOGLE CLOUD PLATFORM INVOICE',
                'Service: Maps SDK, Places & Routes API',
                'Billing ID: GOOG-ID-882910',
                'Total Biaya: Rp 3.850.000',
                'Status: PAID IN FULL',
            ],
            'bukti_wa_gateway.jpg' => [
                'TAGIHAN WHATSAPP OTP GATEWAY',
                'Layanan: OTP Login & Order Dispatch',
                'Paket: Enterprise High Speed (100k msg)',
                'Total Biaya: Rp 1.500.000',
                'Status: LUNAS (BCA Transfer)',
            ],
            'bukti_bagi_hasil_driver.jpg' => [
                'REKAP SETORAN BAGI HASIL DRIVER',
                'Batch: Order Ride & Titip Barang',
                'Area: Polewali Mandar & Sekitarnya',
                'Total Penerimaan: Rp 18.750.000',
                'Status: SUDAH MASUK KAS BCA',
            ],
            'bukti_komisi_resto.jpg' => [
                'KOMISI PENJUALAN MERCHANT RESTO',
                'Batch: MaiFood Partner Settlement',
                'Total Rekanan: 48 Merchant Aktif',
                'Total Penerimaan: Rp 14.300.000',
                'Status: REKONSILIASI COCOK',
            ],
            'bukti_dividen_q3.jpg' => [
                'BUKTI TRANSFER DIVIDEN Q3 2026',
                'Investor: H. Baharuddin (35% Share)',
                'Nominal: Rp 14.700.000',
                'Bank Tujuan: BNI 0823910291',
                'Status: SUKSES DITRANSFER',
            ],
            'bukti_merchandise.jpg' => [
                'PENGADAAN JAKET & HELM DRIVER',
                'Vendor: CV. Konveksi Mandar Berkarya',
                'Item: 50 Set Jaket & Helm Standar SNI',
                'Total Biaya: Rp 8.500.000',
                'Status: LUNAS DIBAYAR KAS',
            ],
        ];

        foreach ($receiptList as $fileName => $details) {
            $width = 520;
            $height = 320;
            $img = imagecreatetruecolor($width, $height);
            $bg = imagecolorallocate($img, 248, 250, 252);
            imagefill($img, 0, 0, $bg);

            $topHeader = imagecolorallocate($img, 15, 23, 42); // slate-900
            imagefilledrectangle($img, 0, 0, $width, 58, $topHeader);

            $accentGreen = imagecolorallocate($img, 16, 185, 129); // emerald-500
            $border = imagecolorallocate($img, 226, 232, 240); // slate-200
            $darkText = imagecolorallocate($img, 30, 41, 59); // slate-800
            $mutedText = imagecolorallocate($img, 100, 116, 139); // slate-500
            $white = imagecolorallocate($img, 255, 255, 255);

            imagerectangle($img, 0, 0, $width - 1, $height - 1, $border);
            imagestring($img, 4, 20, 15, 'MAIJEK FINANCE MONITORING', $white);
            imagestring($img, 2, 20, 36, 'BUKTI RESMI TRANSAKSI KEUANGAN', $accentGreen);
            imagestring($img, 2, 400, 22, 'TERVALIDASI', $accentGreen);

            $yPos = 85;
            foreach ($details as $idx => $line) {
                if ($idx === 0) {
                    imagestring($img, 4, 25, $yPos, $line, $darkText);
                    $yPos += 28;
                } else {
                    $color = ($idx === count($details) - 1) ? $accentGreen : $mutedText;
                    imagestring($img, 3, 25, $yPos, $line, $color);
                    $yPos += 24;
                }
            }

            imagefilledrectangle($img, 20, $height - 35, $width - 20, $height - 34, $border);
            imagestring($img, 2, 25, $height - 24, 'ID Transaksi: TRX-VERIFIED-MAIJEK-INVESTASI-2026', $mutedText);

            imagejpeg($img, $uploadDir . $fileName, 90);
            imagedestroy($img);
        }

        // 3. Seed: Investors
        $investors = [
            [
                'kode_investor'         => 'INV-001',
                'nama_investor'         => 'H. Baharuddin',
                'email'                 => 'baharuddin@maijek.id',
                'no_telepon'            => '081234567890',
                'nama_bank'             => 'Bank BNI',
                'nomor_rekening'        => '0823910291',
                'pemilik_rekening'      => 'H. Baharuddin',
                'total_investasi'       => 150000000.00,
                'persentase_bagi_hasil' => 35.00,
                'tanggal_bergabung'     => '2026-01-10',
                'status'                => 'aktif',
                'keterangan'            => 'Investor Utama - Fokus ekspansi armada dan regional Polman & Majene',
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'kode_investor'         => 'INV-002',
                'nama_investor'         => 'CV. Mandar Mitra Usaha',
                'email'                 => 'mitra@mandarusaha.co.id',
                'no_telepon'            => '081344229988',
                'nama_bank'             => 'Bank Mandiri',
                'nomor_rekening'        => '1520023910',
                'pemilik_rekening'      => 'CV Mandar Mitra Usaha',
                'total_investasi'       => 100000000.00,
                'persentase_bagi_hasil' => 25.00,
                'tanggal_bergabung'     => '2026-02-15',
                'status'                => 'aktif',
                'keterangan'            => 'Mitra Korporasi - Dukungan modal operasional dan merchant partner',
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'kode_investor'         => 'INV-003',
                'nama_investor'         => 'Dr. Andi Sulfikar, M.Kes',
                'email'                 => 'sulfikar@gmail.com',
                'no_telepon'            => '082188776655',
                'nama_bank'             => 'Bank BCA',
                'nomor_rekening'        => '7820192831',
                'pemilik_rekening'      => 'Andi Sulfikar',
                'total_investasi'       => 80000000.00,
                'persentase_bagi_hasil' => 20.00,
                'tanggal_bergabung'     => '2026-03-01',
                'status'                => 'aktif',
                'keterangan'            => 'Investor Angel - Pendanaan sistem teknologi dan keamanan aplikasi',
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
            [
                'kode_investor'         => 'INV-004',
                'nama_investor'         => 'Hj. Nurhaeni K., S.E.',
                'email'                 => 'nurhaeni.k@yahoo.com',
                'no_telepon'            => '085299112233',
                'nama_bank'             => 'Bank BRI',
                'nomor_rekening'        => '491029381029',
                'pemilik_rekening'      => 'Nurhaeni K',
                'total_investasi'       => 70000000.00,
                'persentase_bagi_hasil' => 20.00,
                'tanggal_bergabung'     => '2026-04-20',
                'status'                => 'aktif',
                'keterangan'            => 'Investor Strategis - Program promosi dan voucher merchant MaiFood',
                'created_at'            => date('Y-m-d H:i:s'),
                'updated_at'            => date('Y-m-d H:i:s'),
            ],
        ];
        $db->table('investors')->insertBatch($investors);

        // 4. Seed: Users (Admin & Investor)
        $users = [
            [
                'nama'        => 'Super Administrator Maijek',
                'username'    => 'admin',
                'email'       => 'admin@maijek.id',
                'password'    => password_hash('admin123', PASSWORD_BCRYPT),
                'role'        => 'superadmin',
                'investor_id' => null,
                'avatar'      => null,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nama'        => 'Staff Finance & Accounting',
                'username'    => 'finance',
                'email'       => 'finance@maijek.id',
                'password'    => password_hash('finance123', PASSWORD_BCRYPT),
                'role'        => 'admin',
                'investor_id' => null,
                'avatar'      => null,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'nama'        => 'H. Baharuddin (Investor)',
                'username'    => 'investor_bahar',
                'email'       => 'baharuddin@maijek.id',
                'password'    => password_hash('investor123', PASSWORD_BCRYPT),
                'role'        => 'investor',
                'investor_id' => 1,
                'avatar'      => null,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
        ];
        $db->table('users')->insertBatch($users);

        // 5. Seed: Categories
        $categories = [
            // Pemasukan
            [
                'id'            => 1,
                'nama_kategori' => 'Bagi Hasil Driver (Ride & Delivery)',
                'tipe'          => 'pemasukan',
                'kode_kategori' => 'IN-RIDE',
                'icon'          => 'bi-bicycle',
                'warna'         => '#10b981', // emerald
                'deskripsi'     => 'Potongan fee 20% dari setiap trip order ride & kurir titip barang',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 2,
                'nama_kategori' => 'Komisi Penjualan Resto & Merchant (MaiFood)',
                'tipe'          => 'pemasukan',
                'kode_kategori' => 'IN-FOOD',
                'icon'          => 'bi-shop',
                'warna'         => '#06b6d4', // cyan
                'deskripsi'     => 'Komisi bagi hasil dari pesanan makanan resto partner',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 3,
                'nama_kategori' => 'Biaya Layanan Platform & Jasa Titip',
                'tipe'          => 'pemasukan',
                'kode_kategori' => 'IN-PLATFORM',
                'icon'          => 'bi-box-seam',
                'warna'         => '#3b82f6', // blue
                'deskripsi'     => 'Biaya aplikasi per transaksi dari konsumen',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 4,
                'nama_kategori' => 'Topup Fee Saldo Dompet Driver & Pelanggan',
                'tipe'          => 'pemasukan',
                'kode_kategori' => 'IN-TOPUP',
                'icon'          => 'bi-wallet2',
                'warna'         => '#8b5cf6', // purple
                'deskripsi'     => 'Biaya admin topup saldo melalui payment gateway atau loket',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 5,
                'nama_kategori' => 'Suntikan Modal Investasi Masuk',
                'tipe'          => 'pemasukan',
                'kode_kategori' => 'IN-INVEST',
                'icon'          => 'bi-cash-coin',
                'warna'         => '#059669', // dark emerald
                'deskripsi'     => 'Penerimaan dana investasi baru dari investor',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 6,
                'nama_kategori' => 'Iklan Merchant & Kerjasama Promo',
                'tipe'          => 'pemasukan',
                'kode_kategori' => 'IN-PROMO',
                'icon'          => 'bi-megaphone',
                'warna'         => '#f59e0b', // amber
                'deskripsi'     => 'Pemasangan banner promosi merchant di beranda aplikasi',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],

            // Pengeluaran
            [
                'id'            => 7,
                'nama_kategori' => 'Cloud Server & Database Hosting',
                'tipe'          => 'pengeluaran',
                'kode_kategori' => 'OUT-SERVER',
                'icon'          => 'bi-hdd-network',
                'warna'         => '#ef4444', // red
                'deskripsi'     => 'Biaya server backend API, redis cache, dan hosting database',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 8,
                'nama_kategori' => 'Google Maps Platform & Location API',
                'tipe'          => 'pengeluaran',
                'kode_kategori' => 'OUT-GMAPS',
                'icon'          => 'bi-geo-alt',
                'warna'         => '#f97316', // orange
                'deskripsi'     => 'Biaya rute, directions, dan geocoding live tracking driver',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 9,
                'nama_kategori' => 'WhatsApp OTP Gateway & Notifikasi',
                'tipe'          => 'pengeluaran',
                'kode_kategori' => 'OUT-WA',
                'icon'          => 'bi-chat-dots',
                'warna'         => '#ec4899', // pink
                'deskripsi'     => 'Biaya pengiriman kode OTP login driver/pelanggan via WhatsApp',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 10,
                'nama_kategori' => 'Gaji & Insentif Tim CS / Dispatcher',
                'tipe'          => 'pengeluaran',
                'kode_kategori' => 'OUT-GAJI',
                'icon'          => 'bi-people',
                'warna'         => '#6366f1', // indigo
                'deskripsi'     => 'Gaji bulanan customer care, admin finance, dan verifikator mitra',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 11,
                'nama_kategori' => 'Marketing, Jaket & Merchandise Mitra',
                'tipe'          => 'pengeluaran',
                'kode_kategori' => 'OUT-MKTG',
                'icon'          => 'bi-gift',
                'warna'         => '#84cc16', // lime
                'deskripsi'     => 'Pengadaan jaket/helm driver, stiker merchant, dan promosi offline',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 12,
                'nama_kategori' => 'Pencairan Dividen / Bagi Hasil Investor',
                'tipe'          => 'pengeluaran',
                'kode_kategori' => 'OUT-DIVIDEN',
                'icon'          => 'bi-coin',
                'warna'         => '#eab308', // yellow
                'deskripsi'     => 'Pembayaran dividen / return of investment kepada investor',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 13,
                'nama_kategori' => 'Operasional Kantor & Biaya Umum',
                'tipe'          => 'pengeluaran',
                'kode_kategori' => 'OUT-OPS',
                'icon'          => 'bi-building',
                'warna'         => '#64748b', // slate
                'deskripsi'     => 'Listrik, internet fiber, sewa basecamp, ATK & konsumsi rapat',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
            [
                'id'            => 14,
                'nama_kategori' => 'Maintenance Aplikasi & Bug Fixing',
                'tipe'          => 'pengeluaran',
                'kode_kategori' => 'OUT-MAINT',
                'icon'          => 'bi-tools',
                'warna'         => '#a855f7', // purple
                'deskripsi'     => 'Biaya lisensi development tools, audit security, dan rilis Google Play',
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ],
        ];
        $db->table('categories')->insertBatch($categories);

        // 6. Seed: Kas Accounts
        $kasAccounts = [
            [
                'id'             => 1,
                'nama_akun'      => 'Bank BCA Bisnis Maijek',
                'nomor_akun'     => '8219082311',
                'saldo_awal'     => 15000000.00,
                'saldo_saat_ini' => 15000000.00,
                'keterangan'     => 'Rekening utama penampungan transaksi driver dan merchant resto',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id'             => 2,
                'nama_akun'      => 'Bank Mandiri Operasional',
                'nomor_akun'     => '152009812344',
                'saldo_awal'     => 10000000.00,
                'saldo_saat_ini' => 10000000.00,
                'keterangan'     => 'Rekening khusus pembayaran server, API Google, gaji dan vendor',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id'             => 3,
                'nama_akun'      => 'Rekening Penampungan Investasi (BNI)',
                'nomor_akun'     => '0987123456',
                'saldo_awal'     => 400000000.00,
                'saldo_saat_ini' => 400000000.00,
                'keterangan'     => 'Rekening modal investasi awal & dana cadangan strategis',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
            [
                'id'             => 4,
                'nama_akun'      => 'Kas Tunai / Petty Cash Kantor',
                'nomor_akun'     => 'CASH-POLEWALI',
                'saldo_awal'     => 5000000.00,
                'saldo_saat_ini' => 5000000.00,
                'keterangan'     => 'Kas kecil tunai untuk keperluan darurat operasional kantor',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ],
        ];
        $db->table('kas_accounts')->insertBatch($kasAccounts);

        // 7. Seed: Transactions
        $transactions = [
            // Modal Investasi (Juli 2026)
            [
                'nomor_transaksi' => 'TRX-202607-0001',
                'tanggal'         => '2026-07-05',
                'tipe'            => 'pemasukan',
                'category_id'     => 5, // Modal Investasi
                'kas_account_id'  => 3, // BNI Investasi
                'investor_id'     => 1, // Baharuddin
                'jumlah'          => 150000000.00,
                'judul'           => 'Setoran Modal Investasi Utama H. Baharuddin',
                'keterangan'      => 'Suntikan modal fase 1 untuk ekspansi jaringan driver Polman & Majene',
                'bukti_transaksi' => 'bukti_investor_bahar.jpg',
                'nama_penginput'  => 'Super Administrator Maijek',
                'created_at'      => '2026-07-05 09:30:00',
                'updated_at'      => '2026-07-05 09:30:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202607-0002',
                'tanggal'         => '2026-07-08',
                'tipe'            => 'pemasukan',
                'category_id'     => 5,
                'kas_account_id'  => 3,
                'investor_id'     => 2, // Mandar Mitra Usaha
                'jumlah'          => 100000000.00,
                'judul'           => 'Penyertaan Modal CV. Mandar Mitra Usaha',
                'keterangan'      => 'Suntikan modal penyertaan usaha kemitraan Maijek',
                'bukti_transaksi' => 'bukti_investor_mandar.jpg',
                'nama_penginput'  => 'Super Administrator Maijek',
                'created_at'      => '2026-07-08 14:15:00',
                'updated_at'      => '2026-07-08 14:15:00',
            ],
            // Agustus 2026 Transactions
            [
                'nomor_transaksi' => 'TRX-202608-0001',
                'tanggal'         => '2026-08-01',
                'tipe'            => 'pengeluaran',
                'category_id'     => 7, // Server Cloud
                'kas_account_id'  => 2, // Mandiri
                'investor_id'     => null,
                'jumlah'          => 3950000.00,
                'judul'           => 'Pembayaran Server Cloud DigitalOcean & Redis (Agustus)',
                'keterangan'      => 'Server production backend Maijek dan replica database',
                'bukti_transaksi' => 'bukti_server_cloud.jpg',
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-08-01 10:00:00',
                'updated_at'      => '2026-08-01 10:00:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202608-0002',
                'tanggal'         => '2026-08-02',
                'tipe'            => 'pengeluaran',
                'category_id'     => 8, // Google Maps
                'kas_account_id'  => 2,
                'investor_id'     => null,
                'jumlah'          => 3200000.00,
                'judul'           => 'Tagihan Google Maps Platform API (Juli Billing)',
                'keterangan'      => 'API tracking lokasi rute driver dan estimasi ongkir Maijek',
                'bukti_transaksi' => 'bukti_gmaps_api.jpg',
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-08-02 11:20:00',
                'updated_at'      => '2026-08-02 11:20:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202608-0003',
                'tanggal'         => '2026-08-15',
                'tipe'            => 'pemasukan',
                'category_id'     => 1, // Bagi hasil driver
                'kas_account_id'  => 1, // BCA
                'investor_id'     => null,
                'jumlah'          => 22500000.00,
                'judul'           => 'Setoran Bagi Hasil Trip Driver Maijek (Agustus Batch 1)',
                'keterangan'      => 'Pemasukan komisi 20% total 1.420 orderan ride & kurir',
                'bukti_transaksi' => 'bukti_bagi_hasil_driver.jpg',
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-08-15 16:00:00',
                'updated_at'      => '2026-08-15 16:00:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202608-0004',
                'tanggal'         => '2026-08-18',
                'tipe'            => 'pemasukan',
                'category_id'     => 2, // Komisi Resto
                'kas_account_id'  => 1,
                'investor_id'     => null,
                'jumlah'          => 16800000.00,
                'judul'           => 'Komisi Merchant Restoran MaiFood (Agustus Batch 1)',
                'keterangan'      => 'Penerimaan komisi order makanan resto area Polman & Wonomulyo',
                'bukti_transaksi' => 'bukti_komisi_resto.jpg',
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-08-18 15:30:00',
                'updated_at'      => '2026-08-18 15:30:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202608-0005',
                'tanggal'         => '2026-08-25',
                'tipe'            => 'pengeluaran',
                'category_id'     => 10, // Gaji Tim
                'kas_account_id'  => 2,
                'investor_id'     => null,
                'jumlah'          => 12000000.00,
                'judul'           => 'Gaji & Operasional Tim CS & Dispatcher (Agustus)',
                'keterangan'      => 'Pembayaran 4 personil operasional kantor',
                'bukti_transaksi' => null,
                'nama_penginput'  => 'Super Administrator Maijek',
                'created_at'      => '2026-08-25 13:00:00',
                'updated_at'      => '2026-08-25 13:00:00',
            ],
            // September 2026 Transactions
            [
                'nomor_transaksi' => 'TRX-202609-0001',
                'tanggal'         => '2026-09-02',
                'tipe'            => 'pengeluaran',
                'category_id'     => 7, // Server Cloud
                'kas_account_id'  => 2,
                'investor_id'     => null,
                'jumlah'          => 4250000.00,
                'judul'           => 'Pembayaran Cloud Infrastructure Server (September)',
                'keterangan'      => 'Upgrade RAM & CPU Droplets untuk antisipasi lonjakan order',
                'bukti_transaksi' => 'bukti_server_cloud.jpg',
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-09-02 09:00:00',
                'updated_at'      => '2026-09-02 09:00:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202609-0002',
                'tanggal'         => '2026-09-03',
                'tipe'            => 'pengeluaran',
                'category_id'     => 8, // Google Maps
                'kas_account_id'  => 2,
                'investor_id'     => null,
                'jumlah'          => 3850000.00,
                'judul'           => 'Biaya Google Maps Platform & Routing API (Agustus Billing)',
                'keterangan'      => 'Biaya API rute pencarian navigasi driver',
                'bukti_transaksi' => 'bukti_gmaps_api.jpg',
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-09-03 10:45:00',
                'updated_at'      => '2026-09-03 10:45:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202609-0003',
                'tanggal'         => '2026-09-05',
                'tipe'            => 'pengeluaran',
                'category_id'     => 9, // WA Gateway
                'kas_account_id'  => 2,
                'investor_id'     => null,
                'jumlah'          => 1500000.00,
                'judul'           => 'Topup Kuota WhatsApp OTP Gateway (September)',
                'keterangan'      => 'Pengiriman pesan OTP dan konfirmasi pesanan',
                'bukti_transaksi' => 'bukti_wa_gateway.jpg',
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-09-05 14:00:00',
                'updated_at'      => '2026-09-05 14:00:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202609-0004',
                'tanggal'         => '2026-09-12',
                'tipe'            => 'pengeluaran',
                'category_id'     => 11, // Merchandise Jaket/Helm
                'kas_account_id'  => 1,
                'investor_id'     => null,
                'jumlah'          => 8500000.00,
                'judul'           => 'Pengadaan 50 Paket Jaket & Helm Driver Maijek',
                'keterangan'      => 'Standarisasi atribut pengemudi armada Maijek',
                'bukti_transaksi' => 'bukti_merchandise.jpg',
                'nama_penginput'  => 'Super Administrator Maijek',
                'created_at'      => '2026-09-12 11:15:00',
                'updated_at'      => '2026-09-12 11:15:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202609-0005',
                'tanggal'         => '2026-09-16',
                'tipe'            => 'pemasukan',
                'category_id'     => 1, // Bagi hasil driver
                'kas_account_id'  => 1,
                'investor_id'     => null,
                'jumlah'          => 28400000.00,
                'judul'           => 'Pemasukan Bagi Hasil Driver Maijek (September Rekap)',
                'keterangan'      => 'Peningkatan order di event lokal & hari libur',
                'bukti_transaksi' => 'bukti_bagi_hasil_driver.jpg',
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-09-16 16:30:00',
                'updated_at'      => '2026-09-16 16:30:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202609-0006',
                'tanggal'         => '2026-09-20',
                'tipe'            => 'pemasukan',
                'category_id'     => 2, // Komisi Resto
                'kas_account_id'  => 1,
                'investor_id'     => null,
                'jumlah'          => 19750000.00,
                'judul'           => 'Pemasukan Komisi Merchant MaiFood (September Rekap)',
                'keterangan'      => 'Hasil kemitraan 55 UMKM kuliner di Polman',
                'bukti_transaksi' => 'bukti_komisi_resto.jpg',
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-09-20 15:45:00',
                'updated_at'      => '2026-09-20 15:45:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202609-0007',
                'tanggal'         => '2026-09-25',
                'tipe'            => 'pengeluaran',
                'category_id'     => 10, // Gaji Tim
                'kas_account_id'  => 2,
                'investor_id'     => null,
                'jumlah'          => 12000000.00,
                'judul'           => 'Gaji Bulanan Tim CS & Operasional (September)',
                'keterangan'      => 'Gaji reguler tim operasional',
                'bukti_transaksi' => null,
                'nama_penginput'  => 'Super Administrator Maijek',
                'created_at'      => '2026-09-25 13:00:00',
                'updated_at'      => '2026-09-25 13:00:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202609-0008',
                'tanggal'         => '2026-09-30',
                'tipe'            => 'pengeluaran',
                'category_id'     => 12, // Dividen Investor
                'kas_account_id'  => 3, // BNI
                'investor_id'     => 1, // H. Baharuddin
                'jumlah'          => 14700000.00,
                'judul'           => 'Pembayaran Dividen Q3 Kepada H. Baharuddin (35%)',
                'keterangan'      => 'Pencairan bagi hasil laba operasional Q3 sesuai porsi saham',
                'bukti_transaksi' => 'bukti_dividen_q3.jpg',
                'nama_penginput'  => 'Super Administrator Maijek',
                'created_at'      => '2026-09-30 17:00:00',
                'updated_at'      => '2026-09-30 17:00:00',
            ],
            // Oktober 2026 Transactions (Bulan Berjalan)
            [
                'nomor_transaksi' => 'TRX-202610-0001',
                'tanggal'         => '2026-10-02',
                'tipe'            => 'pengeluaran',
                'category_id'     => 7, // Server
                'kas_account_id'  => 2,
                'investor_id'     => null,
                'jumlah'          => 4500000.00,
                'judul'           => 'Biaya Server Cloud & Load Balancer (Oktober)',
                'keterangan'      => 'Pembayaran hosting cloud backend',
                'bukti_transaksi' => 'bukti_server_cloud.jpg',
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-10-02 09:15:00',
                'updated_at'      => '2026-10-02 09:15:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202610-0002',
                'tanggal'         => '2026-10-05',
                'tipe'            => 'pemasukan',
                'category_id'     => 1, // Bagi Hasil Driver
                'kas_account_id'  => 1,
                'investor_id'     => null,
                'jumlah'          => 18750000.00,
                'judul'           => 'Setoran Bagi Hasil Trip Driver Maijek (Minggu I Oktober)',
                'keterangan'      => 'Setoran fee mitra driver aplikasi',
                'bukti_transaksi' => 'bukti_bagi_hasil_driver.jpg',
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-10-05 14:00:00',
                'updated_at'      => '2026-10-05 14:00:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202610-0003',
                'tanggal'         => '2026-10-08',
                'tipe'            => 'pemasukan',
                'category_id'     => 2, // Komisi Resto
                'kas_account_id'  => 1,
                'investor_id'     => null,
                'jumlah'          => 14300000.00,
                'judul'           => 'Komisi Merchant Partner MaiFood (Minggu I Oktober)',
                'keterangan'      => 'Settlement komisi merchant kuliner',
                'bukti_transaksi' => 'bukti_komisi_resto.jpg',
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-10-08 16:10:00',
                'updated_at'      => '2026-10-08 16:10:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202610-0004',
                'tanggal'         => '2026-10-10',
                'tipe'            => 'pemasukan',
                'category_id'     => 3, // Platform Fee
                'kas_account_id'  => 1,
                'investor_id'     => null,
                'jumlah'          => 5400000.00,
                'judul'           => 'Pendapatan Biaya Layanan Aplikasi & Jasa Titip',
                'keterangan'      => 'Platform fee konsumen Maijek periode awal Oktober',
                'bukti_transaksi' => null,
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-10-10 11:30:00',
                'updated_at'      => '2026-10-10 11:30:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202610-0005',
                'tanggal'         => '2026-10-12',
                'tipe'            => 'pengeluaran',
                'category_id'     => 13, // Operasional Kantor
                'kas_account_id'  => 4, // Petty Cash
                'investor_id'     => null,
                'jumlah'          => 1250000.00,
                'judul'           => 'Biaya Listrik, Internet Fiber & Konsumsi Kantor',
                'keterangan'      => 'Keperluan operasional basecamp Maijek Polewali',
                'bukti_transaksi' => null,
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-10-12 10:00:00',
                'updated_at'      => '2026-10-12 10:00:00',
            ],
            [
                'nomor_transaksi' => 'TRX-202610-0006',
                'tanggal'         => '2026-10-15',
                'tipe'            => 'pemasukan',
                'category_id'     => 6, // Kerjasama Iklan
                'kas_account_id'  => 1,
                'investor_id'     => null,
                'jumlah'          => 4500000.00,
                'judul'           => 'Pembayaran Slot Banner Promosi Merchant Premium',
                'keterangan'      => 'Iklan banner 3 merchant restoran di beranda aplikasi Maijek',
                'bukti_transaksi' => null,
                'nama_penginput'  => 'Staff Finance & Accounting',
                'created_at'      => '2026-10-15 15:00:00',
                'updated_at'      => '2026-10-15 15:00:00',
            ],
        ];
        $db->table('transactions')->insertBatch($transactions);

        // 8. Seed: Investment Logs
        $investmentLogs = [
            [
                'investor_id'     => 1,
                'transaction_id'  => 1,
                'tipe'            => 'suntikan_modal',
                'jumlah'          => 150000000.00,
                'tanggal'         => '2026-07-05',
                'periode_dividen' => null,
                'bukti'           => 'bukti_investor_bahar.jpg',
                'keterangan'      => 'Penyertaan modal pokok tahap 1',
                'created_at'      => '2026-07-05 09:30:00',
                'updated_at'      => '2026-07-05 09:30:00',
            ],
            [
                'investor_id'     => 2,
                'transaction_id'  => 2,
                'tipe'            => 'suntikan_modal',
                'jumlah'          => 100000000.00,
                'tanggal'         => '2026-07-08',
                'periode_dividen' => null,
                'bukti'           => 'bukti_investor_mandar.jpg',
                'keterangan'      => 'Penyertaan modal mitra ventura',
                'created_at'      => '2026-07-08 14:15:00',
                'updated_at'      => '2026-07-08 14:15:00',
            ],
            [
                'investor_id'     => 3,
                'transaction_id'  => null,
                'tipe'            => 'suntikan_modal',
                'jumlah'          => 80000000.00,
                'tanggal'         => '2026-03-01',
                'periode_dividen' => null,
                'bukti'           => null,
                'keterangan'      => 'Modal pendanaan sistem & riset',
                'created_at'      => '2026-03-01 10:00:00',
                'updated_at'      => '2026-03-01 10:00:00',
            ],
            [
                'investor_id'     => 4,
                'transaction_id'  => null,
                'tipe'            => 'suntikan_modal',
                'jumlah'          => 70000000.00,
                'tanggal'         => '2026-04-20',
                'periode_dividen' => null,
                'bukti'           => null,
                'keterangan'      => 'Penyertaan modal voucher promo',
                'created_at'      => '2026-04-20 11:00:00',
                'updated_at'      => '2026-04-20 11:00:00',
            ],
            [
                'investor_id'     => 1,
                'transaction_id'  => 13,
                'tipe'            => 'dividen_cair',
                'jumlah'          => 14700000.00,
                'tanggal'         => '2026-09-30',
                'periode_dividen' => 'Q3 2026 (Juli - September)',
                'bukti'           => 'bukti_dividen_q3.jpg',
                'keterangan'      => 'Bagi hasil laba bersih Q3 2026 (porsi 35%)',
                'created_at'      => '2026-09-30 17:00:00',
                'updated_at'      => '2026-09-30 17:00:00',
            ],
        ];
        $db->table('investment_logs')->insertBatch($investmentLogs);

        // 9. Update real-time kas_account saldo_saat_ini based on transactions
        $allKas = $db->table('kas_accounts')->get()->getResultArray();
        foreach ($allKas as $kas) {
            $in = $db->table('transactions')
                ->where('kas_account_id', $kas['id'])
                ->where('tipe', 'pemasukan')
                ->selectSum('jumlah')
                ->get()->getRow()->jumlah ?? 0;

            $out = $db->table('transactions')
                ->where('kas_account_id', $kas['id'])
                ->where('tipe', 'pengeluaran')
                ->selectSum('jumlah')
                ->get()->getRow()->jumlah ?? 0;

            $currentBalance = $kas['saldo_awal'] + $in - $out;
            $db->table('kas_accounts')->where('id', $kas['id'])->update(['saldo_saat_ini' => $currentBalance]);
        }

        // 10. Seed: System Settings
        $settings = [
            ['setting_key' => 'app_name', 'setting_value' => 'Maijek Financial & Investment Monitoring System'],
            ['setting_key' => 'company_name', 'setting_value' => 'PT. Maijek Transportasi Nusantara'],
            ['setting_key' => 'currency_symbol', 'setting_value' => 'Rp'],
            ['setting_key' => 'target_roi_annual', 'setting_value' => '18.5'],
            ['setting_key' => 'contact_finance', 'setting_value' => 'finance@maijek.id'],
            ['setting_key' => 'app_version', 'setting_value' => 'v2.4.0-Enterprise'],
        ];
        $db->table('system_settings')->insertBatch($settings);
    }
}
