<?php

namespace App\Models;

use CodeIgniter\Model;

class InvestmentLogModel extends Model
{
    protected $table            = 'investment_logs';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'investor_id',
        'transaction_id',
        'tipe',
        'jumlah',
        'tanggal',
        'periode_dividen',
        'bukti',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getLogsByInvestor(int $investorId): array
    {
        return $this->db->table($this->table . ' l')
            ->select('l.*, t.nomor_transaksi')
            ->join('transactions t', 't.id = l.transaction_id', 'left')
            ->where('l.investor_id', $investorId)
            ->orderBy('l.tanggal', 'DESC')
            ->orderBy('l.id', 'DESC')
            ->get()->getResultArray();
    }
}
