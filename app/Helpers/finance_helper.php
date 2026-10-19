<?php

if (!function_exists('format_rupiah')) {
    function format_rupiah($nominal, bool $withSymbol = true): string
    {
        $val = (float)$nominal;
        $formatted = number_format($val, 0, ',', '.');
        return $withSymbol ? 'Rp ' . $formatted : $formatted;
    }
}

if (!function_exists('format_tanggal_indo')) {
    function format_tanggal_indo($dateStr, bool $withDay = false): string
    {
        if (empty($dateStr) || $dateStr === '0000-00-00') {
            return '-';
        }

        $timestamp = strtotime($dateStr);
        $bulanIndo = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'
        ];

        $hariIndo = [
            'Sunday' => 'Minggu', 'Monday' => 'Senin', 'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu', 'Thursday' => 'Kamis', 'Friday' => 'Jumat', 'Saturday' => 'Sabtu'
        ];

        $d = date('d', $timestamp);
        $m = (int)date('m', $timestamp);
        $y = date('Y', $timestamp);

        $res = "{$d} {$bulanIndo[$m]} {$y}";
        if ($withDay) {
            $dayName = $hariIndo[date('l', $timestamp)] ?? '';
            $res = "{$dayName}, " . $res;
        }

        return $res;
    }
}

if (!function_exists('format_badge_tipe')) {
    function format_badge_tipe(string $tipe): string
    {
        if ($tipe === 'pemasukan') {
            return '<span class="badge bg-emerald-subtle text-emerald border border-emerald-subtle"><i class="bi bi-arrow-down-left me-1"></i>Pemasukan</span>';
        }
        return '<span class="badge bg-rose-subtle text-rose border border-rose-subtle"><i class="bi bi-arrow-up-right me-1"></i>Pengeluaran</span>';
    }
}

if (!function_exists('format_persen')) {
    function format_persen($val): string
    {
        return number_format((float)$val, 1, ',', '.') . '%';
    }
}
