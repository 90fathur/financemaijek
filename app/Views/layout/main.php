<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title ?? 'Monitoring Keuangan Maijek') ?> - Maijek Finance</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 & Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <!-- ApexCharts CSS -->
    <link href="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2/dist/apexcharts.css" rel="stylesheet">
    
    <!-- Custom Compact Micro-Admin CSS -->
    <style>
        :root {
            --mj-primary: #059669;
            --mj-primary-hover: #047857;
            --mj-primary-light: #ecfdf5;
            --mj-sidebar-bg: #0f172a;
            --mj-sidebar-hover: #1e293b;
            --mj-sidebar-active: #10b981;
            --mj-body-bg: #f8fafc;
            --mj-card-border: #e2e8f0;
            --mj-text-main: #1e293b;
            --mj-text-muted: #64748b;
            --mj-font-size-base: 12.5px;
        }

        body {
            font-family: 'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            font-size: var(--mj-font-size-base);
            color: var(--mj-text-main);
            background-color: var(--mj-body-bg);
            line-height: 1.45;
            letter-spacing: -0.01em;
            overflow-x: hidden;
        }

        /* High-density & Compact typography */
        h1, h2, h3, h4, h5, h6 {
            color: #0f172a;
            letter-spacing: -0.025em;
            font-weight: 700;
        }
        h1 { font-size: 1.35rem; }
        h2 { font-size: 1.2rem; }
        h3 { font-size: 1.05rem; }
        h4 { font-size: 0.95rem; }
        h5 { font-size: 0.85rem; }
        h6 { font-size: 0.78rem; }

        .small, small { font-size: 11px; }
        .micro-text { font-size: 10px; letter-spacing: 0.02em; }

        /* Sidebar */
        #sidebar {
            width: 240px;
            min-height: 100vh;
            background: var(--mj-sidebar-bg);
            color: #94a3b8;
            position: fixed;
            top: 0;
            bottom: 0;
            left: 0;
            z-index: 1030;
            transition: all 0.25s ease-in-out;
            display: flex;
            flex-direction: column;
            border-right: 1px solid #1e293b;
        }

        #sidebar .brand-section {
            padding: 16px 18px;
            border-bottom: 1px solid #1e293b;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .brand-badge {
            width: 32px;
            height: 32px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-weight: 800;
            font-size: 15px;
            box-shadow: 0 4px 10px rgba(16, 185, 129, 0.35);
        }

        .sidebar-menu {
            padding: 12px 10px;
            flex-grow: 1;
            overflow-y: auto;
        }

        .sidebar-header {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: #475569;
            font-weight: 700;
            padding: 10px 10px 4px 10px;
        }

        .nav-link-custom {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 7px 12px;
            color: #94a3b8;
            border-radius: 6px;
            font-weight: 500;
            font-size: 12px;
            text-decoration: none;
            transition: all 0.15s ease;
            margin-bottom: 2px;
        }

        .nav-link-custom i {
            font-size: 14px;
            width: 18px;
            text-align: center;
        }

        .nav-link-custom:hover {
            color: #ffffff;
            background: var(--mj-sidebar-hover);
        }

        .nav-link-custom.active {
            color: #ffffff;
            background: #10b981;
            font-weight: 600;
            box-shadow: 0 2px 8px rgba(16, 185, 129, 0.4);
        }

        /* Topbar & Content Area */
        #main-wrapper {
            margin-left: 240px;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            transition: all 0.25s ease-in-out;
        }

        #topbar {
            height: 54px;
            background: #ffffff;
            border-bottom: 1px solid var(--mj-card-border);
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            position: sticky;
            top: 0;
            z-index: 1020;
        }

        .content-body {
            padding: 20px 24px 40px 24px;
            flex-grow: 1;
        }

        /* Compact Cards */
        .card-custom {
            background: #ffffff;
            border: 1px solid var(--mj-card-border);
            border-radius: 8px;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.03);
            transition: border-color 0.15s ease, box-shadow 0.15s ease;
        }
        .card-custom:hover {
            border-color: #cbd5e1;
        }

        .card-header-compact {
            padding: 10px 14px;
            background: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .card-body-compact {
            padding: 14px;
        }

        /* Stat KPI Cards */
        .kpi-card {
            padding: 12px 14px;
            border-radius: 8px;
            background: #ffffff;
            border: 1px solid var(--mj-card-border);
            position: relative;
            overflow: hidden;
            transition: transform 0.15s ease, box-shadow 0.15s ease;
        }
        .kpi-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.05);
        }
        .kpi-title {
            font-size: 10.5px;
            text-transform: uppercase;
            font-weight: 700;
            letter-spacing: 0.04em;
            color: var(--mj-text-muted);
            margin-bottom: 4px;
        }
        .kpi-value {
            font-size: 1.15rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.02em;
        }
        .kpi-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 16px;
        }

        /* Compact Table */
        .table-compact {
            font-size: 11.5px;
            margin-bottom: 0;
        }
        .table-compact thead th {
            background-color: #f8fafc;
            color: #475569;
            font-weight: 700;
            font-size: 10.5px;
            text-transform: uppercase;
            letter-spacing: 0.04em;
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            border-top: none;
            white-space: nowrap;
        }
        .table-compact tbody td {
            padding: 7px 10px;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }
        .table-compact tbody tr:hover {
            background-color: #f8fafc;
        }

        /* Custom Badges */
        .badge-custom {
            font-size: 10px;
            font-weight: 600;
            padding: 3px 7px;
            border-radius: 4px;
            letter-spacing: 0.02em;
        }
        .bg-emerald-subtle { background-color: #ecfdf5 !important; }
        .text-emerald { color: #059669 !important; }
        .border-emerald-subtle { border-color: #a7f3d0 !important; }

        .bg-rose-subtle { background-color: #fff1f2 !important; }
        .text-rose { color: #e11d48 !important; }
        .border-rose-subtle { border-color: #fecdd3 !important; }

        .bg-blue-subtle { background-color: #eff6ff !important; }
        .text-blue { color: #2563eb !important; }
        .border-blue-subtle { border-color: #bfdbfe !important; }

        .bg-amber-subtle { background-color: #fffbeb !important; }
        .text-amber { color: #d97706 !important; }
        .border-amber-subtle { border-color: #fde68a !important; }

        /* Form Inputs High Density */
        .form-control-sm, .form-select-sm {
            font-size: 11.5px;
            padding: 5px 9px;
            border-radius: 5px;
            border-color: #cbd5e1;
        }
        .form-control-sm:focus, .form-select-sm:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 2px rgba(16, 185, 129, 0.2);
        }
        .form-label-compact {
            font-size: 11px;
            font-weight: 600;
            color: #334155;
            margin-bottom: 3px;
        }

        /* Buttons Compact */
        .btn-compact {
            font-size: 11.5px;
            padding: 5px 11px;
            font-weight: 600;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-compact-xs {
            font-size: 10.5px;
            padding: 3px 7px;
            font-weight: 600;
            border-radius: 4px;
        }
        .btn-emerald {
            background-color: #059669;
            color: #ffffff;
            border: 1px solid #059669;
        }
        .btn-emerald:hover {
            background-color: #047857;
            color: #ffffff;
            border-color: #047857;
        }

        /* Thumbnail preview */
        .receipt-thumb {
            width: 32px;
            height: 32px;
            border-radius: 4px;
            object-fit: cover;
            border: 1px solid #cbd5e1;
            cursor: pointer;
            transition: transform 0.15s ease;
        }
        .receipt-thumb:hover {
            transform: scale(1.1);
            border-color: #059669;
        }

        /* Lightbox Image Modal */
        #receiptImageModal .modal-body {
            background: #0f172a;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 10px;
        }
        #receiptImageModal img {
            max-height: 80vh;
            max-width: 100%;
            border-radius: 6px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.5);
        }

        /* Scrollbar */
        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }
        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }
        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 3px;
        }
        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        @media (max-width: 991.98px) {
            #sidebar {
                transform: translateX(-100%);
            }
            #sidebar.show {
                transform: translateX(0);
            }
            #main-wrapper {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

    <!-- Sidebar Navigation -->
    <nav id="sidebar">
        <div class="brand-section">
            <div class="brand-badge">
                <i class="bi bi-send-fill" style="transform: rotate(45deg);"></i>
            </div>
            <div>
                <div class="fw-bold text-white" style="font-size: 13px; letter-spacing: -0.01em;">MAIJEK FINANCE</div>
                <div class="micro-text text-emerald fw-semibold">INVESTMENT & MONITORING</div>
            </div>
        </div>

        <div class="sidebar-menu">
            <div class="sidebar-header">Utama</div>
            <a href="<?= base_url('dashboard') ?>" class="nav-link-custom <?= (url_is('dashboard') || url_is('/')) ? 'active' : '' ?>">
                <i class="bi bi-grid-1x2-fill"></i>
                <span>Dashboard Overview</span>
            </a>

            <div class="sidebar-header mt-2">Buku Keuangan</div>
            <a href="<?= base_url('transactions') ?>" class="nav-link-custom <?= url_is('transactions*') ? 'active' : '' ?>">
                <i class="bi bi-journal-check"></i>
                <span>Transaksi (Arus Kas)</span>
            </a>
            <a href="<?= base_url('reports') ?>" class="nav-link-custom <?= url_is('reports*') ? 'active' : '' ?>">
                <i class="bi bi-file-earmark-bar-graph"></i>
                <span>Laporan & Laba Rugi</span>
            </a>

            <div class="sidebar-header mt-2">Investasi & Modal</div>
            <a href="<?= base_url('investors') ?>" class="nav-link-custom <?= url_is('investors*') ? 'active' : '' ?>">
                <i class="bi bi-person-badge"></i>
                <span>Investor & Bagi Hasil</span>
            </a>

            <div class="sidebar-header mt-2">Master Data Dinamis</div>
            <a href="<?= base_url('categories') ?>" class="nav-link-custom <?= url_is('categories*') ? 'active' : '' ?>">
                <i class="bi bi-tags"></i>
                <span>Kategori Dinamis</span>
            </a>
            <a href="<?= base_url('kas-accounts') ?>" class="nav-link-custom <?= url_is('kas-accounts*') ? 'active' : '' ?>">
                <i class="bi bi-wallet2"></i>
                <span>Akun Kas & Bank</span>
            </a>

            <div class="sidebar-header mt-2">Sistem & Integrasi</div>
            <a href="<?= base_url('settings/whatsapp') ?>" class="nav-link-custom <?= url_is('settings/whatsapp*') ? 'active' : '' ?>">
                <i class="bi bi-whatsapp text-success"></i>
                <span>Pengaturan WA Grup</span>
                <span class="badge bg-success-subtle text-success ms-auto py-0 px-1" style="font-size: 9.5px;">CloudChat</span>
            </a>
            <div class="px-2 py-2 mt-2 rounded" style="background: rgba(255,255,255,0.03); border: 1px solid #1e293b;">
                <div class="micro-text text-muted">Status Koneksi</div>
                <div class="d-flex align-items-center gap-2 mt-1">
                    <span class="spinner-grow spinner-grow-sm text-success" style="width: 8px; height: 8px;" role="status"></span>
                    <span class="text-white fw-semibold micro-text">Database Terhubung</span>
                </div>
            </div>
        </div>

        <!-- Sidebar Footer User Profile -->
        <div class="p-2 border-top border-secondary border-opacity-25" style="background: #090d16;">
            <div class="d-flex align-items-center justify-content-between p-2 rounded">
                <div class="d-flex align-items-center gap-2 overflow-hidden">
                    <div class="rounded-circle bg-emerald text-white d-flex align-items-center justify-content-center fw-bold" style="width: 28px; height: 28px; font-size: 11px;">
                        <?= strtoupper(substr(session()->get('nama') ?? 'A', 0, 1)) ?>
                    </div>
                    <div class="overflow-hidden">
                        <div class="text-white fw-semibold text-truncate" style="font-size: 11px;"><?= esc(session()->get('nama') ?? 'Administrator') ?></div>
                        <div class="micro-text text-muted"><?= strtoupper(esc(session()->get('role') ?? 'ADMIN')) ?></div>
                    </div>
                </div>
                <a href="<?= base_url('auth/logout') ?>" class="btn btn-outline-danger btn-compact-xs" title="Keluar / Logout">
                    <i class="bi bi-box-arrow-right"></i>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content Wrapper -->
    <div id="main-wrapper">
        <!-- Topbar -->
        <header id="topbar">
            <div class="d-flex align-items-center gap-3">
                <button class="btn btn-sm btn-light border d-lg-none" id="sidebarToggle" type="button">
                    <i class="bi bi-list"></i>
                </button>
                <div class="d-none d-md-flex align-items-center gap-2 text-muted" style="font-size: 11px;">
                    <i class="bi bi-geo-alt-fill text-emerald"></i>
                    <span>Maijek Regional West Sulawesi</span>
                    <span class="mx-1">•</span>
                    <i class="bi bi-calendar3"></i>
                    <span id="currentDateStr"><?= date('l, d F Y') ?></span>
                </div>
            </div>

            <div class="d-flex align-items-center gap-2">
                <a href="<?= base_url('transactions/export-csv') ?>" class="btn btn-outline-secondary btn-compact" title="Unduh CSV Transaksi">
                    <i class="bi bi-download"></i>
                    <span class="d-none d-sm-inline">Export Excel/CSV</span>
                </a>
                <button type="button" class="btn btn-emerald btn-compact" data-bs-toggle="modal" data-bs-target="#modalTambahTransaksiCepat">
                    <i class="bi bi-plus-circle-fill"></i>
                    <span>+ Catat Transaksi</span>
                </button>
            </div>
        </header>

        <!-- Content Body -->
        <main class="content-body">
            <!-- Flash Message Alerts -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-3 border-0 shadow-sm" style="font-size: 12px; background-color: #d1fae5; color: #065f46;" role="alert">
                    <i class="bi bi-check-circle-fill me-2 fs-6"></i>
                    <div class="flex-grow-1"><?= session()->getFlashdata('success') ?></div>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-3 border-0 shadow-sm" style="font-size: 12px; background-color: #ffe4e6; color: #9f1239;" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2 fs-6"></i>
                    <div class="flex-grow-1"><?= session()->getFlashdata('error') ?></div>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('warning')): ?>
                <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center py-2 px-3 mb-3 border-0 shadow-sm" style="font-size: 12px; background-color: #fef3c7; color: #92400e;" role="alert">
                    <i class="bi bi-info-circle-fill me-2 fs-6"></i>
                    <div class="flex-grow-1"><?= session()->getFlashdata('warning') ?></div>
                    <button type="button" class="btn-close btn-close-sm" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <!-- View Injected Here -->
            <?= $this->renderSection('content') ?>
        </main>
    </div>

    <!-- Universal Receipt Preview Modal -->
    <div class="modal fade" id="receiptImageModal" tabindex="-1" aria-labelledby="receiptModalTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header py-2 px-3 bg-dark text-white border-bottom border-secondary">
                    <h6 class="modal-title mb-0 d-flex align-items-center gap-2" id="receiptModalTitle">
                        <i class="bi bi-receipt-cutoff text-emerald"></i>
                        <span id="receiptModalTitleText">Bukti Transaksi Keuangan</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <img id="receiptModalImg" src="" alt="Bukti Transaksi" class="img-fluid">
                </div>
                <div class="modal-footer py-2 px-3 bg-light d-flex justify-content-between">
                    <div class="micro-text text-muted" id="receiptModalInfo">Tervalidasi di Sistem Monitoring Maijek</div>
                    <div class="d-flex gap-2">
                        <a id="receiptDownloadBtn" href="" download class="btn btn-sm btn-outline-secondary btn-compact">
                            <i class="bi bi-download"></i> Unduh Bukti
                        </a>
                        <button type="button" class="btn btn-sm btn-secondary btn-compact" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        helper(['finance', 'form', 'url']);
        $globalDb = \Config\Database::connect();
        $globalCategories = $globalDb->table('categories')->orderBy('nama_kategori', 'ASC')->get()->getResultArray();
        $globalKasAccounts = $globalDb->table('kas_accounts')->orderBy('nama_akun', 'ASC')->get()->getResultArray();
        $globalInvestors = $globalDb->table('investors')->where('status', 'aktif')->orderBy('nama_investor', 'ASC')->get()->getResultArray();
    ?>

    <!-- Quick Global Modal: Tambah Transaksi Cepat -->
    <div class="modal fade" id="modalTambahTransaksiCepat" tabindex="-1" aria-labelledby="tambahTxTitle" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-md">
            <div class="modal-content border-0 shadow-lg">
                <form action="<?= base_url('transactions/store') ?>" method="POST" enctype="multipart/form-data">
                    <?= csrf_field() ?>
                    <div class="modal-header py-2 px-3 bg-emerald text-white">
                        <h6 class="modal-title mb-0 d-flex align-items-center gap-2" id="tambahTxTitle">
                            <i class="bi bi-plus-circle-fill"></i>
                            <span>Catat Transaksi Keuangan Maijek</span>
                        </h6>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body p-3">
                        <div class="row g-2">
                            <div class="col-6">
                                <label class="form-label-compact">Tipe Transaksi <span class="text-danger">*</span></label>
                                <select name="tipe" id="quick_tipe" class="form-select form-select-sm" required onchange="filterQuickCategories()">
                                    <option value="pemasukan" selected>🟢 Pemasukan (Inflow)</option>
                                    <option value="pengeluaran">🔴 Pengeluaran (Outflow)</option>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label-compact">Tanggal Transaksi <span class="text-danger">*</span></label>
                                <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label-compact">Judul / Uraian Transaksi <span class="text-danger">*</span></label>
                                <input type="text" name="judul" class="form-control form-control-sm" placeholder="Contoh: Bagi Hasil Driver Batch 1 / Tagihan Server" required>
                            </div>
                            <div class="col-6">
                                <label class="form-label-compact">Kategori <span class="text-danger">*</span></label>
                                <select name="category_id" id="quick_category_id" class="form-select form-select-sm" required>
                                    <?php 
                                    $defaultQuickCats = array_filter($globalCategories, function($c) {
                                        return ($c['tipe'] ?? '') === 'pemasukan';
                                    });
                                    ?>
                                    <?php if (!empty($defaultQuickCats)): ?>
                                        <?php foreach ($defaultQuickCats as $c): ?>
                                            <option value="<?= $c['id'] ?>"><?= esc($c['nama_kategori']) ?></option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">-- Belum ada kategori pemasukan --</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label-compact">Rekening Kas / Bank <span class="text-danger">*</span></label>
                                <select name="kas_account_id" id="quick_kas_account_id" class="form-select form-select-sm" required>
                                    <?php if (!empty($globalKasAccounts)): ?>
                                        <?php foreach ($globalKasAccounts as $k): ?>
                                            <option value="<?= $k['id'] ?>"><?= esc($k['nama_akun']) ?> (<?= function_exists('format_rupiah') ? format_rupiah($k['saldo_saat_ini']) : 'Rp ' . number_format($k['saldo_saat_ini'], 0, ',', '.') ?>)</option>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <option value="">-- Belum ada rekening kas --</option>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-6">
                                <label class="form-label-compact">Nominal (Rupiah) <span class="text-danger">*</span></label>
                                <div class="input-group input-group-sm">
                                    <span class="input-group-text bg-light border-end-0 fw-bold">Rp</span>
                                    <input type="number" name="jumlah" class="form-control form-control-sm" placeholder="0" min="1000" step="1" required>
                                </div>
                            </div>
                            <div class="col-6">
                                <label class="form-label-compact">Investor Terkait (Opsional)</label>
                                <select name="investor_id" id="quick_investor_id" class="form-select form-select-sm">
                                    <option value="">-- Bukan Transaksi Investor --</option>
                                    <?php if (!empty($globalInvestors)): ?>
                                        <?php foreach ($globalInvestors as $i): ?>
                                            <option value="<?= $i['id'] ?>"><?= esc($i['nama_investor']) ?> (<?= esc($i['kode_investor']) ?> - <?= $i['persentase_bagi_hasil'] ?>%)</option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label-compact">Keterangan / Rincian Tambahan</label>
                                <textarea name="keterangan" rows="2" class="form-control form-control-sm" placeholder="Catatan bukti transfer, nomor invoice, dsb."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label-compact">Upload Bukti Transaksi (Nota / Struk / Struk Transfer)</label>
                                <input type="file" name="bukti_transaksi" class="form-control form-control-sm" accept="image/*,application/pdf">
                                <div class="micro-text text-muted mt-1">Format: JPG, PNG, WEBP, PDF (Maks. 5 MB)</div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer py-2 px-3 bg-light">
                        <button type="button" class="btn btn-secondary btn-compact" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-emerald btn-compact">
                            <i class="bi bi-save-fill"></i> Simpan Transaksi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts@3.45.2/dist/apexcharts.min.js"></script>
    
    <script>
        // Sidebar Toggle for Mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('show');
            });
        }

        // Global Receipt Modal Viewer
        function openReceiptModal(imageUrl, title, info) {
            const modalImg = document.getElementById('receiptModalImg');
            const titleElem = document.getElementById('receiptModalTitleText');
            const infoElem = document.getElementById('receiptModalInfo');
            const downloadBtn = document.getElementById('receiptDownloadBtn');

            if (modalImg) modalImg.src = imageUrl;
            if (titleElem) titleElem.textContent = title || 'Bukti Transaksi Keuangan';
            if (infoElem) infoElem.textContent = info || 'Tervalidasi Resmi di Sistem Monitoring Maijek';
            if (downloadBtn) downloadBtn.href = imageUrl;

            const modal = new bootstrap.Modal(document.getElementById('receiptImageModal'));
            modal.show();
        }

        // Global helper for formatting Rupiah in JS
        function formatRupiahJS(number) {
            return 'Rp ' + Number(number).toLocaleString('id-ID');
        }

        // Global master data for quick modal (available everywhere across all pages)
        window.allCategories = <?= json_encode($globalCategories) ?>;
        window.allKasAccounts = <?= json_encode($globalKasAccounts) ?>;
        window.allInvestors = <?= json_encode($globalInvestors) ?>;

        function filterQuickCategories() {
            const tipeSelect = document.getElementById('quick_tipe');
            const catSelect = document.getElementById('quick_category_id');
            if (!tipeSelect || !catSelect) return;

            const selectedTipe = tipeSelect.value;
            const filtered = (window.allCategories || []).filter(c => c.tipe === selectedTipe);
            
            if (filtered.length > 0) {
                catSelect.innerHTML = filtered.map(c => 
                    `<option value="${c.id}">${c.nama_kategori}</option>`
                ).join('');
            } else {
                catSelect.innerHTML = '<option value="">-- Belum ada kategori untuk tipe ini --</option>';
            }
        }

        function initQuickModalData(categories, kasAccounts, investors) {
            if (Array.isArray(categories) && categories.length > 0 && categories[0].tipe) {
                window.allCategories = categories;
            }
            if (Array.isArray(kasAccounts) && kasAccounts.length > 0 && kasAccounts[0].nama_akun) {
                window.allKasAccounts = kasAccounts;
                const kasSelect = document.getElementById('quick_kas_account_id');
                if (kasSelect) {
                    kasSelect.innerHTML = window.allKasAccounts.map(k => 
                        `<option value="${k.id}">${k.nama_akun} (${formatRupiahJS(k.saldo_saat_ini)})</option>`
                    ).join('');
                }
            }
            if (Array.isArray(investors) && investors.length > 0 && investors[0].nama_investor) {
                window.allInvestors = investors;
                const invSelect = document.getElementById('quick_investor_id');
                if (invSelect) {
                    invSelect.innerHTML = '<option value="">-- Bukan Transaksi Investor --</option>' + 
                        window.allInvestors.map(i => `<option value="${i.id}">${i.nama_investor} (${i.kode_investor} - ${i.persentase_bagi_hasil}%)</option>`).join('');
                }
            }
            filterQuickCategories();
        }

        // Initialize and bind modal event
        document.addEventListener('DOMContentLoaded', function() {
            filterQuickCategories();
            const quickModal = document.getElementById('modalTambahTransaksiCepat');
            if (quickModal) {
                quickModal.addEventListener('show.bs.modal', function() {
                    filterQuickCategories();
                });
            }
        });
    </script>

    <?= $this->renderSection('scripts') ?>
</body>
</html>
