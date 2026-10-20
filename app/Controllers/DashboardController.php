<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\InvestorModel;
use App\Models\KasAccountModel;
use App\Models\TransactionModel;

class DashboardController extends BaseController
{
    protected TransactionModel $transactionModel;
    protected InvestorModel $investorModel;
    protected CategoryModel $categoryModel;
    protected KasAccountModel $kasAccountModel;

    public function __construct()
    {
        $this->transactionModel = new TransactionModel();
        $this->investorModel    = new InvestorModel();
        $this->categoryModel    = new CategoryModel();
        $this->kasAccountModel  = new KasAccountModel();
        helper(['finance', 'url']);
    }

    public function index()
    {
        $db = \Config\Database::connect();

        // 1. Investors Summary
        $investors = $this->investorModel->where('status', 'aktif')->orderBy('total_investasi', 'DESC')->findAll();
        $totalModalInvestasi = 0;
        foreach ($investors as $inv) {
            $totalModalInvestasi += (float)$inv['total_investasi'];
        }

        // 2. All Kas Accounts Total Balance
        $kasAccounts = $this->kasAccountModel->findAll();
        $totalSaldoKas = 0;
        foreach ($kasAccounts as $k) {
            $totalSaldoKas += (float)$k['saldo_saat_ini'];
        }

        // 3. Overall Income & Expense
        $totalPemasukan = $db->table('transactions')
            ->where('tipe', 'pemasukan')
            ->selectSum('jumlah')
            ->get()->getRow()->jumlah ?? 0;

        $totalPengeluaran = $db->table('transactions')
            ->where('tipe', 'pengeluaran')
            ->selectSum('jumlah')
            ->get()->getRow()->jumlah ?? 0;

        // Current Month Inflow & Outflow
        $currentMonth = date('m');
        $currentYear = date('Y');

        $bulanIniPemasukan = $db->table('transactions')
            ->where('tipe', 'pemasukan')
            ->where('MONTH(tanggal)', $currentMonth)
            ->where('YEAR(tanggal)', $currentYear)
            ->selectSum('jumlah')
            ->get()->getRow()->jumlah ?? 0;

        $bulanIniPengeluaran = $db->table('transactions')
            ->where('tipe', 'pengeluaran')
            ->where('MONTH(tanggal)', $currentMonth)
            ->where('YEAR(tanggal)', $currentYear)
            ->selectSum('jumlah')
            ->get()->getRow()->jumlah ?? 0;

        $bulanIniNet = (float)$bulanIniPemasukan - (float)$bulanIniPengeluaran;

        // 4. Monthly Chart Data (Jan - Dec for current year)
        $monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $monthlyInflow = array_fill(0, 12, 0);
        $monthlyOutflow = array_fill(0, 12, 0);
        $monthlyNet = array_fill(0, 12, 0);

        $monthlyRows = $db->table('transactions')
            ->select('MONTH(tanggal) as bln, tipe, SUM(jumlah) as total')
            ->where('YEAR(tanggal)', $currentYear)
            ->groupBy('MONTH(tanggal), tipe')
            ->get()->getResultArray();

        foreach ($monthlyRows as $row) {
            $idx = (int)$row['bln'] - 1;
            if ($row['tipe'] === 'pemasukan') {
                $monthlyInflow[$idx] = (float)$row['total'];
            } else {
                $monthlyOutflow[$idx] = (float)$row['total'];
            }
        }

        for ($i = 0; $i < 12; $i++) {
            $monthlyNet[$i] = $monthlyInflow[$i] - $monthlyOutflow[$i];
        }

        // 5. Category Breakdown: Expense
        $expenseCategories = $db->table('transactions t')
            ->select('c.nama_kategori, c.warna, SUM(t.jumlah) as total')
            ->join('categories c', 'c.id = t.category_id', 'left')
            ->where('t.tipe', 'pengeluaran')
            ->groupBy('t.category_id')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();

        // 6. Category Breakdown: Income
        $incomeCategories = $db->table('transactions t')
            ->select('c.nama_kategori, c.warna, SUM(t.jumlah) as total')
            ->join('categories c', 'c.id = t.category_id', 'left')
            ->where('t.tipe', 'pemasukan')
            ->groupBy('t.category_id')
            ->orderBy('total', 'DESC')
            ->get()->getResultArray();

        // 7. Recent Transactions (Top 8)
        $recentTransactions = $this->transactionModel->getFiltered(['limit' => 8]);

        // 8. Total Dividen Paid Out
        $totalDividen = $db->table('investment_logs')
            ->where('tipe', 'dividen_cair')
            ->selectSum('jumlah')
            ->get()->getRow()->jumlah ?? 0;

        $data = [
            'title'               => 'Dashboard Monitoring Keuangan & Investasi',
            'investors'           => $investors,
            'totalModalInvestasi' => $totalModalInvestasi,
            'totalSaldoKas'       => $totalSaldoKas,
            'totalPemasukan'      => (float)$totalPemasukan,
            'totalPengeluaran'    => (float)$totalPengeluaran,
            'labaBersih'          => (float)$totalPemasukan - (float)$totalPengeluaran,
            'bulanIniPemasukan'   => (float)$bulanIniPemasukan,
            'bulanIniPengeluaran' => (float)$bulanIniPengeluaran,
            'bulanIniNet'         => $bulanIniNet,
            'totalDividen'        => (float)$totalDividen,
            'kasAccounts'         => $kasAccounts,
            'monthlyLabels'       => json_encode($monthlyLabels),
            'monthlyInflow'       => json_encode($monthlyInflow),
            'monthlyOutflow'      => json_encode($monthlyOutflow),
            'monthlyNet'          => json_encode($monthlyNet),
            'expenseCategories'   => $expenseCategories,
            'incomeCategories'    => $incomeCategories,
            'recentTransactions'  => $recentTransactions,
            'waReady'             => (new \App\Libraries\WhatsAppService())->isReady(),
            'waGroupId'           => (new \App\Models\SettingModel())->getVal('wa_group_id', ''),
        ];

        return view('dashboard/index', $data);
    }

    /**
     * Kirim Laporan Rekap Lengkap Keuangan ke Grup WhatsApp dari Dashboard
     */
    public function sendWaRecap()
    {
        $period     = trim((string)$this->request->getPost('period')) ?: 'today';
        $customNote = trim((string)$this->request->getPost('custom_note'));
        $senderName = session()->get('nama') ?? 'Manajemen Maijek';

        $waService = new \App\Libraries\WhatsAppService();
        $result    = $waService->sendFullExecutiveRecapNotice($period, $customNote, $senderName);

        if ($result['success']) {
            return redirect()->to(base_url('dashboard'))->with('success', 'Laporan Rekap Lengkap Keuangan berhasil dikirim ke Grup WhatsApp!');
        }

        return redirect()->to(base_url('dashboard'))->with('error', 'Gagal kirim ke Grup WhatsApp: ' . ($result['message'] ?? 'Periksa pengaturan WhatsApp Gateway'));
    }
}
