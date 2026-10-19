<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\InvestmentLogModel;
use App\Models\InvestorModel;
use App\Models\KasAccountModel;
use App\Models\TransactionModel;

class InvestorController extends BaseController
{
    protected InvestorModel $investorModel;
    protected InvestmentLogModel $investmentLogModel;
    protected TransactionModel $transactionModel;
    protected CategoryModel $categoryModel;
    protected KasAccountModel $kasAccountModel;

    public function __construct()
    {
        $this->investorModel      = new InvestorModel();
        $this->investmentLogModel = new InvestmentLogModel();
        $this->transactionModel   = new TransactionModel();
        $this->categoryModel      = new CategoryModel();
        $this->kasAccountModel    = new KasAccountModel();
        helper(['finance', 'form', 'url']);
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $investors = $this->investorModel->orderBy('total_investasi', 'DESC')->findAll();

        $totalInvestasiSemua = 0;
        $totalPersenSemua = 0;

        foreach ($investors as &$inv) {
            $totalInvestasiSemua += (float)$inv['total_investasi'];
            $totalPersenSemua    += (float)$inv['persentase_bagi_hasil'];

            // Calculate total dividends received by this investor
            $totalDividen = $db->table('investment_logs')
                ->where('investor_id', $inv['id'])
                ->where('tipe', 'dividen_cair')
                ->selectSum('jumlah')
                ->get()->getRow()->jumlah ?? 0;

            $inv['total_dividen'] = (float)$totalDividen;
            $inv['roi_realized']  = ($inv['total_investasi'] > 0) ? ($totalDividen / $inv['total_investasi']) * 100 : 0;
        }

        $data = [
            'title'               => 'Monitoring Portofolio Investasi Maijek',
            'investors'           => $investors,
            'totalInvestasiSemua' => $totalInvestasiSemua,
            'totalPersenSemua'    => $totalPersenSemua,
            'kasAccounts'         => $this->kasAccountModel->findAll(),
        ];

        return view('investors/index', $data);
    }

    public function store()
    {
        $rules = [
            'nama_investor'         => 'required|min_length[3]|max_length[100]',
            'total_investasi'       => 'required|numeric|greater_than_equal_to[0]',
            'persentase_bagi_hasil' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'tanggal_bergabung'     => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Silakan isi data investor dengan valid.');
        }

        $kode = $this->investorModel->generateKodeInvestor();
        $totalInvestasi = (float)$this->request->getPost('total_investasi');
        $kasAccountId = (int)$this->request->getPost('kas_account_id');

        $investorData = [
            'kode_investor'         => $kode,
            'nama_investor'         => trim((string)$this->request->getPost('nama_investor')),
            'email'                 => trim((string)$this->request->getPost('email')),
            'no_telepon'            => trim((string)$this->request->getPost('no_telepon')),
            'nama_bank'             => trim((string)$this->request->getPost('nama_bank')),
            'nomor_rekening'        => trim((string)$this->request->getPost('nomor_rekening')),
            'pemilik_rekening'      => trim((string)$this->request->getPost('pemilik_rekening')),
            'total_investasi'       => $totalInvestasi,
            'persentase_bagi_hasil' => (float)$this->request->getPost('persentase_bagi_hasil'),
            'tanggal_bergabung'     => $this->request->getPost('tanggal_bergabung'),
            'status'                => $this->request->getPost('status') ?? 'aktif',
            'keterangan'            => trim((string)$this->request->getPost('keterangan')),
        ];

        $investorId = $this->investorModel->insert($investorData);

        // If initial capital > 0 and kas account is chosen, record it as a transaction
        if ($totalInvestasi > 0 && $kasAccountId) {
            $cat = $this->categoryModel->where('tipe', 'pemasukan')
                ->where('kode_kategori', 'IN-INVEST')
                ->first();
            $catId = $cat['id'] ?? 5;

            // Handle optional receipt
            $buktiFileName = null;
            $fileBukti = $this->request->getFile('bukti_investasi');
            if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
                $newName = 'bukti_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $fileBukti->getExtension();
                $fileBukti->move(FCPATH . 'uploads/bukti', $newName);
                $buktiFileName = $newName;
            }

            $nomorTransaksi = $this->transactionModel->generateNomorTransaksi('pemasukan');
            $txId = $this->transactionModel->insert([
                'nomor_transaksi' => $nomorTransaksi,
                'tanggal'         => $this->request->getPost('tanggal_bergabung'),
                'tipe'            => 'pemasukan',
                'category_id'     => $catId,
                'kas_account_id'  => $kasAccountId,
                'investor_id'     => $investorId,
                'jumlah'          => $totalInvestasi,
                'judul'           => 'Setoran Modal Awal Investor ' . $investorData['nama_investor'],
                'keterangan'      => 'Penyertaan modal awal pendaftaran investor baru',
                'bukti_transaksi' => $buktiFileName,
                'nama_penginput'  => session()->get('nama') ?? 'Admin Maijek',
            ]);

            $this->investmentLogModel->insert([
                'investor_id'    => $investorId,
                'transaction_id' => $txId,
                'tipe'           => 'suntikan_modal',
                'jumlah'         => $totalInvestasi,
                'tanggal'        => $this->request->getPost('tanggal_bergabung'),
                'bukti'          => $buktiFileName,
                'keterangan'     => 'Modal awal pendaftaran',
            ]);

            $this->kasAccountModel->recalculateBalance($kasAccountId);
        }

        return redirect()->to(base_url('investors'))->with('success', "Investor {$investorData['nama_investor']} ({$kode}) berhasil ditambahkan!");
    }

    public function detail(int $id)
    {
        $investor = $this->investorModel->find($id);
        if (!$investor) {
            return redirect()->to(base_url('investors'))->with('error', 'Data investor tidak ditemukan.');
        }

        $logs = $this->investmentLogModel->getLogsByInvestor($id);

        $totalSuntikan = 0;
        $totalDividen  = 0;
        $totalTarik    = 0;

        foreach ($logs as $log) {
            if ($log['tipe'] === 'suntikan_modal') {
                $totalSuntikan += (float)$log['jumlah'];
            } elseif ($log['tipe'] === 'dividen_cair') {
                $totalDividen += (float)$log['jumlah'];
            } elseif ($log['tipe'] === 'penarikan_modal') {
                $totalTarik += (float)$log['jumlah'];
            }
        }

        $data = [
            'title'         => 'Detail Portofolio - ' . $investor['nama_investor'],
            'investor'      => $investor,
            'logs'          => $logs,
            'totalSuntikan' => $totalSuntikan,
            'totalDividen'  => $totalDividen,
            'totalTarik'    => $totalTarik,
            'roiRealized'   => ($investor['total_investasi'] > 0) ? ($totalDividen / $investor['total_investasi']) * 100 : 0,
            'kasAccounts'   => $this->kasAccountModel->findAll(),
        ];

        return view('investors/detail', $data);
    }

    public function update(int $id)
    {
        $investor = $this->investorModel->find($id);
        if (!$investor) {
            return redirect()->to(base_url('investors'))->with('error', 'Data investor tidak ditemukan.');
        }

        $rules = [
            'nama_investor'         => 'required|min_length[3]|max_length[100]',
            'persentase_bagi_hasil' => 'required|numeric|greater_than_equal_to[0]|less_than_equal_to[100]',
            'tanggal_bergabung'     => 'required|valid_date',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Data tidak valid.');
        }

        $updateData = [
            'nama_investor'         => trim((string)$this->request->getPost('nama_investor')),
            'email'                 => trim((string)$this->request->getPost('email')),
            'no_telepon'            => trim((string)$this->request->getPost('no_telepon')),
            'nama_bank'             => trim((string)$this->request->getPost('nama_bank')),
            'nomor_rekening'        => trim((string)$this->request->getPost('nomor_rekening')),
            'pemilik_rekening'      => trim((string)$this->request->getPost('pemilik_rekening')),
            'persentase_bagi_hasil' => (float)$this->request->getPost('persentase_bagi_hasil'),
            'tanggal_bergabung'     => $this->request->getPost('tanggal_bergabung'),
            'status'                => $this->request->getPost('status') ?? 'aktif',
            'keterangan'            => trim((string)$this->request->getPost('keterangan')),
        ];

        $this->investorModel->update($id, $updateData);

        return redirect()->to(base_url('investors'))->with('success', "Data investor {$investor['nama_investor']} berhasil diperbarui.");
    }

    public function addCapital(int $id)
    {
        $investor = $this->investorModel->find($id);
        if (!$investor) {
            return redirect()->to(base_url('investors'))->with('error', 'Investor tidak ditemukan.');
        }

        $jumlah       = (float)$this->request->getPost('jumlah');
        $kasAccountId = (int)$this->request->getPost('kas_account_id');
        $tanggal      = $this->request->getPost('tanggal') ?? date('Y-m-d');
        $keterangan   = trim((string)$this->request->getPost('keterangan'));

        if ($jumlah <= 0 || !$kasAccountId) {
            return redirect()->back()->with('error', 'Nominal dan rekening kas harus valid.');
        }

        // Upload receipt if provided
        $buktiFileName = null;
        $fileBukti = $this->request->getFile('bukti_transaksi');
        if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $newName = 'bukti_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $fileBukti->getExtension();
            $fileBukti->move(FCPATH . 'uploads/bukti', $newName);
            $buktiFileName = $newName;
        }

        $cat = $this->categoryModel->where('tipe', 'pemasukan')->where('kode_kategori', 'IN-INVEST')->first();
        $catId = $cat['id'] ?? 5;

        $nomorTransaksi = $this->transactionModel->generateNomorTransaksi('pemasukan');
        $txId = $this->transactionModel->insert([
            'nomor_transaksi' => $nomorTransaksi,
            'tanggal'         => $tanggal,
            'tipe'            => 'pemasukan',
            'category_id'     => $catId,
            'kas_account_id'  => $kasAccountId,
            'investor_id'     => $id,
            'jumlah'          => $jumlah,
            'judul'           => 'Suntikan Modal Investasi - ' . $investor['nama_investor'],
            'keterangan'      => $keterangan ?: 'Penambahan modal investasi baru',
            'bukti_transaksi' => $buktiFileName,
            'nama_penginput'  => session()->get('nama') ?? 'Admin Maijek',
        ]);

        $this->investmentLogModel->insert([
            'investor_id'    => $id,
            'transaction_id' => $txId,
            'tipe'           => 'suntikan_modal',
            'jumlah'         => $jumlah,
            'tanggal'        => $tanggal,
            'bukti'          => $buktiFileName,
            'keterangan'     => $keterangan ?: 'Penambahan modal investasi',
        ]);

        $this->investorModel->recalculateInvestment($id);
        $this->kasAccountModel->recalculateBalance($kasAccountId);

        return redirect()->to(base_url('investors/detail/' . $id))->with('success', 'Suntikan modal sebesar ' . format_rupiah($jumlah) . ' berhasil ditambahkan!');
    }

    public function distributeDividends()
    {
        $totalLaba    = (float)$this->request->getPost('total_laba');
        $periode      = trim((string)$this->request->getPost('periode'));
        $kasAccountId = (int)$this->request->getPost('kas_account_id');
        $tanggal      = $this->request->getPost('tanggal') ?? date('Y-m-d');

        if ($totalLaba <= 0 || empty($periode) || !$kasAccountId) {
            return redirect()->back()->with('error', 'Mohon isi total laba yang dibagikan, nama periode, dan rekening kas pembayar.');
        }

        $activeInvestors = $this->investorModel->where('status', 'aktif')->findAll();
        if (empty($activeInvestors)) {
            return redirect()->back()->with('error', 'Tidak ada investor aktif.');
        }

        $cat = $this->categoryModel->where('tipe', 'pengeluaran')->where('kode_kategori', 'OUT-DIVIDEN')->first();
        $catId = $cat['id'] ?? 12;

        $count = 0;
        foreach ($activeInvestors as $inv) {
            $persen = (float)$inv['persentase_bagi_hasil'];
            if ($persen <= 0) continue;

            $nominalBagiHasil = ($persen / 100) * $totalLaba;
            if ($nominalBagiHasil <= 0) continue;

            $nomorTransaksi = $this->transactionModel->generateNomorTransaksi('pengeluaran');
            $txId = $this->transactionModel->insert([
                'nomor_transaksi' => $nomorTransaksi,
                'tanggal'         => $tanggal,
                'tipe'            => 'pengeluaran',
                'category_id'     => $catId,
                'kas_account_id'  => $kasAccountId,
                'investor_id'     => $inv['id'],
                'jumlah'          => $nominalBagiHasil,
                'judul'           => "Pencairan Dividen {$periode} ({$persen}%) - {$inv['nama_investor']}",
                'keterangan'      => "Pembagian bagi hasil laba {$periode}. Rekening: {$inv['nama_bank']} {$inv['nomor_rekening']} a.n {$inv['pemilik_rekening']}",
                'bukti_transaksi' => null,
                'nama_penginput'  => session()->get('nama') ?? 'Admin Maijek',
            ]);

            $this->investmentLogModel->insert([
                'investor_id'     => $inv['id'],
                'transaction_id'  => $txId,
                'tipe'            => 'dividen_cair',
                'jumlah'          => $nominalBagiHasil,
                'tanggal'         => $tanggal,
                'periode_dividen' => $periode,
                'bukti'           => null,
                'keterangan'      => "Bagi hasil dividen {$periode} ({$persen}%)",
            ]);

            $count++;
        }

        $this->kasAccountModel->recalculateBalance($kasAccountId);

        return redirect()->to(base_url('investors'))->with('success', "Dividen periode {$periode} berhasil dicairkan kepada {$count} investor!");
    }

    public function delete(int $id)
    {
        $investor = $this->investorModel->find($id);
        if (!$investor) {
            return redirect()->to(base_url('investors'))->with('error', 'Investor tidak ditemukan.');
        }

        // Check if there are existing transactions
        $txCount = $this->transactionModel->where('investor_id', $id)->countAllResults();
        if ($txCount > 0) {
            // Set status to nonaktif instead of hard deleting to preserve historical financial integrity
            $this->investorModel->update($id, ['status' => 'nonaktif']);
            return redirect()->to(base_url('investors'))->with('warning', "Investor {$investor['nama_investor']} memiliki riwayat transaksi, status diubah menjadi Non-Aktif.");
        }

        $this->investorModel->delete($id);
        return redirect()->to(base_url('investors'))->with('success', "Data investor {$investor['nama_investor']} berhasil dihapus.");
    }
}
