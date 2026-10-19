<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\InvestorModel;
use App\Models\KasAccountModel;
use App\Models\TransactionModel;

class ReportController extends BaseController
{
    protected TransactionModel $transactionModel;
    protected CategoryModel $categoryModel;
    protected KasAccountModel $kasAccountModel;
    protected InvestorModel $investorModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->categoryModel    = new CategoryModel();
        $this->kasAccountModel  = new KasAccountModel();
        $this->investorModel    = new InvestorModel();
        helper(['finance', 'url']);
    }

    public function index()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-01-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        $db = \Config\Database::connect();

        // Income by categories
        $incomeRows = $db->table('transactions t')
            ->select('c.nama_kategori, c.kode_kategori, c.warna, SUM(t.jumlah) as total, COUNT(t.id) as count_tx')
            ->join('categories c', 'c.id = t.category_id', 'left')
            ->where('t.tipe', 'pemasukan')
            ->where('t.tanggal >=', $startDate)
            ->where('t.tanggal <=', $endDate)
            ->groupBy('t.category_id')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();

        // Expense by categories
        $expenseRows = $db->table('transactions t')
            ->select('c.nama_kategori, c.kode_kategori, c.warna, SUM(t.jumlah) as total, COUNT(t.id) as count_tx')
            ->join('categories c', 'c.id = t.category_id', 'left')
            ->where('t.tipe', 'pengeluaran')
            ->where('t.tanggal >=', $startDate)
            ->where('t.tanggal <=', $endDate)
            ->groupBy('t.category_id')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();

        $totalIncome = 0;
        foreach ($incomeRows as $r) {
            $totalIncome += (float)$r['total'];
        }

        $totalExpense = 0;
        foreach ($expenseRows as $r) {
            $totalExpense += (float)$r['total'];
        }

        $netProfit = $totalIncome - $totalExpense;

        // Transactions list during period
        $transactions = $this->transactionModel->getFiltered([
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        $data = [
            'title'        => 'Laporan Keuangan & Laba Rugi Maijek',
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'incomeRows'   => $incomeRows,
            'expenseRows'  => $expenseRows,
            'totalIncome'  => $totalIncome,
            'totalExpense' => $totalExpense,
            'netProfit'    => $netProfit,
            'transactions' => $transactions,
            'kasAccounts'  => $this->kasAccountModel->findAll(),
            'investors'    => $this->investorModel->where('status', 'aktif')->findAll(),
        ];

        return view('reports/index', $data);
    }

    public function print()
    {
        $startDate = $this->request->getGet('start_date') ?: date('Y-01-01');
        $endDate   = $this->request->getGet('end_date') ?: date('Y-m-d');

        $db = \Config\Database::connect();

        $incomeRows = $db->table('transactions t')
            ->select('c.nama_kategori, c.kode_kategori, SUM(t.jumlah) as total, COUNT(t.id) as count_tx')
            ->join('categories c', 'c.id = t.category_id', 'left')
            ->where('t.tipe', 'pemasukan')
            ->where('t.tanggal >=', $startDate)
            ->where('t.tanggal <=', $endDate)
            ->groupBy('t.category_id')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();

        $expenseRows = $db->table('transactions t')
            ->select('c.nama_kategori, c.kode_kategori, SUM(t.jumlah) as total, COUNT(t.id) as count_tx')
            ->join('categories c', 'c.id = t.category_id', 'left')
            ->where('t.tipe', 'pengeluaran')
            ->where('t.tanggal >=', $startDate)
            ->where('t.tanggal <=', $endDate)
            ->groupBy('t.category_id')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();

        $totalIncome = 0;
        foreach ($incomeRows as $r) $totalIncome += (float)$r['total'];

        $totalExpense = 0;
        foreach ($expenseRows as $r) $totalExpense += (float)$r['total'];

        $transactions = $this->transactionModel->getFiltered([
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        $data = [
            'title'        => 'Cetak Laporan Keuangan Maijek',
            'startDate'    => $startDate,
            'endDate'      => $endDate,
            'incomeRows'   => $incomeRows,
            'expenseRows'  => $expenseRows,
            'totalIncome'  => $totalIncome,
            'totalExpense' => $totalExpense,
            'netProfit'    => $totalIncome - $totalExpense,
            'transactions' => $transactions,
            'kasAccounts'  => $this->kasAccountModel->findAll(),
            'investors'    => $this->investorModel->where('status', 'aktif')->findAll(),
        ];

        return view('reports/print', $data);
    }
}
