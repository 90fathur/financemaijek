<?php

namespace App\Controllers;

use App\Models\KasAccountModel;
use App\Models\TransactionModel;

class KasAccountController extends BaseController
{
    protected KasAccountModel $kasAccountModel;
    protected TransactionModel $transactionModel;

    public function __construct()
    {
        $this->kasAccountModel  = new KasAccountModel();
        $this->transactionModel = new TransactionModel();
        helper(['finance', 'form', 'url']);
    }

    public function index()
    {
        $accounts = $this->kasAccountModel->findAll();
        $totalSemuaKas = 0;
        foreach ($accounts as $a) {
            $totalSemuaKas += (float)$a['saldo_saat_ini'];
        }

        $data = [
            'title'         => 'Akun Kas & Rekening Bank Maijek',
            'accounts'      => $accounts,
            'totalSemuaKas' => $totalSemuaKas,
        ];

        return view('kas_accounts/index', $data);
    }

    public function store()
    {
        $rules = [
            'nama_akun'  => 'required|min_length[3]|max_length[100]',
            'saldo_awal' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Formulir akun kas tidak valid.');
        }

        $saldoAwal = (float)$this->request->getPost('saldo_awal');

        $id = $this->kasAccountModel->insert([
            'nama_akun'      => trim((string)$this->request->getPost('nama_akun')),
            'nomor_akun'     => trim((string)$this->request->getPost('nomor_akun')),
            'saldo_awal'     => $saldoAwal,
            'saldo_saat_ini' => $saldoAwal,
            'keterangan'     => trim((string)$this->request->getPost('keterangan')),
        ]);

        $this->kasAccountModel->recalculateBalance($id);

        return redirect()->to(base_url('kas-accounts'))->with('success', 'Akun kas berhasil ditambahkan!');
    }

    public function update(int $id)
    {
        $account = $this->kasAccountModel->find($id);
        if (!$account) {
            return redirect()->to(base_url('kas-accounts'))->with('error', 'Akun kas tidak ditemukan.');
        }

        $rules = [
            'nama_akun'  => 'required|min_length[3]|max_length[100]',
            'saldo_awal' => 'required|numeric',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Formulir akun kas tidak valid.');
        }

        $this->kasAccountModel->update($id, [
            'nama_akun'  => trim((string)$this->request->getPost('nama_akun')),
            'nomor_akun' => trim((string)$this->request->getPost('nomor_akun')),
            'saldo_awal' => (float)$this->request->getPost('saldo_awal'),
            'keterangan' => trim((string)$this->request->getPost('keterangan')),
        ]);

        $this->kasAccountModel->recalculateBalance($id);

        return redirect()->to(base_url('kas-accounts'))->with('success', "Akun kas {$account['nama_akun']} berhasil diperbarui.");
    }

    public function recalculate(int $id)
    {
        $newBalance = $this->kasAccountModel->recalculateBalance($id);
        return redirect()->to(base_url('kas-accounts'))->with('success', 'Saldo akun berhasil disinkronkan kembali: ' . format_rupiah($newBalance));
    }

    public function delete(int $id)
    {
        $account = $this->kasAccountModel->find($id);
        if (!$account) {
            return redirect()->to(base_url('kas-accounts'))->with('error', 'Akun kas tidak ditemukan.');
        }

        $usedCount = $this->transactionModel->where('kas_account_id', $id)->countAllResults();
        if ($usedCount > 0) {
            return redirect()->to(base_url('kas-accounts'))->with('error', "Akun kas tidak dapat dihapus karena memiliki riwayat {$usedCount} transaksi.");
        }

        $this->kasAccountModel->delete($id);
        return redirect()->to(base_url('kas-accounts'))->with('success', "Akun kas {$account['nama_akun']} berhasil dihapus.");
    }
}
