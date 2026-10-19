<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
    <div>
        <h1 class="h5 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-tags-fill text-emerald"></i>
            <span>Manajemen Kategori Keuangan Dinamis</span>
        </h1>
        <p class="text-muted small mb-0">Atur dan sesuaikan pos pemasukan dan pengeluaran operasional Maijek secara dinamis.</p>
    </div>
    <div>
        <button type="button" class="btn btn-emerald btn-compact" data-bs-toggle="modal" data-bs-target="#modalTambahKategori">
            <i class="bi bi-plus-lg"></i> + Tambah Kategori Baru
        </button>
    </div>
</div>

<!-- Category Tabs & Tables -->
<div class="row g-2">
    <!-- Pemasukan (Income Categories) -->
    <div class="col-12 col-lg-6">
        <div class="card-custom h-100">
            <div class="card-header-compact">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-emerald-subtle text-emerald border border-emerald-subtle"><i class="bi bi-arrow-down-left me-1"></i>Pemasukan</span>
                    <span class="fw-bold" style="font-size: 12px;">Pos Penerimaan Arus Kas (<?= count($incomeCategories) ?> Kategori)</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-compact align-middle table-hover">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Kategori</th>
                            <th>Icon & Warna</th>
                            <th>Deskripsi Pos</th>
                            <th class="text-center" style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($incomeCategories)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada kategori pemasukan.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($incomeCategories as $c): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace" style="font-size: 10px;">
                                            <?= esc($c['kode_kategori'] ?: '-') ?>
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        <?= esc($c['nama_kategori']) ?>
                                    </td>
                                    <td>
                                        <span class="badge d-inline-flex align-items-center gap-1 border" style="background-color: #ffffff; color: #1e293b; border-color: #cbd5e1;">
                                            <i class="bi <?= esc($c['icon'] ?: 'bi-tag') ?>" style="color: <?= esc($c['warna'] ?: '#10b981') ?>;"></i>
                                            <span style="font-size: 9.5px;"><?= esc($c['warna']) ?></span>
                                        </span>
                                    </td>
                                    <td class="micro-text text-muted" style="max-width: 180px;">
                                        <?= esc($c['deskripsi'] ?: '-') ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-outline-primary btn-compact-xs" title="Edit" onclick='openEditCatModal(<?= json_encode($c) ?>)'>
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="<?= base_url('categories/delete/' . $c['id']) ?>" 
                                               class="btn btn-outline-danger btn-compact-xs" 
                                               title="Hapus" 
                                               onclick="return confirm('Hapus kategori <?= esc($c['nama_kategori']) ?>?')">
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
    </div>

    <!-- Pengeluaran (Expense Categories) -->
    <div class="col-12 col-lg-6">
        <div class="card-custom h-100">
            <div class="card-header-compact">
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-rose-subtle text-rose border border-rose-subtle"><i class="bi bi-arrow-up-right me-1"></i>Pengeluaran</span>
                    <span class="fw-bold" style="font-size: 12px;">Pos Beban Operasional (<?= count($expenseCategories) ?> Kategori)</span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-compact align-middle table-hover">
                    <thead>
                        <tr>
                            <th>Kode</th>
                            <th>Nama Kategori</th>
                            <th>Icon & Warna</th>
                            <th>Deskripsi Pos</th>
                            <th class="text-center" style="width: 70px;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($expenseCategories)): ?>
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">Belum ada kategori pengeluaran.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($expenseCategories as $c): ?>
                                <tr>
                                    <td>
                                        <span class="badge bg-light text-dark border font-monospace" style="font-size: 10px;">
                                            <?= esc($c['kode_kategori'] ?: '-') ?>
                                        </span>
                                    </td>
                                    <td class="fw-semibold text-dark">
                                        <?= esc($c['nama_kategori']) ?>
                                    </td>
                                    <td>
                                        <span class="badge d-inline-flex align-items-center gap-1 border" style="background-color: #ffffff; color: #1e293b; border-color: #cbd5e1;">
                                            <i class="bi <?= esc($c['icon'] ?: 'bi-tag') ?>" style="color: <?= esc($c['warna'] ?: '#ef4444') ?>;"></i>
                                            <span style="font-size: 9.5px;"><?= esc($c['warna']) ?></span>
                                        </span>
                                    </td>
                                    <td class="micro-text text-muted" style="max-width: 180px;">
                                        <?= esc($c['deskripsi'] ?: '-') ?>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex justify-content-center gap-1">
                                            <button type="button" class="btn btn-outline-primary btn-compact-xs" title="Edit" onclick='openEditCatModal(<?= json_encode($c) ?>)'>
                                                <i class="bi bi-pencil-square"></i>
                                            </button>
                                            <a href="<?= base_url('categories/delete/' . $c['id']) ?>" 
                                               class="btn btn-outline-danger btn-compact-xs" 
                                               title="Hapus" 
                                               onclick="return confirm('Hapus kategori <?= esc($c['nama_kategori']) ?>?')">
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
    </div>
</div>

<!-- Modal: Tambah Kategori -->
<div class="modal fade" id="modalTambahKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= base_url('categories/store') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header py-2 px-3 bg-emerald text-white">
                    <h6 class="modal-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-tag-fill"></i>
                        <span>Tambah Kategori Keuangan Baru</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label-compact">Tipe Kategori <span class="text-danger">*</span></label>
                            <select name="tipe" class="form-select form-select-sm" required>
                                <option value="pemasukan">🟢 Pemasukan (Inflow)</option>
                                <option value="pengeluaran">🔴 Pengeluaran (Outflow)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Kode Singkat (Opsional)</label>
                            <input type="text" name="kode_kategori" class="form-control form-control-sm" placeholder="Contoh: IN-ADS / OUT-SERVER">
                        </div>
                        <div class="col-12">
                            <label class="form-label-compact">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" class="form-control form-control-sm" placeholder="Contoh: Google Maps API / Bagi Hasil Driver" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Pilih Icon (Bootstrap Icon)</label>
                            <select name="icon" class="form-select form-select-sm">
                                <option value="bi-tag">bi-tag (Tag Umum)</option>
                                <option value="bi-bicycle">bi-bicycle (Driver Ride)</option>
                                <option value="bi-shop">bi-shop (Merchant Resto)</option>
                                <option value="bi-box-seam">bi-box-seam (Platform/Titip)</option>
                                <option value="bi-wallet2">bi-wallet2 (Dompet/Topup)</option>
                                <option value="bi-hdd-network">bi-hdd-network (Server Cloud)</option>
                                <option value="bi-geo-alt">bi-geo-alt (Maps/Lokasi)</option>
                                <option value="bi-chat-dots">bi-chat-dots (WhatsApp/SMS)</option>
                                <option value="bi-people">bi-people (Gaji/Insentif)</option>
                                <option value="bi-gift">bi-gift (Merchandise/Helm)</option>
                                <option value="bi-coin">bi-coin (Dividen/Saham)</option>
                                <option value="bi-building">bi-building (Kantor/Ops)</option>
                                <option value="bi-tools">bi-tools (Maintenance)</option>
                                <option value="bi-megaphone">bi-megaphone (Iklan/Promo)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Warna Label / Chart <span class="text-danger">*</span></label>
                            <div class="d-flex align-items-center gap-2">
                                <input type="color" name="warna" class="form-control form-control-color form-control-sm" value="#10b981" title="Pilih warna label">
                                <span class="micro-text text-muted">Untuk visual chart</span>
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label-compact">Deskripsi Pos Kategori</label>
                            <textarea name="deskripsi" rows="2" class="form-control form-control-sm" placeholder="Rincian pos transaksi ini"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-compact" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-emerald btn-compact">
                        <i class="bi bi-save-fill"></i> Simpan Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Kategori -->
<div class="modal fade" id="modalEditKategori" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-md">
        <div class="modal-content border-0 shadow-lg">
            <form id="formEditCat" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header py-2 px-3 bg-dark text-white">
                    <h6 class="modal-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-emerald"></i>
                        <span>Edit Kategori Keuangan</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <div class="col-6">
                            <label class="form-label-compact">Nama Kategori <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kategori" id="edit_cat_nama" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Kode Singkat</label>
                            <input type="text" name="kode_kategori" id="edit_cat_kode" class="form-control form-control-sm">
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Icon</label>
                            <select name="icon" id="edit_cat_icon" class="form-select form-select-sm">
                                <option value="bi-tag">bi-tag</option>
                                <option value="bi-bicycle">bi-bicycle</option>
                                <option value="bi-shop">bi-shop</option>
                                <option value="bi-box-seam">bi-box-seam</option>
                                <option value="bi-wallet2">bi-wallet2</option>
                                <option value="bi-hdd-network">bi-hdd-network</option>
                                <option value="bi-geo-alt">bi-geo-alt</option>
                                <option value="bi-chat-dots">bi-chat-dots</option>
                                <option value="bi-people">bi-people</option>
                                <option value="bi-gift">bi-gift</option>
                                <option value="bi-coin">bi-coin</option>
                                <option value="bi-building">bi-building</option>
                                <option value="bi-tools">bi-tools</option>
                                <option value="bi-megaphone">bi-megaphone</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label-compact">Warna Label</label>
                            <input type="color" name="warna" id="edit_cat_warna" class="form-control form-control-color form-control-sm">
                        </div>
                        <div class="col-12">
                            <label class="form-label-compact">Deskripsi Pos</label>
                            <textarea name="deskripsi" id="edit_cat_deskripsi" rows="2" class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-compact" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-compact">
                        <i class="bi bi-check2-circle"></i> Perbarui Kategori
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    function openEditCatModal(cat) {
        document.getElementById('formEditCat').action = '<?= base_url('categories/update/') ?>' + cat.id;
        document.getElementById('edit_cat_nama').value = cat.nama_kategori;
        document.getElementById('edit_cat_kode').value = cat.kode_kategori || '';
        document.getElementById('edit_cat_icon').value = cat.icon || 'bi-tag';
        document.getElementById('edit_cat_warna').value = cat.warna || '#10b981';
        document.getElementById('edit_cat_deskripsi').value = cat.deskripsi || '';

        const modal = new bootstrap.Modal(document.getElementById('modalEditKategori'));
        modal.show();
    }
</script>
<?= $this->endSection() ?>
