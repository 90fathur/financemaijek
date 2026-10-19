<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Laporan Keuangan - Maijek</title>
    
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            font-size: 11px;
            color: #0f172a;
            background: #ffffff;
            line-height: 1.4;
            padding: 30px;
        }

        .header-kop {
            border-bottom: 2px solid #0f172a;
            padding-bottom: 15px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .brand-logo-print {
            width: 44px;
            height: 44px;
            background: #059669;
            color: #ffffff;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
        }

        .table-print {
            font-size: 10.5px;
            border-color: #cbd5e1;
        }
        .table-print th {
            background-color: #f1f5f9 !important;
            font-weight: 700;
            text-transform: uppercase;
            font-size: 9.5px;
            padding: 6px 8px;
            border-bottom: 1px solid #94a3b8;
        }
        .table-print td {
            padding: 5px 8px;
            border-bottom: 1px solid #e2e8f0;
        }

        .signature-box {
            margin-top: 40px;
            display: flex;
            justify-content: space-between;
            page-break-inside: avoid;
        }
        .sign-col {
            text-align: center;
            width: 200px;
        }
        .sign-space {
            height: 60px;
        }

        @media print {
            body {
                padding: 15px;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body>

    <!-- Print Control Buttons -->
    <div class="no-print mb-4 d-flex justify-content-between align-items-center bg-light p-3 border rounded">
        <div>
            <strong>Preview Cetak Dokumen Keuangan Maijek</strong>
            <div class="text-muted" style="font-size: 11px;">Silakan gunakan tombol cetak atau simpan sebagai PDF.</div>
        </div>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-sm btn-success fw-bold">
                <i class="bi bi-printer-fill me-1"></i> Cetak Dokumen / Simpan PDF
            </button>
            <button onclick="window.close()" class="btn btn-sm btn-secondary">
                Tutup
            </button>
        </div>
    </div>

    <!-- Official Header / Kop Surat -->
    <div class="header-kop">
        <div class="d-flex align-items-center gap-3">
            <div class="brand-logo-print">
                <i class="bi bi-send-fill" style="transform: rotate(45deg);"></i>
            </div>
            <div>
                <h4 class="fw-bold mb-0 text-dark" style="font-size: 16px; letter-spacing: -0.02em;">PT. MAIJEK TRANSPORTASI NUSANTARA</h4>
                <div style="font-size: 11px; color: #475569;">Layanan Transportasi, Pengantaran & Marketplace Kuliner Lokal</div>
                <div style="font-size: 10px; color: #64748b;">Kantor Pusat: Polewali Mandar, Sulawesi Barat • Email: finance@maijek.id</div>
            </div>
        </div>
        <div class="text-end" style="font-size: 10.5px;">
            <div class="badge bg-dark text-white mb-1">OFFICIAL FINANCE REPORT</div>
            <div class="text-muted">Tanggal Cetak: <?= date('d/m/Y H:i') ?> WITA</div>
        </div>
    </div>

    <!-- Report Title -->
    <div class="text-center my-3 pb-2 border-bottom">
        <h5 class="fw-bold mb-1" style="font-size: 14px; text-transform: uppercase;">
            LAPORAN KEUANGAN & LABA RUGI OPERASIONAL
        </h5>
        <div style="font-size: 11px; color: #334155;">
            Periode: <strong><?= format_tanggal_indo($startDate) ?></strong> s/d <strong><?= format_tanggal_indo($endDate) ?></strong>
        </div>
    </div>

    <!-- P&L Financial Highlights Grid -->
    <div class="row g-2 mb-4">
        <div class="col-4">
            <div class="p-2 border rounded bg-light">
                <div class="text-muted text-uppercase" style="font-size: 9.5px; font-weight: 700;">Total Pemasukan (Revenue)</div>
                <div class="fw-bold text-success" style="font-size: 13px;"><?= format_rupiah($totalIncome) ?></div>
            </div>
        </div>
        <div class="col-4">
            <div class="p-2 border rounded bg-light">
                <div class="text-muted text-uppercase" style="font-size: 9.5px; font-weight: 700;">Total Beban Pengeluaran (Expense)</div>
                <div class="fw-bold text-danger" style="font-size: 13px;"><?= format_rupiah($totalExpense) ?></div>
            </div>
        </div>
        <div class="col-4">
            <div class="p-2 border rounded bg-light">
                <div class="text-muted text-uppercase" style="font-size: 9.5px; font-weight: 700;">Laba Bersih Operasional (Net)</div>
                <div class="fw-bold <?= ($netProfit >= 0) ? 'text-primary' : 'text-danger' ?>" style="font-size: 13px;">
                    <?= format_rupiah($netProfit) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Rincian Pemasukan & Pengeluaran -->
    <div class="row g-3 mb-4">
        <div class="col-6">
            <h6 class="fw-bold border-bottom pb-1 text-success" style="font-size: 11px;">
                1. Rincian Pemasukan Arus Kas
            </h6>
            <table class="table table-print table-bordered w-100 mb-0">
                <thead>
                    <tr>
                        <th>Pos Pemasukan</th>
                        <th class="text-center" style="width: 50px;">Trx</th>
                        <th class="text-end">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($incomeRows as $in): ?>
                        <tr>
                            <td><?= esc($in['nama_kategori']) ?></td>
                            <td class="text-center"><?= $in['count_tx'] ?></td>
                            <td class="text-end fw-semibold text-success"><?= format_rupiah($in['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-bold table-light">
                        <td colspan="2">TOTAL PENDAPATAN</td>
                        <td class="text-end text-success"><?= format_rupiah($totalIncome) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <div class="col-6">
            <h6 class="fw-bold border-bottom pb-1 text-danger" style="font-size: 11px;">
                2. Rincian Beban Operasional
            </h6>
            <table class="table table-print table-bordered w-100 mb-0">
                <thead>
                    <tr>
                        <th>Pos Pengeluaran</th>
                        <th class="text-center" style="width: 50px;">Trx</th>
                        <th class="text-end">Jumlah</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($expenseRows as $out): ?>
                        <tr>
                            <td><?= esc($out['nama_kategori']) ?></td>
                            <td class="text-center"><?= $out['count_tx'] ?></td>
                            <td class="text-end fw-semibold text-danger"><?= format_rupiah($out['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot>
                    <tr class="fw-bold table-light">
                        <td colspan="2">TOTAL BEBAN</td>
                        <td class="text-end text-danger"><?= format_rupiah($totalExpense) ?></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Rekap Transaksi Rinci -->
    <h6 class="fw-bold border-bottom pb-1 mb-2 text-dark" style="font-size: 11px;">
        3. Catatan Buku Kas Transaksi Periode Berjalan
    </h6>
    <table class="table table-print table-bordered w-100 mb-4">
        <thead>
            <tr>
                <th style="width: 100px;">No. Transaksi</th>
                <th style="width: 70px;">Tanggal</th>
                <th style="width: 80px;">Tipe</th>
                <th>Kategori</th>
                <th>Uraian / Deskripsi</th>
                <th style="width: 110px;">Kas / Bank</th>
                <th class="text-end" style="width: 110px;">Nominal (Rp)</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($transactions as $t): ?>
                <tr>
                    <td class="font-monospace fw-bold"><?= esc($t['nomor_transaksi']) ?></td>
                    <td><?= date('d/m/Y', strtotime($t['tanggal'])) ?></td>
                    <td><?= strtoupper($t['tipe']) ?></td>
                    <td><?= esc($t['nama_kategori'] ?? '-') ?></td>
                    <td>
                        <?= esc($t['judul']) ?>
                        <?php if (!empty($t['nama_investor'])): ?>
                            <div style="font-size: 9.5px; color: #059669;">[Investor: <?= esc($t['nama_investor']) ?>]</div>
                        <?php endif; ?>
                    </td>
                    <td><?= esc($t['nama_akun'] ?? '-') ?></td>
                    <td class="text-end fw-semibold <?= ($t['tipe'] === 'pemasukan') ? 'text-success' : 'text-danger' ?>">
                        <?= ($t['tipe'] === 'pemasukan' ? '+' : '-') ?> <?= format_rupiah($t['jumlah']) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Signature Block -->
    <div class="signature-box">
        <div class="sign-col">
            <div>Diverifikasi Oleh,</div>
            <div class="fw-semibold text-muted" style="font-size: 10px;">Head of Finance & Accounting</div>
            <div class="sign-space"></div>
            <div class="fw-bold text-dark text-decoration-underline">H. BAHARUDDIN</div>
            <div style="font-size: 9.5px; color: #64748b;">Staff Finance Maijek</div>
        </div>

        <div class="sign-col">
            <div>Polewali Mandar, <?= date('d F Y') ?></div>
            <div>Disetujui Oleh,</div>
            <div class="fw-semibold text-muted" style="font-size: 10px;">Direktur Utama PT Maijek</div>
            <div class="sign-space"></div>
            <div class="fw-bold text-dark text-decoration-underline">DIREKSI UTAMA MAIJEK</div>
            <div style="font-size: 9.5px; color: #64748b;">PT. Maijek Transportasi Nusantara</div>
        </div>
    </div>

</body>
</html>
