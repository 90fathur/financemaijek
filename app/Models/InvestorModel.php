<?php

namespace App\Models;

use CodeIgniter\Model;

class InvestorModel extends Model
{
    protected $table            = 'investors';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'kode_investor',
        'nama_investor',
        'email',
        'no_telepon',
        'nama_bank',
        'nomor_rekening',
        'pemilik_rekening',
        'total_investasi',
        'persentase_bagi_hasil',
        'tanggal_bergabung',
        'status',
        'keterangan',
    ];

    // Dates
    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Generate next investor code e.g. INV-005
     */
    public function generateKodeInvestor(): string
    {
        $last = $this->orderBy('id', 'DESC')->first();
        if (!$last) {
            return 'INV-001';
        }
        $num = (int) substr($last['kode_investor'], 4) + 1;
        return 'INV-' . str_pad((string)$num, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Recalculate investor total investment based on logs
     */
    public function recalculateInvestment(int $investorId): float
    {
        $db = \Config\Database::connect();
        $in = $db->table('investment_logs')
            ->where('investor_id', $investorId)
            ->where('tipe', 'suntikan_modal')
            ->selectSum('jumlah')
            ->get()->getRow()->jumlah ?? 0;

        $out = $db->table('investment_logs')
            ->where('investor_id', $investorId)
            ->where('tipe', 'penarikan_modal')
            ->selectSum('jumlah')
            ->get()->getRow()->jumlah ?? 0;

        $total = (float)$in - (float)$out;
        if ($total < 0) $total = 0;

        $this->update($investorId, ['total_investasi' => $total]);
        return $total;
    }
}
