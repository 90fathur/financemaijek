<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\InvestmentLogModel;
use App\Models\InvestorModel;
use App\Models\KasAccountModel;
use App\Models\TransactionModel;

class TransactionController extends BaseController
{
    protected TransactionModel $transactionModel;
    protected CategoryModel $categoryModel;
    protected KasAccountModel $kasAccountModel;
    protected InvestorModel $investorModel;
    protected InvestmentLogModel $investmentLogModel;

    public function __construct()
    {
        $this->transactionModel   = new TransactionModel();
        $this->categoryModel      = new CategoryModel();
        $this->kasAccountModel    = new KasAccountModel();
        $this->investorModel      = new InvestorModel();
        $this->investmentLogModel = new InvestmentLogModel();
        helper(['finance', 'form', 'url']);
    }

    public function index()
    {
        $filters = [
            'tipe'           => $this->request->getGet('tipe'),
            'category_id'    => $this->request->getGet('category_id'),
            'kas_account_id' => $this->request->getGet('kas_account_id'),
            'investor_id'    => $this->request->getGet('investor_id'),
            'start_date'     => $this->request->getGet('start_date'),
            'end_date'       => $this->request->getGet('end_date'),
            'search'         => $this->request->getGet('search'),
        ];

        $transactions = $this->transactionModel->getFiltered($filters);

        // Calculate summary for currently filtered records
        $totalMasuk = 0;
        $totalKeluar = 0;
        foreach ($transactions as $t) {
            if ($t['tipe'] === 'pemasukan') {
                $totalMasuk += (float)$t['jumlah'];
            } else {
                $totalKeluar += (float)$t['jumlah'];
            }
        }

        $data = [
            'title'        => 'Pencatatan Keuangan & Transaksi',
            'transactions' => $transactions,
            'filters'      => $filters,
            'totalMasuk'   => $totalMasuk,
            'totalKeluar'  => $totalKeluar,
            'netFlow'      => $totalMasuk - $totalKeluar,
            'categories'   => $this->categoryModel->orderBy('nama_kategori', 'ASC')->findAll(),
            'kasAccounts'  => $this->kasAccountModel->findAll(),
            'investors'    => $this->investorModel->where('status', 'aktif')->findAll(),
        ];

        return view('transactions/index', $data);
    }

    public function store()
    {
        $rules = [
            'tipe'           => 'required|in_list[pemasukan,pengeluaran]',
            'tanggal'        => 'required|valid_date',
            'category_id'    => 'required|is_not_unique[categories.id]',
            'kas_account_id' => 'required|is_not_unique[kas_accounts.id]',
            'jumlah'         => 'required|numeric|greater_than[0]',
            'judul'          => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Mohon lengkapi formulir transaksi dengan benar.');
        }

        $tipe         = $this->request->getPost('tipe');
        $tanggal      = $this->request->getPost('tanggal');
        $categoryId   = (int)$this->request->getPost('category_id');
        $kasAccountId = (int)$this->request->getPost('kas_account_id');
        $investorId   = $this->request->getPost('investor_id') ? (int)$this->request->getPost('investor_id') : null;
        $jumlah       = (float)$this->request->getPost('jumlah');
        $judul        = trim((string)$this->request->getPost('judul'));
        $keterangan   = trim((string)$this->request->getPost('keterangan'));

        // Handle proof / receipt file upload
        $buktiFileName = null;
        $fileBukti = $this->request->getFile('bukti_transaksi');
        if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
            $newName = 'bukti_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $fileBukti->getExtension();
            $fileBukti->move(FCPATH . 'uploads/bukti', $newName);
            $buktiFileName = $newName;
        }

        $nomorTransaksi = $this->transactionModel->generateNomorTransaksi($tipe);

        $txData = [
            'nomor_transaksi' => $nomorTransaksi,
            'tanggal'         => $tanggal,
            'tipe'            => $tipe,
            'category_id'     => $categoryId,
            'kas_account_id'  => $kasAccountId,
            'investor_id'     => $investorId,
            'jumlah'          => $jumlah,
            'judul'           => $judul,
            'keterangan'      => $keterangan,
            'bukti_transaksi' => $buktiFileName,
            'nama_penginput'  => session()->get('nama') ?? 'Admin Maijek',
        ];

        $txId = $this->transactionModel->insert($txData);

        // Recalculate kas account balance
        $this->kasAccountModel->recalculateBalance($kasAccountId);

        // If transaction involves investor (e.g. investment capital or dividend)
        if ($investorId) {
            $category = $this->categoryModel->find($categoryId);
            $catCode = $category['kode_kategori'] ?? '';

            if ($tipe === 'pemasukan' && (stripos($category['nama_kategori'], 'Modal') !== false || $catCode === 'IN-INVEST')) {
                $this->investmentLogModel->insert([
                    'investor_id'    => $investorId,
                    'transaction_id' => $txId,
                    'tipe'           => 'suntikan_modal',
                    'jumlah'         => $jumlah,
                    'tanggal'        => $tanggal,
                    'bukti'          => $buktiFileName,
                    'keterangan'     => $judul . ' - ' . $keterangan,
                ]);
                $this->investorModel->recalculateInvestment($investorId);
            } elseif ($tipe === 'pengeluaran' && (stripos($category['nama_kategori'], 'Dividen') !== false || $catCode === 'OUT-DIVIDEN')) {
                $this->investmentLogModel->insert([
                    'investor_id'     => $investorId,
                    'transaction_id'  => $txId,
                    'tipe'            => 'dividen_cair',
                    'jumlah'          => $jumlah,
                    'tanggal'         => $tanggal,
                    'periode_dividen' => date('F Y', strtotime($tanggal)),
                    'bukti'           => $buktiFileName,
                    'keterangan'      => $judul . ' - ' . $keterangan,
                ]);
            }
        }

        return redirect()->to(base_url('transactions'))->with('success', "Transaksi {$nomorTransaksi} berhasil dicatat!");
    }

    public function update(int $id)
    {
        $transaction = $this->transactionModel->find($id);
        if (!$transaction) {
            return redirect()->to(base_url('transactions'))->with('error', 'Transaksi tidak ditemukan.');
        }

        $rules = [
            'tipe'           => 'required|in_list[pemasukan,pengeluaran]',
            'tanggal'        => 'required|valid_date',
            'category_id'    => 'required|is_not_unique[categories.id]',
            'kas_account_id' => 'required|is_not_unique[kas_accounts.id]',
            'jumlah'         => 'required|numeric|greater_than[0]',
            'judul'          => 'required|min_length[3]|max_length[255]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Mohon lengkapi formulir transaksi dengan benar.');
        }

        $oldKasId = $transaction['kas_account_id'];
        $newKasId = (int)$this->request->getPost('kas_account_id');

        $buktiFileName = $transaction['bukti_transaksi'];
        $fileBukti = $this->request->getFile('bukti_transaksi');
        if ($fileBukti && $fileBukti->isValid() && !$fileBukti->hasMoved()) {
            // Delete old file if exists and different
            if ($buktiFileName && file_exists(FCPATH . 'uploads/bukti/' . $buktiFileName)) {
                @unlink(FCPATH . 'uploads/bukti/' . $buktiFileName);
            }
            $newName = 'bukti_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $fileBukti->getExtension();
            $fileBukti->move(FCPATH . 'uploads/bukti', $newName);
            $buktiFileName = $newName;
        }

        $investorId = $this->request->getPost('investor_id') ? (int)$this->request->getPost('investor_id') : null;

        $updateData = [
            'tanggal'         => $this->request->getPost('tanggal'),
            'tipe'            => $this->request->getPost('tipe'),
            'category_id'     => (int)$this->request->getPost('category_id'),
            'kas_account_id'  => $newKasId,
            'investor_id'     => $investorId,
            'jumlah'          => (float)$this->request->getPost('jumlah'),
            'judul'           => trim((string)$this->request->getPost('judul')),
            'keterangan'      => trim((string)$this->request->getPost('keterangan')),
            'bukti_transaksi' => $buktiFileName,
        ];

        $this->transactionModel->update($id, $updateData);

        // Recalculate kas accounts
        $this->kasAccountModel->recalculateBalance($oldKasId);
        if ($newKasId !== $oldKasId) {
            $this->kasAccountModel->recalculateBalance($newKasId);
        }

        if ($investorId) {
            $this->investorModel->recalculateInvestment($investorId);
        }

        return redirect()->to(base_url('transactions'))->with('success', "Transaksi {$transaction['nomor_transaksi']} berhasil diperbarui.");
    }

    public function delete(int $id)
    {
        $transaction = $this->transactionModel->find($id);
        if (!$transaction) {
            return redirect()->to(base_url('transactions'))->with('error', 'Transaksi tidak ditemukan.');
        }

        $kasId = $transaction['kas_account_id'];
        $investorId = $transaction['investor_id'];

        // Remove proof image if present
        if ($transaction['bukti_transaksi'] && file_exists(FCPATH . 'uploads/bukti/' . $transaction['bukti_transaksi'])) {
            @unlink(FCPATH . 'uploads/bukti/' . $transaction['bukti_transaksi']);
        }

        // Delete associated investment logs if any
        $this->investmentLogModel->where('transaction_id', $id)->delete();

        $this->transactionModel->delete($id);

        // Recalculate kas balance
        $this->kasAccountModel->recalculateBalance($kasId);
        if ($investorId) {
            $this->investorModel->recalculateInvestment($investorId);
        }

        return redirect()->to(base_url('transactions'))->with('success', "Transaksi {$transaction['nomor_transaksi']} berhasil dihapus.");
    }

    public function exportCsv()
    {
        $filters = [
            'tipe'           => $this->request->getGet('tipe'),
            'category_id'    => $this->request->getGet('category_id'),
            'kas_account_id' => $this->request->getGet('kas_account_id'),
            'investor_id'    => $this->request->getGet('investor_id'),
            'start_date'     => $this->request->getGet('start_date'),
            'end_date'       => $this->request->getGet('end_date'),
            'search'         => $this->request->getGet('search'),
        ];

        $transactions = $this->transactionModel->getFiltered($filters);

        $filename = 'Laporan_Keuangan_Maijek_' . date('Ymd_His') . '.csv';

        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');

        $output = fopen('php://output', 'w');
        // UTF-8 BOM for Microsoft Excel
        fputs($output, "\xEF\xBB\xBF");

        fputcsv($output, [
            'No. Transaksi',
            'Tanggal',
            'Tipe',
            'Kategori',
            'Rekening Kas',
            'Investor Terkait',
            'Nominal (Rp)',
            'Judul Transaksi',
            'Keterangan',
            'Bukti Transaksi',
            'Diinput Oleh'
        ]);

        foreach ($transactions as $t) {
            fputcsv($output, [
                $t['nomor_transaksi'],
                $t['tanggal'],
                strtoupper($t['tipe']),
                $t['nama_kategori'] ?? '-',
                $t['nama_akun'] ?? '-',
                $t['nama_investor'] ?? '-',
                $t['jumlah'],
                $t['judul'],
                $t['keterangan'] ?? '-',
                $t['bukti_transaksi'] ? base_url('uploads/bukti/' . $t['bukti_transaksi']) : 'Tidak ada',
                $t['nama_penginput'] ?? '-'
            ]);
        }

        fclose($output);
        exit;
    }
}
