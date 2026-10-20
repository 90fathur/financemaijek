<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Title & Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
    <div>
        <h1 class="h5 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-speedometer2 text-emerald"></i>
            <span>Monitoring Keuangan & Investasi Dinamis Maijek</span>
        </h1>
        <p class="text-muted small mb-0">Ringkasan arus kas, portofolio modal investor, laba rugi, dan transaksi real-time.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <span class="badge bg-white border text-dark py-1 px-2 d-flex align-items-center gap-1 shadow-sm" style="font-size: 11px;">
            <i class="bi bi-clock-history text-emerald"></i>
            <span>Tahun Fiskal <?= date('Y') ?></span>
        </span>
        <a href="<?= base_url('reports') ?>" class="btn btn-outline-secondary btn-compact">
            <i class="bi bi-file-earmark-text"></i> Laporan Laba Rugi
        </a>
        <button type="button" class="btn btn-success btn-compact shadow-sm d-flex align-items-center gap-1" data-bs-toggle="modal" data-bs-target="#modalKirimWaRecap" title="Kirim Laporan Rekap Lengkap ke Grup WhatsApp">
            <i class="bi bi-whatsapp"></i>
            <span>Kirim Rekap ke Grup WA</span>
        </button>
    </div>
</div>

<!-- KPI Cards Grid (Compact & Crisp) -->
<div class="row g-2 mb-3">
    <!-- Total Modal Investasi -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="kpi-title">Modal Investasi</div>
                <div class="kpi-icon bg-emerald-subtle text-emerald"><i class="bi bi-piggy-bank"></i></div>
            </div>
            <div class="kpi-value text-emerald"><?= format_rupiah($totalModalInvestasi) ?></div>
            <div class="micro-text text-muted mt-1 d-flex align-items-center gap-1">
                <i class="bi bi-people-fill text-emerald"></i> <?= count($investors) ?> Investor Aktif
            </div>
        </div>
    </div>

    <!-- Total Saldo Kas Saat Ini -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="kpi-title">Saldo Kas & Bank</div>
                <div class="kpi-icon bg-blue-subtle text-blue"><i class="bi bi-wallet2"></i></div>
            </div>
            <div class="kpi-value text-blue"><?= format_rupiah($totalSaldoKas) ?></div>
            <div class="micro-text text-muted mt-1 d-flex align-items-center gap-1">
                <i class="bi bi-check-circle-fill text-success"></i> <?= count($kasAccounts) ?> Rekening Kas
            </div>
        </div>
    </div>

    <!-- Total Pemasukan -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="kpi-title">Total Pemasukan</div>
                <div class="kpi-icon bg-emerald-subtle text-emerald"><i class="bi bi-arrow-down-left-circle"></i></div>
            </div>
            <div class="kpi-value text-dark"><?= format_rupiah($totalPemasukan) ?></div>
            <div class="micro-text text-success mt-1 d-flex align-items-center gap-1">
                <i class="bi bi-calendar-check"></i> Bln Ini: <?= format_rupiah($bulanIniPemasukan) ?>
            </div>
        </div>
    </div>

    <!-- Total Pengeluaran -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="kpi-title">Total Pengeluaran</div>
                <div class="kpi-icon bg-rose-subtle text-rose"><i class="bi bi-arrow-up-right-circle"></i></div>
            </div>
            <div class="kpi-value text-rose"><?= format_rupiah($totalPengeluaran) ?></div>
            <div class="micro-text text-rose mt-1 d-flex align-items-center gap-1">
                <i class="bi bi-calendar-x"></i> Bln Ini: <?= format_rupiah($bulanIniPengeluaran) ?>
            </div>
        </div>
    </div>

    <!-- Laba Bersih Kumulatif -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="kpi-title">Laba Bersih (Net)</div>
                <div class="kpi-icon bg-amber-subtle text-amber"><i class="bi bi-graph-up-arrow"></i></div>
            </div>
            <div class="kpi-value <?= ($labaBersih >= 0) ? 'text-success' : 'text-danger' ?>">
                <?= format_rupiah($labaBersih) ?>
            </div>
            <div class="micro-text text-muted mt-1 d-flex align-items-center gap-1">
                <i class="bi bi-reception-4 text-emerald"></i> Net Bln Ini: <?= format_rupiah($bulanIniNet) ?>
            </div>
        </div>
    </div>

    <!-- Total Dividen Terbayar -->
    <div class="col-6 col-md-4 col-xl-2">
        <div class="kpi-card">
            <div class="d-flex justify-content-between align-items-start">
                <div class="kpi-title">Dividen Terbayar</div>
                <div class="kpi-icon bg-amber-subtle text-amber"><i class="bi bi-cash-stack"></i></div>
            </div>
            <div class="kpi-value text-amber"><?= format_rupiah($totalDividen) ?></div>
            <div class="micro-text text-muted mt-1 d-flex align-items-center gap-1">
                <i class="bi bi-pie-chart-fill text-warning"></i> Return to Investor
            </div>
        </div>
    </div>
</div>

<!-- Main Charts Section -->
<div class="row g-2 mb-3">
    <!-- Line / Area Chart: Cashflow Trend -->
    <div class="col-12 col-xl-8">
        <div class="card-custom h-100">
            <div class="card-header-compact">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-bar-chart-line-fill text-emerald"></i>
                    <span class="fw-bold" style="font-size: 12px;">Tren Arus Kas Masuk & Keluar (Tahun <?= date('Y') ?>)</span>
                </div>
                <div class="micro-text text-muted">Satuan dalam Rupiah (Rp)</div>
            </div>
            <div class="card-body-compact">
                <div id="chartCashFlow" style="min-height: 280px;"></div>
            </div>
        </div>
    </div>

    <!-- Donut Chart: Investor Share Distribution -->
    <div class="col-12 col-xl-4">
        <div class="card-custom h-100">
            <div class="card-header-compact">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart-fill text-primary"></i>
                    <span class="fw-bold" style="font-size: 12px;">Portofolio Kepemilikan Investor</span>
                </div>
                <a href="<?= base_url('investors') ?>" class="micro-text text-emerald fw-semibold text-decoration-none">Kelola &rarr;</a>
            </div>
            <div class="card-body-compact d-flex flex-column justify-content-between">
                <div id="chartInvestorShare" style="min-height: 220px;"></div>
                <div class="border-top pt-2 mt-2">
                    <div class="d-flex justify-content-between micro-text text-muted mb-1">
                        <span>Total Akumulasi Modal:</span>
                        <strong class="text-dark"><?= format_rupiah($totalModalInvestasi) ?></strong>
                    </div>
                    <div class="d-flex justify-content-between micro-text text-muted">
                        <span>Skema Bagi Hasil:</span>
                        <span class="badge bg-emerald-subtle text-emerald border border-emerald-subtle">Sesuai Saham</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Category Breakdown Donut Charts -->
<div class="row g-2 mb-3">
    <!-- Expense Breakdown -->
    <div class="col-12 col-lg-6">
        <div class="card-custom">
            <div class="card-header-compact">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart text-danger"></i>
                    <span class="fw-bold" style="font-size: 12px;">Komposisi Pengeluaran Operasional</span>
                </div>
                <span class="badge bg-rose-subtle text-rose border border-rose-subtle" style="font-size: 10px;">Outflow</span>
            </div>
            <div class="card-body-compact">
                <div id="chartExpenseCategories" style="min-height: 220px;"></div>
            </div>
        </div>
    </div>

    <!-- Income Breakdown -->
    <div class="col-12 col-lg-6">
        <div class="card-custom">
            <div class="card-header-compact">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-pie-chart text-success"></i>
                    <span class="fw-bold" style="font-size: 12px;">Komposisi Sumber Pemasukan</span>
                </div>
                <span class="badge bg-emerald-subtle text-emerald border border-emerald-subtle" style="font-size: 10px;">Inflow</span>
            </div>
            <div class="card-body-compact">
                <div id="chartIncomeCategories" style="min-height: 220px;"></div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions & Bank Accounts -->
<div class="row g-2">
    <!-- Recent Transactions Table -->
    <div class="col-12 col-xl-8">
        <div class="card-custom">
            <div class="card-header-compact">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-clock-history text-secondary"></i>
                    <span class="fw-bold" style="font-size: 12px;">Transaksi Keuangan Terkini</span>
                </div>
                <a href="<?= base_url('transactions') ?>" class="micro-text text-emerald fw-semibold text-decoration-none">
                    Lihat Semua Transaksi (<?= count($recentTransactions) ?>) &rarr;
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-compact align-middle">
                    <thead>
                        <tr>
                            <th>No. Transaksi</th>
                            <th>Tanggal</th>
                            <th>Tipe & Kategori</th>
                            <th>Uraian / Judul</th>
                            <th>Rekening Kas</th>
                            <th class="text-end">Nominal</th>
                            <th class="text-center">Bukti</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($recentTransactions)): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4 text-muted">Belum ada transaksi yang dicatat.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($recentTransactions as $tx): ?>
                                <tr>
                                    <td>
                                        <span class="fw-bold text-dark font-monospace"><?= esc($tx['nomor_transaksi']) ?></span>
                                    </td>
                                    <td class="text-muted" style="white-space: nowrap;">
                                        <?= date('d/m/Y', strtotime($tx['tanggal'])) ?>
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center gap-1">
                                            <?= format_badge_tipe($tx['tipe']) ?>
                                            <span class="badge border" style="background-color: #f1f5f9; color: #334155; font-size: 9.5px;">
                                                <i class="bi <?= esc($tx['kategori_icon'] ?? 'bi-tag') ?> me-1"></i>
                                                <?= esc($tx['nama_kategori'] ?? 'Umum') ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark" style="max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= esc($tx['judul']) ?>">
                                            <?= esc($tx['judul']) ?>
                                        </div>
                                        <?php if (!empty($tx['nama_investor'])): ?>
                                            <div class="micro-text text-emerald">
                                                <i class="bi bi-person-fill"></i> <?= esc($tx['nama_investor']) ?>
                                            </div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="text-secondary small"><?= esc($tx['nama_akun'] ?? '-') ?></span>
                                    </td>
                                    <td class="text-end fw-bold <?= ($tx['tipe'] === 'pemasukan') ? 'text-success' : 'text-danger' ?>">
                                        <?= ($tx['tipe'] === 'pemasukan' ? '+' : '-') ?> <?= format_rupiah($tx['jumlah']) ?>
                                    </td>
                                    <td class="text-center">
                                        <?php if (!empty($tx['bukti_transaksi'])): ?>
                                            <img src="<?= base_url('uploads/bukti/' . $tx['bukti_transaksi']) ?>" 
                                                 alt="Bukti Nota" 
                                                 class="receipt-thumb"
                                                 title="Klik untuk melihat bukti transaksi"
                                                 onclick="openReceiptModal('<?= base_url('uploads/bukti/' . $tx['bukti_transaksi']) ?>', '<?= esc($tx['nomor_transaksi']) ?> - <?= esc($tx['judul']) ?>', 'Nominal: <?= format_rupiah($tx['jumlah']) ?> • Tanggal: <?= date('d M Y', strtotime($tx['tanggal'])) ?>')">
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
    </div>

    <!-- Kas Accounts Summary -->
    <div class="col-12 col-xl-4">
        <div class="card-custom">
            <div class="card-header-compact">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-wallet2 text-emerald"></i>
                    <span class="fw-bold" style="font-size: 12px;">Posisi Kas & Rekening Bank</span>
                </div>
                <a href="<?= base_url('kas-accounts') ?>" class="micro-text text-emerald fw-semibold text-decoration-none">Detail &rarr;</a>
            </div>
            <div class="card-body-compact p-0">
                <ul class="list-group list-group-flush" style="font-size: 11.5px;">
                    <?php foreach ($kasAccounts as $kas): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-3 py-2 border-light">
                            <div>
                                <div class="fw-semibold text-dark"><?= esc($kas['nama_akun']) ?></div>
                                <div class="micro-text text-muted"><?= esc($kas['nomor_akun'] ?: 'Rekening Utama') ?></div>
                            </div>
                            <div class="text-end">
                                <div class="fw-bold text-dark"><?= format_rupiah($kas['saldo_saat_ini']) ?></div>
                                <span class="badge bg-emerald-subtle text-emerald micro-text py-0">Aktif</span>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <div class="p-3 bg-light border-top d-flex justify-content-between align-items-center">
                    <span class="fw-bold" style="font-size: 11px;">Total Likuiditas Kas:</span>
                    <span class="fw-extrabold text-emerald fs-6"><?= format_rupiah($totalSaldoKas) ?></span>
                </div>
                <div class="p-2 bg-white border-top text-center">
                    <button type="button" class="btn btn-outline-success btn-compact-xs w-100 d-flex align-items-center justify-content-center gap-1" data-bs-toggle="modal" data-bs-target="#modalKirimWaRecap">
                        <i class="bi bi-whatsapp"></i>
                        <span>Kirim Rekap Kas ke Grup WA</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal Kirim Laporan Rekap ke Grup WhatsApp -->
<div class="modal fade" id="modalKirimWaRecap" tabindex="-1" aria-labelledby="modalKirimWaRecapLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow">
            <div class="modal-header bg-success text-white py-2 px-3">
                <h6 class="modal-title fw-bold d-flex align-items-center gap-2" id="modalKirimWaRecapLabel" style="font-size: 13px;">
                    <i class="bi bi-whatsapp"></i>
                    <span>Kirim Laporan Rekap Keuangan ke Grup WA</span>
                </h6>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="<?= base_url('dashboard/send-wa-recap') ?>" method="POST" id="formKirimWaRecap">
                <?= csrf_field() ?>
                <div class="modal-body p-3">
                    <?php if (!empty($waReady)): ?>
                        <div class="p-2 mb-3 rounded bg-success-subtle border border-success-subtle d-flex align-items-center gap-2" style="font-size: 11.5px; color: #065f46;">
                            <i class="bi bi-check-circle-fill text-success fs-5 flex-shrink-0"></i>
                            <div>
                                <span class="fw-bold d-block">WhatsApp Gateway Siap & Terhubung</span>
                                <span class="micro-text text-muted">Laporan akan langsung dikirim ke Grup ID: <code><?= esc($waGroupId ?: 'Grup Terdaftar') ?></code></span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="p-2 mb-3 rounded bg-warning-subtle border border-warning-subtle d-flex align-items-center gap-2" style="font-size: 11.5px; color: #92400e;">
                            <i class="bi bi-exclamation-triangle-fill text-warning fs-5 flex-shrink-0"></i>
                            <div>
                                <span class="fw-bold d-block">WhatsApp Gateway Belum Dikonfigurasi</span>
                                <span class="micro-text text-muted">Silakan atur API Key CloudChat & Group ID di <a href="<?= base_url('settings/whatsapp') ?>" class="text-decoration-underline fw-bold">Pengaturan WA</a> terlebih dahulu.</span>
                            </div>
                        </div>
                    <?php endif; ?>

                    <label class="form-label fw-bold text-dark mb-2" style="font-size: 11.5px;">
                        Pilih Cakupan Laporan Rekap:
                    </label>

                    <div class="d-flex flex-column gap-2 mb-3">
                        <label class="p-2 rounded border bg-light d-flex align-items-start gap-2 cursor-pointer" style="cursor: pointer;">
                            <input type="radio" name="period" value="today" checked class="form-check-input mt-1">
                            <div>
                                <span class="fw-bold text-dark d-block" style="font-size: 12px;">📊 Rekap Keuangan Hari Ini (<?= date('d M Y') ?>)</span>
                                <span class="text-muted micro-text">Arus kas hari ini, transaksi terbaru, dan posisi sisa saldo kas/bank saat ini.</span>
                            </div>
                        </label>

                        <label class="p-2 rounded border bg-light d-flex align-items-start gap-2 cursor-pointer" style="cursor: pointer;">
                            <input type="radio" name="period" value="month" class="form-check-input mt-1">
                            <div>
                                <span class="fw-bold text-dark d-block" style="font-size: 12px;">🗓️ Rekap Bulan Berjalan (<?= date('F Y') ?>)</span>
                                <span class="text-muted micro-text">Total akumulasi pemasukan, pengeluaran, laba bersih bulan ini, serta sisa kas.</span>
                            </div>
                        </label>

                        <label class="p-2 rounded border bg-light d-flex align-items-start gap-2 cursor-pointer" style="cursor: pointer;">
                            <input type="radio" name="period" value="all" class="form-check-input mt-1">
                            <div>
                                <span class="fw-bold text-dark d-block" style="font-size: 12px;">💎 Rekap Finansial Eksekutif Kumulatif</span>
                                <span class="text-muted micro-text">Semua data arus kas, modal investor aktif, dividen cair, dan seluruh kas/bank.</span>
                            </div>
                        </label>
                    </div>

                    <div class="mb-2">
                        <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 11px;">
                            Pesan / Catatan Pengantar Tambahan (Opsional)
                        </label>
                        <textarea name="custom_note" rows="2" class="form-control" style="font-size: 11.5px;" placeholder="Contoh: Halo manajemen & tim, berikut rekap laporan kas sore ini..."></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light py-2 px-3 d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-secondary btn-compact" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-success btn-compact d-flex align-items-center gap-1 px-3" id="btnSubmitWaRecap">
                        <i class="bi bi-send-fill"></i>
                        <span>Kirim Sekarang ke Grup WA</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    // Initialize quick modal datasets
    initQuickModalData(
        <?= json_encode($expenseCategories + $incomeCategories) ?>,
        <?= json_encode($kasAccounts) ?>,
        <?= json_encode($investors) ?>
    );

    // 1. Chart: Monthly Cashflow Area Line
    const monthlyLabels = <?= $monthlyLabels ?>;
    const monthlyInflow = <?= $monthlyInflow ?>;
    const monthlyOutflow = <?= $monthlyOutflow ?>;
    const monthlyNet = <?= $monthlyNet ?>;

    const optionsCashFlow = {
        series: [
            {
                name: 'Pemasukan (Inflow)',
                data: monthlyInflow
            },
            {
                name: 'Pengeluaran (Outflow)',
                data: monthlyOutflow
            },
            {
                name: 'Laba Bersih (Net Cashflow)',
                data: monthlyNet
            }
        ],
        chart: {
            height: 290,
            type: 'area',
            fontFamily: 'Plus Jakarta Sans, sans-serif',
            toolbar: {
                show: true,
                tools: { download: true, selection: false, zoom: false, zoomin: false, zoomout: false, pan: false, reset: false }
            }
        },
        colors: ['#10b981', '#f43f5e', '#3b82f6'],
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: [2.5, 2.5, 2] },
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.35,
                opacityTo: 0.05,
                stops: [0, 90, 100]
            }
        },
        xaxis: {
            categories: monthlyLabels,
            labels: { style: { fontSize: '11px', colors: '#64748b' } }
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    if (Math.abs(value) >= 1000000) {
                        return (value / 1000000).toFixed(0) + ' Jt';
                    }
                    return value.toLocaleString('id-ID');
                },
                style: { fontSize: '10.5px', colors: '#64748b' }
            }
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return formatRupiahJS(val);
                }
            }
        },
        grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
        legend: { position: 'top', fontSize: '11px', fontWeight: 600, labels: { colors: '#475569' } }
    };

    const chartCashFlow = new ApexCharts(document.querySelector("#chartCashFlow"), optionsCashFlow);
    chartCashFlow.render();

    // 2. Chart: Investor Share Donut
    const investorNames = <?= json_encode(array_column($investors, 'nama_investor')) ?>;
    const investorShares = <?= json_encode(array_map('floatval', array_column($investors, 'persentase_bagi_hasil'))) ?>;

    const optionsInvestor = {
        series: investorShares,
        labels: investorNames,
        chart: {
            type: 'donut',
            height: 220,
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        colors: ['#059669', '#3b82f6', '#8b5cf6', '#f59e0b', '#06b6d4'],
        dataLabels: { enabled: true, formatter: function(val) { return val.toFixed(1) + "%"; } },
        legend: { position: 'bottom', fontSize: '10.5px' },
        tooltip: {
            y: {
                formatter: function(val) {
                    return val + "% Share";
                }
            }
        }
    };

    const chartInvestor = new ApexCharts(document.querySelector("#chartInvestorShare"), optionsInvestor);
    chartInvestor.render();

    // 3. Chart: Expense Categories Donut
    const expenseData = <?= json_encode($expenseCategories) ?>;
    const expenseLabels = expenseData.map(e => e.nama_kategori);
    const expenseTotals = expenseData.map(e => Number(e.total));
    const expenseColors = expenseData.map(e => e.warna || '#ef4444');

    const optionsExpense = {
        series: expenseTotals,
        labels: expenseLabels,
        chart: {
            type: 'donut',
            height: 230,
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        colors: expenseColors.length ? expenseColors : ['#ef4444', '#f97316', '#ec4899', '#6366f1', '#84cc16'],
        legend: { position: 'right', fontSize: '10.5px' },
        tooltip: {
            y: {
                formatter: function(val) {
                    return formatRupiahJS(val);
                }
            }
        }
    };

    const chartExpense = new ApexCharts(document.querySelector("#chartExpenseCategories"), optionsExpense);
    chartExpense.render();

    // 4. Chart: Income Categories Donut
    const incomeData = <?= json_encode($incomeCategories) ?>;
    const incomeLabels = incomeData.map(e => e.nama_kategori);
    const incomeTotals = incomeData.map(e => Number(e.total));
    const incomeColors = incomeData.map(e => e.warna || '#10b981');

    const optionsIncome = {
        series: incomeTotals,
        labels: incomeLabels,
        chart: {
            type: 'donut',
            height: 230,
            fontFamily: 'Plus Jakarta Sans, sans-serif'
        },
        colors: incomeColors.length ? incomeColors : ['#10b981', '#06b6d4', '#3b82f6', '#8b5cf6', '#059669'],
        legend: { position: 'right', fontSize: '10.5px' },
        tooltip: {
            y: {
                formatter: function(val) {
                    return formatRupiahJS(val);
                }
            }
        }
    };

    const chartIncome = new ApexCharts(document.querySelector("#chartIncomeCategories"), optionsIncome);
    chartIncome.render();
</script>
<?= $this->endSection() ?>
