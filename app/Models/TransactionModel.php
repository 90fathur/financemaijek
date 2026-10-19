<?php

namespace App\Models;

use CodeIgniter\Model;

class TransactionModel extends Model
{
    protected $table            = 'transactions';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nomor_transaksi',
        'tanggal',
        'tipe',
        'category_id',
        'kas_account_id',
        'investor_id',
        'jumlah',
        'judul',
        'keterangan',
        'bukti_transaksi',
        'nama_penginput',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate unique transaction code
     */
    public function generateNomorTransaksi(string $type): string
    {
        $prefix = ($type === 'pemasukan') ? 'TRX-IN' : 'TRX-OUT';
        $ym = date('Ym');
        $pattern = "{$prefix}-{$ym}-";

        $last = $this->like('nomor_transaksi', $pattern, 'after')
            ->orderBy('id', 'DESC')
            ->first();

        if (!$last) {
            return "{$pattern}0001";
        }

        $parts = explode('-', $last['nomor_transaksi']);
        $seq = (int)end($parts) + 1;
        return "{$pattern}" . str_pad((string)$seq, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get joined transaction records with filters
     */
    public function getFiltered(array $filters = []): array
    {
        $builder = $this->db->table($this->table . ' t')
            ->select('t.*, c.nama_kategori, c.warna as kategori_warna, c.icon as kategori_icon, k.nama_akun, inv.nama_investor, inv.kode_investor')
            ->join('categories c', 'c.id = t.category_id', 'left')
            ->join('kas_accounts k', 'k.id = t.kas_account_id', 'left')
            ->join('investors inv', 'inv.id = t.investor_id', 'left');

        if (!empty($filters['tipe'])) {
            $builder->where('t.tipe', $filters['tipe']);
        }
        if (!empty($filters['category_id'])) {
            $builder->where('t.category_id', $filters['category_id']);
        }
        if (!empty($filters['kas_account_id'])) {
            $builder->where('t.kas_account_id', $filters['kas_account_id']);
        }
        if (!empty($filters['investor_id'])) {
            $builder->where('t.investor_id', $filters['investor_id']);
        }
        if (!empty($filters['start_date'])) {
            $builder->where('t.tanggal >=', $filters['start_date']);
        }
        if (!empty($filters['end_date'])) {
            $builder->where('t.tanggal <=', $filters['end_date']);
        }
        if (!empty($filters['search'])) {
            $s = $filters['search'];
            $builder->groupStart()
                ->like('t.nomor_transaksi', $s)
                ->orLike('t.judul', $s)
                ->orLike('t.keterangan', $s)
                ->orLike('c.nama_kategori', $s)
                ->orLike('inv.nama_investor', $s)
                ->groupEnd();
        }

        $limit = $filters['limit'] ?? null;
        $offset = $filters['offset'] ?? null;

        $builder->orderBy('t.tanggal', 'DESC')->orderBy('t.id', 'DESC');

        if ($limit) {
            $builder->limit((int)$limit, (int)$offset);
        }

        return $builder->get()->getResultArray();
    }

    /**
     * Get single transaction detail joined
     */
    public function getDetail(int $id): ?array
    {
        return $this->db->table($this->table . ' t')
            ->select('t.*, c.nama_kategori, c.warna as kategori_warna, c.icon as kategori_icon, k.nama_akun, k.nomor_akun, inv.nama_investor, inv.kode_investor, inv.persentase_bagi_hasil')
            ->join('categories c', 'c.id = t.category_id', 'left')
            ->join('kas_accounts k', 'k.id = t.kas_account_id', 'left')
            ->join('investors inv', 'inv.id = t.investor_id', 'left')
            ->where('t.id', $id)
            ->get()->getRowArray();
    }
}
