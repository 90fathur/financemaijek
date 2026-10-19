<?php

namespace App\Models;

use CodeIgniter\Model;

class KasAccountModel extends Model
{
    protected $table            = 'kas_accounts';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'nama_akun',
        'nomor_akun',
        'saldo_awal',
        'saldo_saat_ini',
        'keterangan',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Recalculate real-time current balance for a kas account
     */
    public function recalculateBalance(int $accountId): float
    {
        $account = $this->find($accountId);
        if (!$account) {
            return 0.00;
        }

        $db = \Config\Database::connect();
        $in = $db->table('transactions')
            ->where('kas_account_id', $accountId)
            ->where('tipe', 'pemasukan')
            ->selectSum('jumlah')
            ->get()->getRow()->jumlah ?? 0;

        $out = $db->table('transactions')
            ->where('kas_account_id', $accountId)
            ->where('tipe', 'pengeluaran')
            ->selectSum('jumlah')
            ->get()->getRow()->jumlah ?? 0;

        $newBalance = (float)$account['saldo_awal'] + (float)$in - (float)$out;
        $this->update($accountId, ['saldo_saat_ini' => $newBalance]);

        return $newBalance;
    }
}
