<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
    <div>
        <h1 class="h5 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-person-badge text-emerald"></i>
            <span>Monitoring Investasi & Portofolio Modal Maijek</span>
        </h1>
        <p class="text-muted small mb-0">Kelola kepemilikan modal, suntikan dana investasi, dan simulasi bagi hasil dividen dinamis.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <button type="button" class="btn btn-outline-warning text-dark fw-bold btn-compact" data-bs-toggle="modal" data-bs-target="#modalBagiHasilDividen">
            <i class="bi bi-calculator-fill text-warning"></i> Kalkulator & Bagi Hasil Dividen
        </button>
        <button type="button" class="btn btn-emerald btn-compact" data-bs-toggle="modal" data-bs-target="#modalTambahInvestor">
            <i class="bi bi-plus-lg"></i> + Daftarkan Investor Baru
        </button>
    </div>
</div>

<!-- KPI Cards Grid -->
<div class="row g-2 mb-3">
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-title">Total Modal Diinvestasikan</div>
            <div class="kpi-value text-emerald"><?= format_rupiah($totalInvestasiSemua) ?></div>
            <div class="micro-text text-muted mt-1"><i class="bi bi-check2-circle text-emerald"></i> 100% Tercatat Resmi</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-title">Investor Terdaftar</div>
            <div class="kpi-value text-dark"><?= count($investors) ?> Investor</div>
            <div class="micro-text text-muted mt-1"><i class="bi bi-pie-chart text-primary"></i> Total Share: <?= number_format($totalPersenSemua, 1) ?>%</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-title">Akumulasi Dividen Cair</div>
            <?php 
                $totalSemuaDividen = array_sum(array_column($investors, 'total_dividen'));
            ?>
            <div class="kpi-value text-amber"><?= format_rupiah($totalSemuaDividen) ?></div>
            <div class="micro-text text-muted mt-1"><i class="bi bi-wallet-fill text-warning"></i> Return to Investors</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="kpi-card">
            <div class="kpi-title">Rata-rata Realized ROI</div>
            <?php 
                $avgRoi = ($totalInvestasiSemua > 0) ? ($totalSemuaDividen / $totalInvestasiSemua) * 100 : 0;
            ?>
            <div class="kpi-value text-primary"><?= number_format($avgRoi, 2) ?>%</div>
            <div class="micro-text text-muted mt-1"><i class="bi bi-graph-up text-success"></i> Berdasarkan dividen cair</div>
        </div>
    </div>
</div>

<!-- Investors Cards & Table -->
<div class="card-custom mb-3">
    <div class="card-header-compact">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-people-fill text-emerald"></i>
            <span class="fw-bold" style="font-size: 12px;">Daftar Portofolio Investor & Kepemilikan Modal</span>
        </div>
        <span class="micro-text text-muted">Diurutkan berdasarkan modal terbesar</span>
    </div>
    <div class="table-responsive">
        <table class="table table-compact align-middle table-hover">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama Investor</th>
                    <th>Rekening Pembayaran</th>
                    <th class="text-end">Total Modal</th>
                    <th class="text-center" style="width: 140px;">Porsi Bagi Hasil</th>
                    <th class="text-end">Dividen Diterima</th>
                    <th class="text-center">ROI Cair</th>
                    <th class="text-center">Status</th>
                    <th class="text-center" style="width: 140px;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($investors)): ?>
                    <tr>
                        <td colspan="9" class="text-center py-4 text-muted">Belum ada investor yang didaftarkan.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($investors as $inv): ?>
                        <tr>
                            <td>
                                <span class="badge bg-light text-dark border font-monospace" style="font-size: 10.5px;">
                                    <?= esc($inv['kode_investor']) ?>
                                </span>
                            </td>
                            <td>
                                <a href="<?= base_url('investors/detail/' . $inv['id']) ?>" class="fw-bold text-dark text-decoration-none">
                                    <?= esc($inv['nama_investor']) ?>
                                </a>
                                <div class="micro-text text-muted">
                                    <?= esc($inv['email'] ?: '-') ?> • Telp: <?= esc($inv['no_telepon'] ?: '-') ?>
                                </div>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark small"><?= esc($inv['nama_bank'] ?: 'Belum diisi') ?></div>
                                <div class="micro-text text-muted font-monospace"><?= esc($inv['nomor_rekening'] ?: '-') ?> a.n <?= esc($inv['pemilik_rekening'] ?: '-') ?></div>
                            </td>
                            <td class="text-end fw-bold text-emerald">
                                <?= format_rupiah($inv['total_investasi']) ?>
                            </td>
                            <td>
                                <div class="d-flex align-items-center justify-content-between mb-1 micro-text">
                                    <span class="fw-bold text-dark"><?= number_format($inv['persentase_bagi_hasil'], 1) ?>%</span>
                                    <span class="text-muted">Saham/Bagi Hasil</span>
                                </div>
                                <div class="progress" style="height: 5px;">
                                    <div class="progress-bar bg-emerald" role="progressbar" style="width: <?= min(100, $inv['persentase_bagi_hasil']) ?>%"></div>
                                </div>
                            </td>
                            <td class="text-end fw-semibold text-amber">
                                <?= format_rupiah($inv['total_dividen']) ?>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-blue-subtle text-blue border border-blue-subtle" style="font-size: 10px;">
                                    <?= number_format($inv['roi_realized'], 1) ?>%
                                </span>
                            </td>
                            <td class="text-center">
                                <?php if ($inv['status'] === 'aktif'): ?>
                                    <span class="badge bg-emerald-subtle text-emerald border border-emerald-subtle" style="font-size: 10px;">
                                        <i class="bi bi-check-circle-fill me-1"></i>Aktif
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-secondary" style="font-size: 10px;">Nonaktif</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?= base_url('investors/detail/' . $inv['id']) ?>" class="btn btn-outline-success btn-compact-xs" title="Lihat Portofolio">
                                        <i class="bi bi-eye-fill"></i> Detail
                                    </a>
                                    <button type="button" class="btn btn-outline-primary btn-compact-xs" title="Edit Data" onclick='openEditInvestorModal(<?= json_encode($inv) ?>)'>
                                        <i class="bi bi-pencil-square"></i>
                                    </button>
                                    <a href="<?= base_url('investors/delete/' . $inv['id']) ?>" 
                                       class="btn btn-outline-danger btn-compact-xs" 
                                       title="Hapus/Nonaktifkan" 
                                       onclick="return confirm('Apakah Anda yakin ingin menonaktifkan investor <?= esc($inv['nama_investor']) ?>?')">
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

<!-- Modal: Daftarkan Investor Baru -->
<div class="modal fade" id="modalTambahInvestor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= base_url('investors/store') ?>" method="POST" enctype="multipart/form-data">
                <?= csrf_field() ?>
                <div class="modal-header py-2 px-3 bg-emerald text-white">
                    <h6 class="modal-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-person-plus-fill"></i>
                        <span>Daftarkan Investor Baru Maijek</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label-compact">Nama Lengkap Investor / Badan Usaha <span class="text-danger">*</span></label>
                            <input type="text" name="nama_investor" class="form-control form-control-sm" placeholder="Contoh: H. Baharuddin / PT Maju Bersama" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-compact">Email</label>
                            <input type="email" name="email" class="form-control form-control-sm" placeholder="investor@email.com">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-compact">Nomor WhatsApp / Telp</label>
                            <input type="text" name="no_telepon" class="form-control form-control-sm" placeholder="0812xxxxxxxx">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label-compact">Nama Bank Penampung</label>
                            <input type="text" name="nama_bank" class="form-control form-control-sm" placeholder="Contoh: Bank BNI / Bank Mandiri">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-compact">Nomor Rekening Bank</label>
                            <input type="text" name="nomor_rekening" class="form-control form-control-sm" placeholder="Nomor rekening">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-compact">Nama Pemilik Rekening</label>
                            <input type="text" name="pemilik_rekening" class="form-control form-control-sm" placeholder="Nama sesuai buku tabungan">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label-compact">Modal Investasi Awal (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" name="total_investasi" class="form-control form-control-sm" placeholder="0" min="0" step="1000" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-compact">Porsi Bagi Hasil (%) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="persentase_bagi_hasil" class="form-control form-control-sm" placeholder="Contoh: 25" min="0" max="100" step="0.01" required>
                                <span class="input-group-text bg-light fw-bold">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-compact">Tanggal Bergabung <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_bergabung" class="form-control form-control-sm" value="<?= date('Y-m-d') ?>" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label-compact">Rekening Penampung Kas Maijek</label>
                            <select name="kas_account_id" class="form-select form-select-sm">
                                <option value="">-- Pilih Rekening Kas Penampungan --</option>
                                <?php foreach ($kasAccounts as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= esc($k['nama_akun']) ?> (<?= format_rupiah($k['saldo_saat_ini']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                            <div class="micro-text text-muted">Jika diisi, modal awal otomatis dicatat sebagai transaksi pemasukan.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label-compact">Bukti Transfer Modal (Opsional)</label>
                            <input type="file" name="bukti_investasi" class="form-control form-control-sm" accept="image/*,application/pdf">
                        </div>

                        <div class="col-12">
                            <label class="form-label-compact">Catatan & Keterangan Kerjasama</label>
                            <textarea name="keterangan" rows="2" class="form-control form-control-sm" placeholder="Klausul perjanjian, tujuan investasi, atau kesepakatan khusus"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-compact" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-emerald btn-compact">
                        <i class="bi bi-save-fill"></i> Simpan Data Investor
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Edit Investor -->
<div class="modal fade" id="modalEditInvestor" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form id="formEditInvestor" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header py-2 px-3 bg-dark text-white">
                    <h6 class="modal-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-pencil-square text-emerald"></i>
                        <span>Edit Profil Investor: <span id="edit_inv_kode" class="text-emerald"></span></span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <label class="form-label-compact">Nama Lengkap Investor <span class="text-danger">*</span></label>
                            <input type="text" name="nama_investor" id="edit_nama_investor" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-compact">Email</label>
                            <input type="email" name="email" id="edit_email" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label-compact">No. WhatsApp</label>
                            <input type="text" name="no_telepon" id="edit_no_telepon" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label-compact">Nama Bank</label>
                            <input type="text" name="nama_bank" id="edit_nama_bank" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-compact">Nomor Rekening</label>
                            <input type="text" name="nomor_rekening" id="edit_nomor_rekening" class="form-control form-control-sm">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-compact">Pemilik Rekening</label>
                            <input type="text" name="pemilik_rekening" id="edit_pemilik_rekening" class="form-control form-control-sm">
                        </div>

                        <div class="col-md-4">
                            <label class="form-label-compact">Porsi Bagi Hasil (%) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <input type="number" name="persentase_bagi_hasil" id="edit_persentase_bagi_hasil" class="form-control form-control-sm" min="0" max="100" step="0.01" required>
                                <span class="input-group-text bg-light fw-bold">%</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-compact">Tanggal Bergabung <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_bergabung" id="edit_tanggal_bergabung" class="form-control form-control-sm" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-compact">Status Investor</label>
                            <select name="status" id="edit_status" class="form-select form-select-sm">
                                <option value="aktif">Aktif</option>
                                <option value="nonaktif">Nonaktif</option>
                            </select>
                        </div>

                        <div class="col-12">
                            <label class="form-label-compact">Keterangan / Catatan</label>
                            <textarea name="keterangan" id="edit_keterangan" rows="2" class="form-control form-control-sm"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light">
                    <button type="button" class="btn btn-secondary btn-compact" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary btn-compact">
                        <i class="bi bi-check2-circle"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal: Kalkulator & Eksekusi Bagi Hasil / Dividen Dinamis -->
<div class="modal fade" id="modalBagiHasilDividen" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 shadow-lg">
            <form action="<?= base_url('investors/distribute-dividends') ?>" method="POST">
                <?= csrf_field() ?>
                <div class="modal-header py-2 px-3 bg-dark text-white">
                    <h6 class="modal-title mb-0 d-flex align-items-center gap-2">
                        <i class="bi bi-calculator-fill text-warning"></i>
                        <span>Kalkulator & Eksekusi Bagi Hasil Dividen Otomatis</span>
                    </h6>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body p-3">
                    <div class="alert alert-warning py-2 px-3 mb-3" style="font-size: 11.5px;">
                        <i class="bi bi-info-circle-fill me-1"></i>
                        Masukkan laba bersih yang dialokasikan untuk dibagikan. Sistem akan mengalkulasi nominal hak masing-masing investor berdasarkan persentase bagi hasil dan secara otomatis mencatatkan pengeluaran dividen.
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label class="form-label-compact">Total Laba yang Dibagikan (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group input-group-sm">
                                <span class="input-group-text bg-light fw-bold">Rp</span>
                                <input type="number" name="total_laba" id="inputSimulasiLaba" class="form-control form-control-sm" placeholder="Contoh: 50000000" min="100000" step="1000" value="50000000" required oninput="calculateDividends()">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-compact">Nama Periode Dividen <span class="text-danger">*</span></label>
                            <input type="text" name="periode" class="form-control form-control-sm" placeholder="Contoh: Q3 2026 / Oktober 2026" value="Oktober <?= date('Y') ?>" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label-compact">Rekening Kas Pembayar <span class="text-danger">*</span></label>
                            <select name="kas_account_id" class="form-select form-select-sm" required>
                                <?php foreach ($kasAccounts as $k): ?>
                                    <option value="<?= $k['id'] ?>"><?= esc($k['nama_akun']) ?> (<?= format_rupiah($k['saldo_saat_ini']) ?>)</option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Realtime Simulation Table -->
                    <div class="table-responsive border rounded">
                        <table class="table table-compact mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Kode</th>
                                    <th>Nama Investor</th>
                                    <th>Rekening Tujuan</th>
                                    <th class="text-center">Porsi (%)</th>
                                    <th class="text-end">Estimasi Dividen Diterima</th>
                                </tr>
                            </thead>
                            <tbody id="simulasiTableBody">
                                <!-- JS Populated -->
                            </tbody>
                            <tfoot class="table-light fw-bold">
                                <tr>
                                    <td colspan="3">Total Dialokasikan ke Investor</td>
                                    <td class="text-center" id="simulasiTotalPersen">0%</td>
                                    <td class="text-end text-emerald" id="simulasiTotalNominal">Rp 0</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="modal-footer py-2 px-3 bg-light d-flex justify-content-between">
                    <div class="micro-text text-muted">
                        *Transaksi pengeluaran dividen akan langsung terposting ke buku kas.
                    </div>
                    <div class="d-flex gap-2">
                        <button type="button" class="btn btn-secondary btn-compact" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-warning text-dark fw-bold btn-compact" onclick="return confirm('Apakah Anda yakin ingin memproses pencairan dividen ini?')">
                            <i class="bi bi-send-check-fill"></i> Eksekusi Pencairan Dividen
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
    const investorsList = <?= json_encode($investors) ?>;

    function openEditInvestorModal(inv) {
        document.getElementById('formEditInvestor').action = '<?= base_url('investors/update/') ?>' + inv.id;
        document.getElementById('edit_inv_kode').textContent = inv.kode_investor;
        document.getElementById('edit_nama_investor').value = inv.nama_investor;
        document.getElementById('edit_email').value = inv.email || '';
        document.getElementById('edit_no_telepon').value = inv.no_telepon || '';
        document.getElementById('edit_nama_bank').value = inv.nama_bank || '';
        document.getElementById('edit_nomor_rekening').value = inv.nomor_rekening || '';
        document.getElementById('edit_pemilik_rekening').value = inv.pemilik_rekening || '';
        document.getElementById('edit_persentase_bagi_hasil').value = inv.persentase_bagi_hasil;
        document.getElementById('edit_tanggal_bergabung').value = inv.tanggal_bergabung;
        document.getElementById('edit_status').value = inv.status;
        document.getElementById('edit_keterangan').value = inv.keterangan || '';

        const modal = new bootstrap.Modal(document.getElementById('modalEditInvestor'));
        modal.show();
    }

    // Dynamic Dividend Simulator Calculation
    function calculateDividends() {
        const laba = parseFloat(document.getElementById('inputSimulasiLaba').value) || 0;
        const tbody = document.getElementById('simulasiTableBody');
        let totalNominal = 0;
        let totalPersen = 0;

        const activeInvestors = investorsList.filter(i => i.status === 'aktif');
        let html = '';

        activeInvestors.forEach(inv => {
            const persen = parseFloat(inv.persentase_bagi_hasil) || 0;
            const nominal = (persen / 100) * laba;
            totalNominal += nominal;
            totalPersen += persen;

            html += `
                <tr>
                    <td><span class="badge bg-light text-dark border font-monospace" style="font-size: 10px;">${inv.kode_investor}</span></td>
                    <td class="fw-semibold text-dark">${inv.nama_investor}</td>
                    <td class="micro-text text-muted">${inv.nama_bank || '-'} ${inv.nomor_rekening || ''}</td>
                    <td class="text-center fw-bold text-primary">${persen.toFixed(1)}%</td>
                    <td class="text-end fw-bold text-success">${formatRupiahJS(nominal)}</td>
                </tr>
            `;
        });

        tbody.innerHTML = html;
        document.getElementById('simulasiTotalPersen').textContent = totalPersen.toFixed(1) + '%';
        document.getElementById('simulasiTotalNominal').textContent = formatRupiahJS(totalNominal);
    }

    // Run initial calculation when DOM is ready
    calculateDividends();
</script>
<?= $this->endSection() ?>
