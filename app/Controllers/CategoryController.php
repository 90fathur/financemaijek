<?php

namespace App\Controllers;

use App\Models\CategoryModel;
use App\Models\TransactionModel;

class CategoryController extends BaseController
{
    protected CategoryModel $categoryModel;
    protected TransactionModel $transactionModel;

    public function __construct()
    {
        $this->categoryModel    = new CategoryModel();
        $this->transactionModel = new TransactionModel();
        helper(['finance', 'form', 'url']);
    }

    public function index()
    {
        $incomeCategories  = $this->categoryModel->where('tipe', 'pemasukan')->orderBy('nama_kategori', 'ASC')->findAll();
        $expenseCategories = $this->categoryModel->where('tipe', 'pengeluaran')->orderBy('nama_kategori', 'ASC')->findAll();

        $data = [
            'title'             => 'Kategori Keuangan Dinamis',
            'incomeCategories'  => $incomeCategories,
            'expenseCategories' => $expenseCategories,
        ];

        return view('categories/index', $data);
    }

    public function store()
    {
        $rules = [
            'nama_kategori' => 'required|min_length[2]|max_length[100]',
            'tipe'          => 'required|in_list[pemasukan,pengeluaran]',
            'warna'         => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Formulir kategori tidak valid.');
        }

        $nama  = trim((string)$this->request->getPost('nama_kategori'));
        $tipe  = $this->request->getPost('tipe');
        $kode  = trim((string)$this->request->getPost('kode_kategori')) ?: strtoupper(substr($tipe, 0, 2) . '-' . substr(preg_replace('/[^A-Za-z0-9]/', '', $nama), 0, 6));
        $icon  = $this->request->getPost('icon') ?: 'bi-tag';
        $warna = $this->request->getPost('warna') ?: '#10b981';
        $desk  = trim((string)$this->request->getPost('deskripsi'));

        $this->categoryModel->insert([
            'nama_kategori' => $nama,
            'tipe'          => $tipe,
            'kode_kategori' => $kode,
            'icon'          => $icon,
            'warna'         => $warna,
            'deskripsi'     => $desk,
        ]);

        return redirect()->to(base_url('categories'))->with('success', "Kategori {$nama} berhasil ditambahkan!");
    }

    public function update(int $id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            return redirect()->to(base_url('categories'))->with('error', 'Kategori tidak ditemukan.');
        }

        $rules = [
            'nama_kategori' => 'required|min_length[2]|max_length[100]',
            'warna'         => 'required|regex_match[/^#[a-fA-F0-9]{6}$/]',
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Formulir kategori tidak valid.');
        }

        $updateData = [
            'nama_kategori' => trim((string)$this->request->getPost('nama_kategori')),
            'kode_kategori' => trim((string)$this->request->getPost('kode_kategori')),
            'icon'          => $this->request->getPost('icon') ?: 'bi-tag',
            'warna'         => $this->request->getPost('warna') ?: '#10b981',
            'deskripsi'     => trim((string)$this->request->getPost('deskripsi')),
        ];

        $this->categoryModel->update($id, $updateData);

        return redirect()->to(base_url('categories'))->with('success', "Kategori {$category['nama_kategori']} berhasil diperbarui.");
    }

    public function delete(int $id)
    {
        $category = $this->categoryModel->find($id);
        if (!$category) {
            return redirect()->to(base_url('categories'))->with('error', 'Kategori tidak ditemukan.');
        }

        $usedCount = $this->transactionModel->where('category_id', $id)->countAllResults();
        if ($usedCount > 0) {
            return redirect()->to(base_url('categories'))->with('error', "Kategori tidak dapat dihapus karena sudah dipakai pada {$usedCount} transaksi.");
        }

        $this->categoryModel->delete($id);
        return redirect()->to(base_url('categories'))->with('success', "Kategori {$category['nama_kategori']} berhasil dihapus.");
    }
}
