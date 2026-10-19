<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
    <div>
        <h1 class="h5 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-journal-text text-emerald"></i>
            <span>Pencatatan Keuangan & Arus Transaksi</span>
        </h1>
        <p class="text-muted small mb-0">Kelola riwayat pemasukan, pengeluaran operasional, dan bukti nota/struk terverifikasi.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="<?= base_url('transactions/export-csv?' . http_build_query($filters)) ?>" class="btn btn-outline-secondary btn-compact">
            <i class="bi bi-file-earmark-excel"></i> Export CSV
        </a>
        <button type="button" class="btn btn-emerald btn-compact" data-bs-toggle="modal" data-bs-target="#modalTambahTransaksi">
            <i class="bi bi-plus-lg"></i> + Catat Transaksi Baru
        </button>
    </div>
</div>

<!-- Filter Box -->
<div class="card-custom mb-3">
    <div class="card-body-compact">
        <form action="<?= base_url('transactions') ?>" method="GET" class="row g-2 align-items-end">
            <div class="col-6 col-md-2">
                <label class="form-label-compact">Tipe Arus Kas</label>
                <select name="tipe" class="form-select form-select-sm">
                    <option value="">Semua Tipe</option>
                    <option value="pemasukan" <?= ($filters['tipe'] === 'pemasukan') ? 'selected' : '' ?>>🟢 Pemasukan (In)</option>
                    <option value="pengeluaran" <?= ($filters['tipe'] === 'pengeluaran') ? 'selected' : '' ?>>🔴 Pengeluaran (Out)</option>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label-compact">Kategori</label>
                <select name="category_id" class="form-select form-select-sm">
                    <option value="">Semua Kategori</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= ($filters['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                            [<?= strtoupper(substr($cat['tipe'], 0, 2)) ?>] <?= esc($cat['nama_kategori']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label-compact">Rekening Kas</label>
                <select name="kas_account_id" class="form-select form-select-sm">
                    <option value="">Semua Kas/Bank</option>
                    <?php foreach ($kasAccounts as $k): ?>
                        <option value="<?= $k['id'] ?>" <?= ($filters['kas_account_id'] == $k['id']) ? 'selected' : '' ?>>
                            <?= esc($k['nama_akun']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label-compact">Dari Tanggal</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="<?= esc($filters['start_date']) ?>">
            </div>
            <div class="col-6 col-md-2">
                <label class="form-label-compact">Sampai Tanggal</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="<?= esc($filters['end_date']) ?>">
            </div>
            <div class="col-12 col-md-2 d-flex gap-1">
                <button type="submit" class="btn btn-emerald btn-compact flex-grow-1">
                    <i class="bi bi-funnel-fill"></i> Filter
                </button>
                <a href="<?= base_url('transactions') ?>" class="btn btn-outline-secondary btn-compact" title="Reset Filter">
                    <i class="bi bi-arrow-counterclockwise"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Summary Strip for Filtered Data -->
<div class="row g-2 mb-3">
    <div class="col-12 col-md-4">
        <div class="p-2 px-3 rounded bg-emerald-subtle border border-emerald-subtle d-flex align-items-center justify-content-between">
            <div>
                <span class="micro-text text-emerald text-uppercase fw-bold">Total Pemasukan (Filter)</span>
                <div class="fw-bold text-success fs-6"><?= format_rupiah($totalMasuk) ?></div>
            </div>
            <i class="bi bi-arrow-down-left-circle-fill text-success fs-4"></i>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="p-2 px-3 rounded bg-rose-subtle border border-rose-subtle d-flex align-items-center justify-content-between">
            <div>
                <span class="micro-text text-rose text-uppercase fw-bold">Total Pengeluaran (Filter)</span>
                <div class="fw-bold text-danger fs-6"><?= format_rupiah($totalKeluar) ?></div>
            </div>
            <i class="bi bi-arrow-up-right-circle-fill text-danger fs-4"></i>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="p-2 px-3 rounded <?= ($netFlow >= 0) ? 'bg-blue-subtle border border-blue-subtle' : 'bg-amber-subtle border border-amber-subtle' ?> d-flex align-items-center justify-content-between">
            <div>
                <span class="micro-text text-dark text-uppercase fw-bold">Selisih Bersih (Net Flow)</span>
                <div class="fw-bold fs-6 <?= ($netFlow >= 0) ? 'text-primary' : 'text-danger' ?>">
                    <?= format_rupiah($netFlow) ?>
                </div>
            </div>
            <i class="bi bi-graph-up-arrow text-primary fs-4"></i>
        </div>
    </div>
</div>

<!-- Transactions Table Card -->
<div class="card-custom">
    <div class="card-header-compact">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-table text-secondary"></i>
            <span class="fw-bold" style="font-size: 12px;">Daftar Transaksi Keuangan (<?= count($transactions) ?> Data)</span>
        </div>
        <div class="micro-text text-muted">
            Menampilkan data terurut dari tanggal terbaru
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
                    <th>Uraian Transaksi</th>
                    <th>Rekening Kas</th>
                    <th class="text-end">Nominal</th>
                    <th class="text-center">Bukti Nota</th>
                    <th class="text-center" style="width: 80px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($transactions)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-2 text-muted d-block mb-2"></i>
                            Tidak ada transaksi yang cocok dengan kriteria filter Anda.
                        </td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($transactions as $tx): ?>
                        <tr>
                            <td>
                                <span class="fw-bold text-dark font-monospace"><?= esc($tx['nomor_transaksi']) ?></span>
                            </td>
                            <td style="white-space: nowrap;">
                                <?= date('d/m/Y', strtotime($tx['tanggal'])) ?>
                            </td>
                            <td>
                                <?= format_badge_tipe($tx['tipe']) ?>
                            </td>
                            <td>
                                <span class="badge border" style="background-color: #f8fafc; color: #334155; font-size: 9.5px; border-color: #cbd5e1;">
                                    <i class="bi <?= esc($tx['kategori_icon'] ?? 'bi-tag') ?> me-1" style="color: <?= esc($tx['kategori_warna'] ?? '#10b981') ?>;"></i>
                                    <?= esc($tx['nama_kategori'] ?? 'Umum') ?>
                                </span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark" style="max-width: 250px;">
                                    <?= esc($tx['judul']) ?>
                                </div>
                                <?php if (!empty($tx['keterangan'])): ?>
                                    <div class="micro-text text-muted text-truncate" style="max-width: 250px;" title="<?= esc($tx['keterangan']) ?>">
                                        <?= esc($tx['keterangan']) ?>
                                    </div>
                                <?php endif; ?>
                                <?php if (!empty($tx['nama_investor'])): ?>
                                    <div class="micro-text text-emerald mt-1">
                                        <i class="bi bi-person-fill"></i> Investor: <strong><?= esc($tx['nama_investor']) ?></strong> (<?= esc($tx['kode_investor']) ?>)
                                    </div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="text-secondary small"><?= esc($tx['nama_akun'] ?? '-') ?></span>
                            </td>
                            <td class="text-end fw-bold <?= ($tx['tipe'] === 'pemasukan') ? 'text-success' : 'text-danger' ?>" style="white-space: nowrap;">
                                <?= ($tx['tipe'] === 'pemasukan' ? '+' : '-') ?> <?= format_rupiah($tx['jumlah']) ?>
                            </td>
                            <td class="text-center">
                                <?php if (!empty($tx['bukti_transaksi'])): ?>
                                    <img src="<?= base_url('uploads/bukti/' . $tx['bukti_transaksi']) ?>" 
                                         alt="Bukti" 
                                         class="receipt-thumb" 
                                         title="Klik untuk zoom bukti nota"
                                         onclick="openReceiptModal('<?= base_url('uploads/bukti/' . $tx['bukti_transaksi']) ?>', '<?= esc($tx['nomor_transaksi']) ?> - <?= esc($tx['judul']) ?>', 'Nominal: <?= format_rupiah($tx['jumlah']) ?> • Tanggal: <?= date('d M Y', strtotime($tx['tanggal'])) ?>')">
                                <?php else: ?>
                                    <span class="micro-text text-muted fst-italic">Tanpa Bukti</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <button type="button" 
                                            class="btn btn-outline-primary btn-compact-xs" 
                                            title="Edit Transaksi"
                                            onclick='openEditTxModal(<?= json_encode($tx) ?>)'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <a href="<?= base_url('transactions/delete/' . $tx['id']) ?>" 
                                       class="btn btn-outline-danger btn-compact-xs" 
                                       title="Hapus Transaksi" 
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus transaksi <?= esc($tx['nomor_transaksi']) ?>?')">
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

<!-- Modal Tambah Transaksi -->
<div class="modal fade" id="modalTambahTransaksi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= base_url('transactions/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header py-2 px-3 bg-emerald text-white">
                    <h6 class="modal-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-plus-circle-fill"></i>
                        <span>Catat Transaksi Keuangan Baru</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label-compact">Tipe Transaksi <span class="text-danger">*</span></label>
                            <select name="tipe" id="tambah_tipe" class="form-select form-select-sm" required onchange="filterTambahCategories()">
                                <option value="pemasukan">🟢 Pemasukan (Inflow)</option>
                                <option value="pengeluaran">🔴 Pengeluaran (Outflow)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-compact">Judul / Uraian Transaksi <span class="text-danger">*</span></label>
                            <input type="text" name="judul" class="form-control form-control-sm" placeholder="Contoh: Pembayaran Server DigitalOcean / Setoran Mitra Driver" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" id="tambah_category_id" class="form-select form-select-sm" required>
                                <!-- JS Populated -->
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Rekening Kas / Bank <span class="text-danger">*</span></label>
                            <select name="kas_account_id" class="form-select form-select-sm" required>
                                <?php foreach ($kasAccounts as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= esc($k['nama_akun']) ?> (<?= format_rupiah($k['saldo_saat_ini']) ?>)</option>
                                <?php endforeach; ?>
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
                            <select name="investor_id" class="form-select form-select-sm">
                                <option value="">-- Bukan Transaksi Investor --</option>
                                <?php foreach ($investors as $i): ?>
                                    <option value="<?= $i['id'] ?>"><?= esc($i['nama_investor']) ?> (<?= esc($i['kode_investor']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-compact">Keterangan / Rincian Tambahan</label>
                            <textarea name="keterangan" rows="2" class="form-control form-control-sm" placeholder="Catatan invoice, penerima transfer, dll."></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label-compact">Upload Bukti Transaksi (Nota / Struk / Struk Bank)</label>
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

<!-- Modal Edit Transaksi -->
<div class="modal fade" id="modalEditTransaksi" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg">
            <form id="formEditTx" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header py-2 px-3 bg-dark text-white">
                    <h6 class="modal-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-emerald"></i>
                        <span>Edit Transaksi: <span id="edit_nomor_tx" class="text-emerald"></span></span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label-compact">Tipe Transaksi <span class="text-danger">*</span></label>
                            <select name="tipe" id="edit_tipe" class="form-select form-select-sm" required onchange="filterEditCategories()">
                                <option value="pemasukan">🟢 Pemasukan (Inflow)</option>
                                <option value="pengeluaran">🔴 Pengeluaran (Outflow)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Tanggal Transaksi <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" id="edit_tanggal" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label-compact">Judul / Uraian Transaksi <span class="text-danger">*</span></label>
                            <input type="text" name="judul" id="edit_judul" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Kategori <span class="text-danger">*</span></label>
                            <select name="category_id" id="edit_category_id" class="form-select form-select-sm" required>
                                <!-- JS Populated -->
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Rekening Kas / Bank <span class="text-danger">*</span></label>
                            <select name="kas_account_id" id="edit_kas_account_id" class="form-select form-select-sm" required>
                                <?php foreach ($kasAccounts as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= esc($k['nama_akun']) ?> (<?= format_rupiah($k['saldo_saat_ini']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Nominal (Rupiah) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light border-end-0 fw-bold">Rp</span>
                                <input type="number" name="jumlah" id="edit_jumlah" class="form-control form-control-sm" min="1000" step="1" required>
                            </div>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Investor Terkait (Opsional)</label>
                            <select name="investor_id" id="edit_investor_id" class="form-select form-select-sm">
                                <option value="">-- Bukan Transaksi Investor --</option>
                                <?php foreach ($investors as $i): ?>
                                    <option value="<?= $i['id'] ?>"><?= esc($i['nama_investor']) ?> (<?= esc($i['kode_investor']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-12">
                            <label class="form-label-compact">Keterangan / Rincian Tambahan</label>
                            <textarea name="keterangan" id="edit_keterangan" rows="2" class="form-control form-control-sm"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label-compact">Ganti Bukti Transaksi (Kosongkan jika tidak diubah)</label>
                            <input type="file" name="bukti_transaksi" class="form-control form-control-sm" accept="image/*,application/pdf">
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-compact" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-compact">
                        <i class="bi bi-check2-circle"></i> Perbarui Transaksi
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const categoriesData = <?= json_encode($categories) ?>;
    let currentSelectedEditCatId = null;

    function filterTambahCategories() {
        const tipe = document.getElementById('tambah_tipe').value;
        const select = document.getElementById('tambah_category_id');
        const filtered = categoriesData.filter(c => c.tipe === tipe);
        select.innerHTML = filtered.map(c => `<option value="${c.id}">${c.nama_kategori}</option>`).join('');
    }

    function filterEditCategories() {
        const tipe = document.getElementById('edit_tipe').value;
        const select = document.getElementById('edit_category_id');
        const filtered = categoriesData.filter(c => c.tipe === tipe);
        select.innerHTML = filtered.map(c => 
            `<option value="${c.id}" ${c.id == currentSelectedEditCatId ? 'selected' : ''}>${c.nama_kategori}</option>`
        ).join('');
    }

    function openEditTxModal(tx) {
        document.getElementById('formEditTx').action = '<?= base_url('transactions/update/') ?>' + tx.id;
        document.getElementById('edit_nomor_tx').textContent = tx.nomor_transaksi;
        document.getElementById('edit_tipe').value = tx.tipe;
        document.getElementById('edit_tanggal').value = tx.tanggal;
        document.getElementById('edit_judul').value = tx.judul;
        document.getElementById('edit_kas_account_id').value = tx.kas_account_id;
        document.getElementById('edit_jumlah').value = tx.jumlah;
        document.getElementById('edit_investor_id').value = tx.investor_id || '';
        document.getElementById('edit_keterangan').value = tx.keterangan || '';

        currentSelectedEditCatId = tx.category_id;
        filterEditCategories();

        const modal = new bootstrap.Modal(document.getElementById('modalEditTransaksi'));
        modal.show();
    }

    // Initialize initial dropdown
    filterTambahCategories();
</script>
<?= $this->endSection() ?>
