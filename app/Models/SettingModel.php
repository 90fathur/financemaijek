<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingModel extends Model
{
    protected $table            = 'system_settings';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        'setting_key',
        'setting_value',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true;
    protected $dateFormat    = 'datetime';
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil nilai setting berdasarkan key
     */
    public function getVal(string $key, $default = null)
    {
        $row = $this->where('setting_key', $key)->first();
        if ($row && isset($row['setting_value']) && $row['setting_value'] !== '') {
            return $row['setting_value'];
        }
        return $default;
    }

    /**
     * Simpan atau perbarui nilai setting
     */
    public function setVal(string $key, $value): bool
    {
        $existing = $this->where('setting_key', $key)->first();
        $strValue = is_array($value) ? json_encode($value) : (string)$value;

        if ($existing) {
            return (bool)$this->update($existing['id'], [
                'setting_value' => $strValue,
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);
        }

        return (bool)$this->insert([
            'setting_key'   => $key,
            'setting_value' => $strValue,
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * Ambil semua konfigurasi sebagai associative array [key => value]
     */
    public function getAllKeyValues(): array
    {
        $all = $this->findAll();
        $result = [];
        foreach ($all as $item) {
            $result[$item['setting_key']] = $item['setting_value'];
        }
        return $result;
    }
}
