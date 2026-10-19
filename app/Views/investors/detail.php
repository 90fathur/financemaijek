<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Breadcrumb & Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <a href="<?= base_url('investors') ?>" class="btn btn-outline-secondary btn-compact-xs">
                <i class="bi bi-arrow-left"></i> Kembali ke Daftar Investor
            </a>
            <span class="badge bg-light text-dark border font-monospace"><?= esc($investor['kode_investor']) ?></span>
            <span class="badge bg-emerald-subtle text-emerald border border-emerald-subtle text-uppercase"><?= esc($investor['status']) ?></span>
        </div>
        <h1 class="h5 mb-0 fw-bold text-dark">
            Portofolio Investasi: <?= esc($investor['nama_investor']) ?>
        </h1>
        <p class="text-muted small mb-0">Rincian modal penyertaan, riwayat suntikan dana, dan rekapitulasi pencairan dividen.</p>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-emerald btn-compact" data-bs-toggle="modal" data-bs-target="#modalSuntikModal">
            <i class="bi bi-cash-stack"></i> + Tambah Suntikan Modal
        </button>
    </div>
</div>

<!-- Profile & KPI Summary Row -->
<div class="row g-2 mb-3">
    <!-- Profile Card -->
    <div class="col-12 col-xl-4">
        <div class="card-custom h-100">
            <div class="card-header-compact">
                <span class="fw-bold" style="font-size: 12px;"><i class="bi bi-person-lines-fill text-emerald me-1"></i> Informasi Investor</span>
            </div>
            <div class="card-body-compact">
                <div class="d-flex align-items-center gap-3 mb-3 pb-3 border-bottom">
                    <div class="rounded-circle bg-emerald text-white d-flex align-items-center justify-content-center fw-bold fs-4" style="width: 48px; height: 48px;">
                        <?= strtoupper(substr($investor['nama_investor'], 0, 1)) ?>
                    </div>
                    <div>
                        <div class="fw-bold text-dark" style="font-size: 14px;"><?= esc($investor['nama_investor']) ?></div>
                        <div class="micro-text text-muted"><?= esc($investor['email'] ?: 'Email tidak tercatat') ?></div>
                        <div class="micro-text text-muted">WhatsApp: <?= esc($investor['no_telepon'] ?: '-') ?></div>
                    </div>
                </div>

                <div class="mb-2">
                    <div class="micro-text text-muted">Rekening Pembayaran Dividen:</div>
                    <div class="fw-semibold text-dark small mt-0.5">
                        <i class="bi bi-bank text-primary me-1"></i>
                        <?= esc($investor['nama_bank'] ?: '-') ?> - <span class="font-monospace"><?= esc($investor['nomor_rekening'] ?: '-') ?></span>
                    </div>
                    <div class="micro-text text-muted">a.n <?= esc($investor['pemilik_rekening'] ?: '-') ?></div>
                </div>

                <div class="mb-2">
                    <div class="micro-text text-muted">Tanggal Mulai Bergabung:</div>
                    <div class="fw-semibold text-dark small"><?= format_tanggal_indo($investor['tanggal_bergabung'], true) ?></div>
                </div>

                <?php if (!empty($investor['keterangan'])): ?>
                    <div class="p-2 bg-light rounded micro-text text-secondary mt-2 border">
                        <i class="bi bi-info-circle me-1 text-emerald"></i> <?= esc($investor['keterangan']) ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Financial Stats -->
    <div class="col-12 col-xl-8">
        <div class="row g-2">
            <div class="col-12 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-title">Total Modal Aktif</div>
                    <div class="kpi-value text-emerald"><?= format_rupiah($investor['total_investasi']) ?></div>
                    <div class="micro-text text-muted mt-1">Saldo modal yang sedang berputar di Maijek</div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-title">Porsi Bagi Hasil Saham</div>
                    <div class="kpi-value text-primary"><?= number_format($investor['persentase_bagi_hasil'], 2) ?>%</div>
                    <div class="micro-text text-muted mt-1">Dari total laba bersih yang dibagikan</div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-title">Akumulasi Dividen Diterima</div>
                    <div class="kpi-value text-amber"><?= format_rupiah($totalDividen) ?></div>
                    <div class="micro-text text-muted mt-1">Total pencairan dividen historis</div>
                </div>
            </div>
            <div class="col-12 col-sm-6">
                <div class="kpi-card">
                    <div class="kpi-title">Realized ROI (Return on Investment)</div>
                    <div class="kpi-value <?= ($roiRealized >= 10) ? 'text-success' : 'text-dark' ?>">
                        <?= number_format($roiRealized, 2) ?>%
                    </div>
                    <div class="micro-text text-muted mt-1">Rasio dividen terhadap modal awal</div>
                </div>
            </div>
        </div>

        <!-- Quick Summary Box -->
        <div class="card-custom mt-2">
            <div class="card-body-compact d-flex justify-content-between align-items-center">
                <div>
                    <span class="micro-text text-muted">Total Riwayat Log Investasi:</span>
                    <div class="fw-bold text-dark"><?= count($logs) ?> Transaksi Log Tercatat</div>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-emerald btn-compact-xs" data-bs-toggle="modal" data-bs-target="#modalSuntikModal">
                        <i class="bi bi-plus-circle"></i> Suntik Modal
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- History Log Table -->
<div class="card-custom">
    <div class="card-header-compact">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-clock-history text-secondary"></i>
            <span class="fw-bold" style="font-size: 12px;">Riwayat Mutasi Modal & Pencairan Dividen</span>
        </div>
        <span class="micro-text text-muted">Daftar lengkap suntikan modal dan dividen</span>
    </div>
    <div class="table-responsive">
        <table class="table table-compact align-middle table-hover">
            <thead>
                <tr>
                    <th>Tanggal</th>
                    <th>Tipe Mutasi</th>
                    <th>Periode Dividen</th>
                    <th>Uraian / Keterangan</th>
                    <th>No. Transaksi Kas</th>
                    <th class="text-end">Nominal</th>
                    <th class="text-center">Bukti</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($logs)): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">Belum ada riwayat mutasi untuk investor ini.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($logs as $log): ?>
                        <tr>
                            <td style="white-space: nowrap;">
                                <?= date('d/m/Y', strtotime($log['tanggal'])) ?>
                            </td>
                            <td>
                                <?php if ($log['tipe'] === 'suntikan_modal'): ?>
                                    <span class="badge bg-emerald-subtle text-emerald border border-emerald-subtle">
                                        <i class="bi bi-arrow-down-left me-1"></i>Suntikan Modal
                                    </span>
                                <?php elseif ($log['tipe'] === 'dividen_cair'): ?>
                                    <span class="badge bg-amber-subtle text-amber border border-amber-subtle">
                                        <i class="bi bi-coin me-1"></i>Dividen Cair
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-rose-subtle text-rose border border-rose-subtle">
                                        <i class="bi bi-arrow-up-right me-1"></i>Penarikan Modal
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="fw-semibold text-dark small"><?= esc($log['periode_dividen'] ?: '-') ?></span>
                            </td>
                            <td>
                                <div class="text-dark"><?= esc($log['keterangan'] ?: '-') ?></div>
                            </td>
                            <td>
                                <?php if (!empty($log['nomor_transaksi'])): ?>
                                    <span class="badge bg-light text-dark border font-monospace"><?= esc($log['nomor_transaksi']) ?></span>
                                <?php else: ?>
                                    <span class="micro-text text-muted">-</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-end fw-bold <?= ($log['tipe'] === 'suntikan_modal') ? 'text-success' : 'text-dark' ?>">
                                <?= format_rupiah($log['jumlah']) ?>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($log['bukti'])): ?>
                                    <img src="<?= base_url('uploads/bukti/' . $log['bukti']) ?>" 
                                         alt="Bukti" 
                                         class="receipt-thumb"
                                         title="Klik untuk zoom bukti transfer"
                                         onclick="openReceiptModal('<?= base_url('uploads/bukti/' . $log['bukti']) ?>', 'Bukti Mutasi - <?= esc($investor['nama_investor']) ?>', 'Nominal: <?= format_rupiah($log['jumlah']) ?> • <?= date('d M Y', strtotime($log['tanggal'])) ?>')">
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

<!-- Modal: Suntik Modal Tambahan -->
<div class="modal fade" id="modalSuntikModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= base_url('investors/add-capital/' . $investor['id']) ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header py-2 px-3 bg-emerald text-white">
                    <h6 class="modal-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-cash-stack"></i>
                        <span>Suntik Modal Investasi Tambahan</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="p-2 mb-3 rounded bg-light border micro-text">
                        Investor: <strong><?= esc($investor['nama_investor']) ?> (<?= esc($investor['kode_investor']) ?>)</strong><br>
                        Modal Saat Ini: <span class="text-emerald fw-bold"><?= format_rupiah($investor['total_investasi']) ?></span>
                    </div>

                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label-compact">Nominal Suntikan Modal (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" name="jumlah" class="form-control form-control-sm" placeholder="Contoh: 25000000" min="100000" step="1000" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Tanggal Penyerahan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Rekening Penampung Kas <span class="text-danger">*</span></label>
                            <select name="kas_account_id" class="form-select form-select-sm" required>
                                <?php foreach ($kasAccounts as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= esc($k['nama_akun']) ?> (<?= format_rupiah($k['saldo_saat_ini']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-compact">Keterangan / Tujuan Modal</label>
                            <textarea name="keterangan" rows="2" class="form-control form-control-sm" placeholder="Contoh: Penambahan modal armada pengadaan atribut dan ekspansi regional"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label-compact">Upload Bukti Transfer / Kwitansi</label>
                            <input type="file" name="bukti_transaksi" class="form-control form-control-sm" accept="image/*,application/pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-compact" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-emerald btn-compact">
                        <i class="bi bi-save-fill"></i> Catat Suntikan Modal
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
