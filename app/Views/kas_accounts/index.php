<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
    <div>
        <h1 class="h5 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-wallet2 text-emerald"></i>
            <span>Akun Kas & Rekening Bank Maijek</span>
        </h1>
        <p class="text-muted small mb-0">Kelola buku rekening bank penampung, kas operasional, dan saldo likuiditas real-time.</p>
    </div>
    <div>
        <button type="button" class="btn btn-emerald btn-compact" data-bs-toggle="modal" data-bs-target="#modalTambahKas">
            <i class="bi bi-plus-lg"></i> + Tambah Akun Kas / Bank
        </button>
    </div>
</div>

<!-- Total Likuiditas Banner -->
<div class="card-custom mb-3 bg-white p-3 border-emerald-subtle">
    <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-2">
        <div>
            <div class="micro-text text-muted text-uppercase fw-bold">Total Akumulasi Likuiditas Kas & Bank</div>
            <div class="h4 fw-extrabold text-emerald mb-0"><?= format_rupiah($totalSemuaKas) ?></div>
        </div>
        <div class="d-flex align-items-center gap-2 text-muted micro-text">
            <i class="bi bi-shield-check text-success fs-5"></i>
            <span>Tersinkronisasi otomatis dengan seluruh transaksi arus kas masuk & keluar</span>
        </div>
    </div>
</div>

<!-- Kas Accounts Table Card -->
<div class="card-custom">
    <div class="card-header-compact">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-bank text-secondary"></i>
            <span class="fw-bold" style="font-size: 12px;">Daftar Rekening Kas & Bank Terdaftar (<?= count($accounts) ?> Akun)</span>
        </div>
        <span class="micro-text text-muted">Real-time ledger balance</span>
    </div>
    <div class="table-responsive">
        <table class="table table-compact align-middle table-hover">
            <thead>
                <tr>
                    <th>Nama Akun / Bank</th>
                    <th>Nomor Rekening</th>
                    <th class="text-end">Saldo Awal</th>
                    <th class="text-end">Saldo Saat Ini</th>
                    <th>Peruntukan / Keterangan</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($accounts)): ?>
                    <tr>
                        <td colspan="6" class="text-center py-4 text-muted">Belum ada akun kas.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($accounts as $acc): ?>
                        <tr>
                            <td>
                                <div class="fw-bold text-dark d-flex align-items-center gap-2">
                                    <i class="bi bi-wallet-fill text-emerald"></i>
                                    <span><?= esc($acc['nama_akun']) ?></span>
                                </div>
                            </td>
                            <td>
                                <span class="font-monospace small text-dark"><?= esc($acc['nomor_akun'] ?: 'Kas Fisik') ?></span>
                            </td>
                            <td class="text-end text-muted small">
                                <?= format_rupiah($acc['saldo_awal']) ?>
                            </td>
                            <td class="text-end fw-extrabold text-emerald" style="font-size: 13px;">
                                <?= format_rupiah($acc['saldo_saat_ini']) ?>
                            </td>
                            <td class="micro-text text-muted" style="max-width: 250px;">
                                <?= esc($acc['keterangan'] ?: '-') ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?= base_url('kas-accounts/recalculate/' . $acc['id']) ?>" 
                                       class="btn btn-outline-success btn-compact-xs" 
                                       title="Sinkronkan / Hitung Ulang Saldo dari Riwayat Transaksi">
                                        <i class="bi bi-arrow-repeat"></i> Sinkron
                                    </a>
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-compact-xs" 
                                            title="Edit Akun" 
                                            onclick='openEditKasModal(<?= json_encode($acc) ?>)'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <a href="<?= base_url('kas-accounts/delete/' . $acc['id']) ?>" 
                                       class="btn btn-outline-danger btn-compact-xs" 
                                       title="Hapus Akun" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus akun <?= esc($acc['nama_akun']) ?>?')">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal: Tambah Akun Kas -->
<div class="modal fade" id="modalTambahKas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= base_url('kas-accounts/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header py-2 px-3 bg-emerald text-white">
                    <h6 class="modal-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-wallet-fill"></i>
                        <span>Tambah Akun Kas / Bank Baru</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label-compact">Nama Akun / Bank <span class="text-danger">*</span></label>
                            <input type="text" name="nama_akun" class="form-control form-control-sm" placeholder="Contoh: Bank BCA Bisnis Maijek / Kas Tunai Petty Cash" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Nomor Rekening / Akun</label>
                            <input type="text" name="nomor_akun" class="form-control form-control-sm" placeholder="Contoh: 8219082311">
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Saldo Awal (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" name="saldo_awal" class="form-control form-control-sm" placeholder="0" min="0" step="1000" value="0" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label-compact">Peruntukan / Keterangan</label>
                            <textarea name="keterangan" rows="2" class="form-control form-control-sm" placeholder="Contoh: Rekening utama penerimaan bagi hasil driver dan resto"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-compact" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-emerald btn-compact">
                        <i class="bi bi-save-fill"></i> Simpan Akun Kas
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Akun Kas -->
<div class="modal fade" id="modalEditKas" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg">
            <form id="formEditKas" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header py-2 px-3 bg-dark text-white">
                    <h6 class="modal-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-emerald"></i>
                        <span>Edit Akun Kas / Bank</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <div class="col-12">
                            <label class="form-label-compact">Nama Akun / Bank <span class="text-danger">*</span></label>
                            <input type="text" name="nama_akun" id="edit_nama_akun" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Nomor Rekening</label>
                            <input type="text" name="nomor_akun" id="edit_nomor_akun" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Saldo Awal (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" name="saldo_awal" id="edit_saldo_awal" class="form-control form-control-sm" min="0" step="1000" required>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label-compact">Keterangan</label>
                            <textarea name="keterangan" id="edit_kas_keterangan" rows="2" class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-compact" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-compact">
                        <i class="bi bi-check2-circle"></i> Perbarui Akun
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function openEditKasModal(acc) {
        document.getElementById('formEditKas').action = '<?= base_url('kas-accounts/update/') ?>' + acc.id;
        document.getElementById('edit_nama_akun').value = acc.nama_akun;
        document.getElementById('edit_nomor_akun').value = acc.nomor_akun || '';
        document.getElementById('edit_saldo_awal').value = acc.saldo_awal;
        document.getElementById('edit_kas_keterangan').value = acc.keterangan || '';

        const modal = new bootstrap.Modal(document.getElementById('modalEditKas'));
        modal.show();
    }
</script>
<?= $this->endSection() ?>
