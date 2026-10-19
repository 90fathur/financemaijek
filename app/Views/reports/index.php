<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
    <div>
        <h1 class="h5 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-file-earmark-bar-graph-fill text-emerald"></i>
            <span>Laporan Keuangan & Laba Rugi Operasional</span>
        </h1>
        <p class="text-muted small mb-0">Laporan laba bersih (profit & loss), arus kas masuk vs keluar, dan mutasi kas Maijek.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="<?= base_url('reports/print?start_date=' . $startDate . '&end_date=' . $endDate) ?>" target="_blank" class="btn btn-outline-dark btn-compact">
            <i class="bi bi-printer-fill"></i> Cetak Dokumen PDF
        </a>
        <a href="<?= base_url('transactions/export-csv?start_date=' . $startDate . '&end_date=' . $endDate) ?>" class="btn btn-emerald btn-compact">
            <i class="bi bi-download"></i> Unduh File CSV
        </a>
    </div>
</div>

<!-- Date Filter Card -->
<div class="card-custom mb-3">
    <div class="card-body-compact">
        <form action="<?= base_url('reports') ?>" method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-3">
                <label class="form-label-compact">Periode Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= esc($startDate) ?>" required>
            </div>
            <div class="col-6 col-md-3">
                <label class="form-label-compact">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= esc($endDate) ?>" required>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-emerald btn-compact flex-grow-1">
                    <i class="bi bi-filter"></i> Terapkan Rentang
                </button>
                <a href="<?= base_url('reports?start_date=' . date('Y-01-01') . '&end_date=' . date('Y-m-d')) ?>" class="btn btn-outline-secondary btn-compact" title="Tahun Berjalan">
                    Tahun Ini
                </a>
                <a href="<?= base_url('reports?start_date=' . date('Y-m-01') . '&end_date=' . date('Y-m-t')) ?>" class="btn btn-outline-secondary btn-compact" title="Bulan Berjalan">
                    Bulan Ini
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Profit & Loss Summary Row -->
<div class="row g-2 mb-3">
    <div class="col-12 col-md-4">
        <div class="p-3 rounded bg-white border border-emerald-subtle shadow-sm">
            <div class="micro-text text-muted text-uppercase fw-bold">Total Pendapatan (Revenue)</div>
            <div class="h5 fw-bold text-success mb-1"><?= format_rupiah($totalIncome) ?></div>
            <div class="micro-text text-muted">Dari <?= count($incomeRows) ?> pos kategori pemasukan</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="p-3 rounded bg-white border border-rose-subtle shadow-sm">
            <div class="micro-text text-muted text-uppercase fw-bold">Total Beban & Pengeluaran (Expense)</div>
            <div class="h5 fw-bold text-danger mb-1"><?= format_rupiah($totalExpense) ?></div>
            <div class="micro-text text-muted">Dari <?= count($expenseRows) ?> pos kategori pengeluaran</div>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="p-3 rounded bg-white border <?= ($netProfit >= 0) ? 'border-blue-subtle' : 'border-danger' ?> shadow-sm">
            <div class="micro-text text-muted text-uppercase fw-bold">Laba Bersih Operasional (Net Profit)</div>
            <div class="h5 fw-bold <?= ($netProfit >= 0) ? 'text-primary' : 'text-danger' ?> mb-1">
                <?= format_rupiah($netProfit) ?>
            </div>
            <div class="micro-text text-muted">
                Margin: <?= ($totalIncome > 0) ? number_format(($netProfit / $totalIncome) * 100, 1) : 0 ?>% dari Pendapatan
            </div>
        </div>
    </div>
</div>

<!-- Laporan Rinci Laba Rugi -->
<div class="row g-2 mb-3">
    <!-- Rincian Pemasukan -->
    <div class="col-12 col-lg-6">
        <div class="card-custom h-100">
            <div class="card-header-compact bg-light">
                <span class="fw-bold text-success" style="font-size: 12px;"><i class="bi bi-plus-circle me-1"></i> Pos Pendapatan & Pemasukan</span>
                <span class="fw-bold text-success"><?= format_rupiah($totalIncome) ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-compact mb-0">
                    <thead>
                        <tr>
                            <th>Kategori Pendapatan</th>
                            <th class="text-center">Jml Transaksi</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end" style="width: 70px;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($incomeRows)): ?>
                            <tr><td colspan="4" class="text-center py-3 text-muted">Tidak ada pemasukan di periode ini.</td></tr>
                        <?php else: ?>
                            <?php foreach ($incomeRows as $in): ?>
                                <tr>
                                    <td class="fw-semibold text-dark">
                                        <?= esc($in['nama_kategori']) ?>
                                    </td>
                                    <td class="text-center text-muted"><?= $in['count_tx'] ?></td>
                                    <td class="text-end fw-bold text-success"><?= format_rupiah($in['total']) ?></td>
                                    <td class="text-end micro-text text-muted">
                                        <?= ($totalIncome > 0) ? number_format(($in['total'] / $totalIncome) * 100, 1) : 0 ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Rincian Pengeluaran -->
    <div class="col-12 col-lg-6">
        <div class="card-custom h-100">
            <div class="card-header-compact bg-light">
                <span class="fw-bold text-danger" style="font-size: 12px;"><i class="bi bi-dash-circle me-1"></i> Pos Beban & Pengeluaran</span>
                <span class="fw-bold text-danger"><?= format_rupiah($totalExpense) ?></span>
            </div>
            <div class="table-responsive">
                <table class="table table-compact mb-0">
                    <thead>
                        <tr>
                            <th>Kategori Beban</th>
                            <th class="text-center">Jml Transaksi</th>
                            <th class="text-end">Subtotal</th>
                            <th class="text-end" style="width: 70px;">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenseRows)): ?>
                            <tr><td colspan="4" class="text-center py-3 text-muted">Tidak ada pengeluaran di periode ini.</td></tr>
                        <?php else: ?>
                            <?php foreach ($expenseRows as $out): ?>
                                <tr>
                                    <td class="fw-semibold text-dark">
                                        <?= esc($out['nama_kategori']) ?>
                                    </td>
                                    <td class="text-center text-muted"><?= $out['count_tx'] ?></td>
                                    <td class="text-end fw-bold text-danger"><?= format_rupiah($out['total']) ?></td>
                                    <td class="text-end micro-text text-muted">
                                        <?= ($totalExpense > 0) ? number_format(($out['total'] / $totalExpense) * 100, 1) : 0 ?>%
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Ringkasan Log Transaksi di Periode Ini -->
<div class="card-custom">
    <div class="card-header-compact">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-list-check text-secondary"></i>
            <span class="fw-bold" style="font-size: 12px;">Lampiran Seluruh Transaksi Periode Ini (<?= count($transactions) ?> Data)</span>
        </div>
        <div class="micro-text text-muted">
            Rentang: <?= format_tanggal_indo($startDate) ?> s/d <?= format_tanggal_indo($endDate) ?>
        </div>
    </div>
    <div class="table-responsive">
        <table class="table table-compact align-middle table-hover">
            <thead>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Tanggal</th>
                    <th>Tipe</th>
                    <th>Kategori</th>
                    <th>Uraian</th>
                    <th>Rekening</th>
                    <th class="text-end">Nominal</th>
                    <th class="text-center">Bukti</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr><td colspan="8" class="text-center py-4 text-muted">Tidak ada transaksi dalam rentang tanggal ini.</td></tr>
                <?php else: ?>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td class="font-monospace fw-bold text-dark"><?= esc($tx['nomor_transaksi']) ?></td>
                            <td><?= date('d/m/Y', strtotime($tx['tanggal'])) ?></td>
                            <td><?= format_badge_tipe($tx['tipe']) ?></td>
                            <td>
                                <span class="badge border bg-light text-dark" style="font-size: 9.5px;">
                                    <?= esc($tx['nama_kategori'] ?? '-') ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark"><?= esc($tx['judul']) ?></div>
                                <?php if (!empty($tx['keterangan'])): ?>
                                    <div class="micro-text text-muted"><?= esc($tx['keterangan']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td><span class="text-secondary small"><?= esc($tx['nama_akun'] ?? '-') ?></span></td>
                            <td class="text-end fw-bold <?= ($tx['tipe'] === 'pemasukan') ? 'text-success' : 'text-danger' ?>">
                                <?= ($tx['tipe'] === 'pemasukan' ? '+' : '-') ?> <?= format_rupiah($tx['jumlah']) ?>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($tx['bukti_transaksi'])): ?>
                                    <img src="<?= base_url('uploads/bukti/' . $tx['bukti_transaksi']) ?>" 
                                         alt="Bukti" 
                                         class="receipt-thumb" 
                                         onclick="openReceiptModal('<?= base_url('uploads/bukti/' . $tx['bukti_transaksi']) ?>', '<?= esc($tx['nomor_transaksi']) ?>', 'Nominal: <?= format_rupiah($tx['jumlah']) ?>')">
                                <?php else: ?>
                                    <span class="micro-text text-muted">-</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>
