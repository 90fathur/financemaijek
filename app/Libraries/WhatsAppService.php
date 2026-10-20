<?php

namespace App\Libraries;

use App\Models\SettingModel;

class WhatsAppService
{
    protected SettingModel $settingModel;
    protected bool $enabled = false;
    protected string $apiUrl = 'https://app.cloudchat.id/api/public/v1/messages';
    protected string $apiKey = '';
    protected string $groupId = '';
    protected bool $notifyIncome = true;
    protected bool $notifyExpense = true;
    protected bool $notifyCapital = true;
    protected bool $notifyDividend = true;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        helper(['finance']);

        $this->enabled        = (bool)$this->settingModel->getVal('wa_enabled', false);
        $this->apiUrl         = (string)$this->settingModel->getVal('wa_api_url', 'https://app.cloudchat.id/api/public/v1/messages');
        $this->apiKey         = trim((string)$this->settingModel->getVal('wa_api_key', ''));
        $this->groupId        = trim((string)$this->settingModel->getVal('wa_group_id', ''));
        $this->notifyIncome   = (bool)$this->settingModel->getVal('wa_notify_income', true);
        $this->notifyExpense  = (bool)$this->settingModel->getVal('wa_notify_expense', true);
        $this->notifyCapital  = (bool)$this->settingModel->getVal('wa_notify_capital', true);
        $this->notifyDividend = (bool)$this->settingModel->getVal('wa_notify_dividend', true);
    }

    /**
     * Cek apakah WhatsApp Gateway aktif dan siap digunakan
     */
    public function isReady(): bool
    {
        return $this->enabled && !empty($this->apiKey) && !empty($this->groupId);
    }

    /**
     * Kirim pesan teks ke WhatsApp Group atau nomor tujuan melalui CloudChat API
     */
    public function sendMessage(string $message, ?string $target = null): array
    {
        $to = $target ?: $this->groupId;

        if (empty($this->apiKey)) {
            return [
                'success' => false,
                'message' => 'API Key CloudChat belum diisi pada menu Pengaturan.',
            ];
        }

        if (empty($to)) {
            return [
                'success' => false,
                'message' => 'Group ID / Nomor WhatsApp tujuan belum ditentukan.',
            ];
        }

        $endpoint = !empty($this->apiUrl) ? $this->apiUrl : 'https://app.cloudchat.id/api/public/v1/messages';

        $payload = [
            'channel' => 'whatsapp',
            'type'    => 'text',
            'to'      => $to,
            'content' => [
                'text' => $message,
            ],
        ];

        $jsonPayload = json_encode($payload);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL            => $endpoint,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_POSTFIELDS     => $jsonPayload,
            CURLOPT_HTTPHEADER     => [
                'Authorization: Bearer ' . $this->apiKey,
                'Content-Type: application/json',
                'Accept: application/json',
            ],
            CURLOPT_TIMEOUT        => 12,
            CURLOPT_CONNECTTIMEOUT => 6,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlErr  = curl_error($ch);
        curl_close($ch);

        if ($curlErr) {
            return [
                'success'  => false,
                'httpCode' => $httpCode,
                'message'  => 'cURL Error: ' . $curlErr,
                'response' => null,
            ];
        }

        $decoded = json_decode((string)$response, true);

        if ($httpCode >= 200 && $httpCode < 300) {
            return [
                'success'  => true,
                'httpCode' => $httpCode,
                'message'  => 'Pesan berhasil dikirim ke WhatsApp.',
                'response' => $decoded ?? $response,
            ];
        }

        $errMsg = 'HTTP ' . $httpCode;
        if (is_array($decoded) && isset($decoded['message'])) {
            $errMsg .= ': ' . $decoded['message'];
        } elseif (is_string($response) && $response !== '') {
            $errMsg .= ': ' . substr(strip_tags($response), 0, 150);
        }

        return [
            'success'  => false,
            'httpCode' => $httpCode,
            'message'  => 'Gagal mengirim pesan via CloudChat (' . $errMsg . ')',
            'response' => $decoded ?? $response,
        ];
    }

    /**
     * Kirim notifikasi transaksi baru (pemasukan / pengeluaran) beserta sisa kas terkini
     */
    public function sendTransactionNotice(array $trx, array $cat, array $kas, ?array $inv = null): array
    {
        if (!$this->isReady()) {
            return ['success' => false, 'message' => 'WhatsApp gateway dinonaktifkan.'];
        }

        $isPemasukan = ($trx['tipe'] === 'pemasukan');
        if ($isPemasukan && !$this->notifyIncome) {
            return ['success' => false, 'message' => 'Notifikasi pemasukan dinonaktifkan.'];
        }
        if (!$isPemasukan && !$this->notifyExpense) {
            return ['success' => false, 'message' => 'Notifikasi pengeluaran dinonaktifkan.'];
        }

        $db = \Config\Database::connect();

        // Ambil data kas akun yang bersangkutan secara real-time dari database
        $kasId       = $kas['id'] ?? ($trx['kas_account_id'] ?? null);
        $freshKas    = $kasId ? $db->table('kas_accounts')->where('id', $kasId)->get()->getRowArray() : $kas;
        $sisaAkunIni = (float)($freshKas['saldo_saat_ini'] ?? 0);
        $namaAkunIni = $freshKas['nama_akun'] ?? ($kas['nama_akun'] ?? 'Kas Utama');

        // Ambil total semua saldo kas & bank saat ini
        $totalSisaKas = (float)($db->table('kas_accounts')->selectSum('saldo_saat_ini')->get()->getRow()->saldo_saat_ini ?? 0);

        // Ambil rincian seluruh akun kas aktif
        $allKasAccounts = $db->table('kas_accounts')->orderBy('saldo_saat_ini', 'DESC')->get()->getResultArray();

        $iconHeader = $isPemasukan ? "🟢 *LAPORAN PEMASUKAN BARU*" : "🔴 *LAPORAN PENGELUARAN BARU*";
        $badgeTipe  = $isPemasukan ? "Pemasukan (+)" : "Pengeluaran (-)";
        $labelKas   = $isPemasukan ? "Masuk ke Kas/Bank" : "Sumber Kas/Bank";
        $nominal    = format_rupiah($trx['jumlah'] ?? 0);
        $tglIndo    = format_tanggal_indo($trx['tanggal'] ?? date('Y-m-d'), true);
        $noTrx      = $trx['nomor_transaksi'] ?? '-';
        $judul      = $trx['judul'] ?? '-';
        $kategori   = $cat['nama_kategori'] ?? 'Umum';
        $penginput  = $trx['nama_penginput'] ?? 'Admin';
        $keterangan = !empty($trx['keterangan']) ? $trx['keterangan'] : '-';

        $text = "{$iconHeader}\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "📌 *No. Trx:* `{$noTrx}`\n";
        $text .= "📅 *Tanggal:* {$tglIndo}\n";
        $text .= "💸 *Nominal:* *{$nominal}* ({$badgeTipe})\n";
        $text .= "🏷️ *Kategori:* {$kategori}\n";
        $text .= "📋 *Keperluan:* {$judul}\n";
        $text .= "🏦 *{$labelKas}:* {$namaAkunIni}\n";
        if ($keterangan !== '-') {
            $text .= "📝 *Catatan:* {$keterangan}\n";
        }
        if ($inv && !empty($inv['nama_investor'])) {
            $text .= "👤 *Investor Terkait:* {$inv['nama_investor']}\n";
        }
        $text .= "👤 *Diinput Oleh:* {$penginput}\n\n";

        // Bagian Posisi Sisa Kas Yang Ada
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "💰 *POSISI SISA KAS TERKINI*\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $iconSaldoAkun = $isPemasukan ? "📈" : "📉";
        $text .= "{$iconSaldoAkun} *Sisa Saldo {$namaAkunIni}:*\n➔ *" . format_rupiah($sisaAkunIni) . "*\n\n";
        $text .= "💎 *Total Kas & Bank Keseluruhan:*\n➔ *" . format_rupiah($totalSisaKas) . "*\n\n";

        if (!empty($allKasAccounts)) {
            $text .= "*Rincian Sisa Kas per Rekening:*\n";
            foreach ($allKasAccounts as $acc) {
                $isCurrent = ($acc['id'] == $kasId) ? " *(Terdampak)*" : "";
                $text .= "• {$acc['nama_akun']}: " . format_rupiah($acc['saldo_saat_ini']) . "{$isCurrent}\n";
            }
        }

        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "_Sistem Monitoring Keuangan Maijek_ 🚀";

        return $this->sendMessage($text);
    }

    /**
     * Kirim notifikasi suntikan modal baru dari investor beserta posisi kas terkini
     */
    public function sendCapitalNotice(array $inv, float $amount, array $kas, string $tgl, ?string $ket = null): array
    {
        if (!$this->isReady() || !$this->notifyCapital) {
            return ['success' => false, 'message' => 'Notifikasi suntik modal dinonaktifkan.'];
        }

        $db = \Config\Database::connect();
        $kasId       = $kas['id'] ?? null;
        $freshKas    = $kasId ? $db->table('kas_accounts')->where('id', $kasId)->get()->getRowArray() : $kas;
        $sisaAkunIni = (float)($freshKas['saldo_saat_ini'] ?? 0);
        $namaAkunIni = $freshKas['nama_akun'] ?? ($kas['nama_akun'] ?? 'Kas Penampungan');
        $totalSisaKas = (float)($db->table('kas_accounts')->selectSum('saldo_saat_ini')->get()->getRow()->saldo_saat_ini ?? 0);
        $allKasAccounts = $db->table('kas_accounts')->orderBy('saldo_saat_ini', 'DESC')->get()->getResultArray();

        $namaInv     = $inv['nama_investor'] ?? 'Investor';
        $kodeInv     = $inv['kode_investor'] ?? '-';
        $nominal     = format_rupiah($amount);
        $totalModal  = format_rupiah($inv['total_investasi'] ?? $amount);
        $share       = ($inv['persentase_bagi_hasil'] ?? 0) . '%';
        $tglIndo     = format_tanggal_indo($tgl, true);
        $keterangan  = $ket ?: 'Suntikan modal ekspansi operasional';

        $text = "💼 *SUNTIKAN MODAL INVESTOR BARU*\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "👤 *Investor:* *{$namaInv}* (`{$kodeInv}`)\n";
        $text .= "💰 *Nominal Dana Masuk:* *{$nominal}*\n";
        $text .= "📈 *Akumulasi Modal Investor:* {$totalModal}\n";
        $text .= "📊 *Porsi Bagi Hasil:* {$share}\n";
        $text .= "🏦 *Diterima di Kas:* {$namaAkunIni}\n";
        $text .= "📅 *Tanggal Efektif:* {$tglIndo}\n";
        $text .= "📝 *Keterangan:* {$keterangan}\n\n";

        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "💰 *POSISI SISA KAS TERKINI*\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "📈 *Sisa Saldo {$namaAkunIni}:*\n➔ *" . format_rupiah($sisaAkunIni) . "*\n\n";
        $text .= "💎 *Total Kas & Bank Keseluruhan:*\n➔ *" . format_rupiah($totalSisaKas) . "*\n\n";

        if (!empty($allKasAccounts)) {
            $text .= "*Rincian Sisa Kas per Rekening:*\n";
            foreach ($allKasAccounts as $acc) {
                $isCurrent = ($acc['id'] == $kasId) ? " *(Terdampak)*" : "";
                $text .= "• {$acc['nama_akun']}: " . format_rupiah($acc['saldo_saat_ini']) . "{$isCurrent}\n";
            }
        }

        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "Terima kasih atas dukungan investasi bagi pertumbuhan armada Maijek Nusantara! 🛵\n";
        $text .= "_Sistem Monitoring Keuangan Maijek_";

        return $this->sendMessage($text);
    }

    /**
     * Kirim notifikasi distribusi dividen / bagi hasil beserta sisa kas terkini
     */
    public function sendDividendNotice(string $periode, float $totalPool, array $penerima, array $kas): array
    {
        if (!$this->isReady() || !$this->notifyDividend) {
            return ['success' => false, 'message' => 'Notifikasi dividen dinonaktifkan.'];
        }

        $db = \Config\Database::connect();
        $kasId       = $kas['id'] ?? null;
        $freshKas    = $kasId ? $db->table('kas_accounts')->where('id', $kasId)->get()->getRowArray() : $kas;
        $sisaAkunIni = (float)($freshKas['saldo_saat_ini'] ?? 0);
        $namaAkunIni = $freshKas['nama_akun'] ?? ($kas['nama_akun'] ?? 'Kas Operasional');
        $totalSisaKas = (float)($db->table('kas_accounts')->selectSum('saldo_saat_ini')->get()->getRow()->saldo_saat_ini ?? 0);
        $allKasAccounts = $db->table('kas_accounts')->orderBy('saldo_saat_ini', 'DESC')->get()->getResultArray();

        $nominalPool = format_rupiah($totalPool);
        $tglIndo     = format_tanggal_indo(date('Y-m-d'), true);

        $text = "🎉 *DISTRIBUSI DIVIDEN INVESTOR CAIR*\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "🗓️ *Periode Dividen:* *{$periode}*\n";
        $text .= "💵 *Total Dana Cair:* *{$nominalPool}*\n";
        $text .= "🏦 *Sumber Dana:* {$namaAkunIni}\n";
        $text .= "📅 *Tanggal Pencairan:* {$tglIndo}\n\n";
        $text .= "*Rincian Pembagian per Investor:*\n";

        $no = 1;
        foreach ($penerima as $p) {
            $nama = $p['nama_investor'] ?? 'Investor';
            $nom  = format_rupiah($p['nominal_dividen'] ?? 0);
            $pct  = ($p['persentase'] ?? 0) . '%';
            $text .= "{$no}. *{$nama}* ({$pct}) ➔ *{$nom}*\n";
            $no++;
        }

        $text .= "\n━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "💰 *POSISI SISA KAS TERKINI*\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "📉 *Sisa Saldo {$namaAkunIni}:*\n➔ *" . format_rupiah($sisaAkunIni) . "*\n\n";
        $text .= "💎 *Total Kas & Bank Keseluruhan:*\n➔ *" . format_rupiah($totalSisaKas) . "*\n\n";

        if (!empty($allKasAccounts)) {
            $text .= "*Rincian Sisa Kas per Rekening:*\n";
            foreach ($allKasAccounts as $acc) {
                $isCurrent = ($acc['id'] == $kasId) ? " *(Terdampak)*" : "";
                $text .= "• {$acc['nama_akun']}: " . format_rupiah($acc['saldo_saat_ini']) . "{$isCurrent}\n";
            }
        }

        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "Dana dividen telah didebet dari rekening kas dan dibukukan ke portofolio masing-masing investor.\n";
        $text .= "_Sistem Monitoring Keuangan Maijek_";

        return $this->sendMessage($text);
    }

    /**
     * Kirim ringkasan arus kas harian ke grup WhatsApp
     */
    public function sendDailySummaryNotice(): array
    {
        if (!$this->isReady()) {
            return ['success' => false, 'message' => 'WhatsApp gateway belum dikonfigurasi.'];
        }

        $db = \Config\Database::connect();
        $today = date('Y-m-d');
        $tglIndo = format_tanggal_indo($today, true);

        // Pemasukan hari ini
        $pemasukan = $db->table('transactions')
            ->where('tipe', 'pemasukan')
            ->where('tanggal', $today)
            ->selectSum('jumlah')
            ->get()->getRow()->jumlah ?? 0;

        // Pengeluaran hari ini
        $pengeluaran = $db->table('transactions')
            ->where('tipe', 'pengeluaran')
            ->where('tanggal', $today)
            ->selectSum('jumlah')
            ->get()->getRow()->jumlah ?? 0;

        $netFlow = (float)$pemasukan - (float)$pengeluaran;

        // Saldo total kas saat ini
        $totalSaldoKas = $db->table('kas_accounts')->selectSum('saldo_saat_ini')->get()->getRow()->saldo_saat_ini ?? 0;

        // Akun-akun kas
        $kasAccounts = $db->table('kas_accounts')->get()->getResultArray();

        $text = "📊 *LAPORAN ARUS KAS HARIAN MAIJEK*\n";
        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "📅 *Tanggal:* {$tglIndo}\n\n";
        $text .= "🟢 *Pemasukan Hari Ini:* " . format_rupiah($pemasukan) . "\n";
        $text .= "🔴 *Pengeluaran Hari Ini:* " . format_rupiah($pengeluaran) . "\n";
        $text .= "📈 *Net Flow Hari Ini:* " . format_rupiah($netFlow) . "\n";
        $text .= "💰 *Total Likuiditas Kas & Bank:* " . format_rupiah($totalSaldoKas) . "\n\n";
        $text .= "*Rincian Saldo Kas & Bank:*\n";

        foreach ($kasAccounts as $k) {
            $text .= "• {$k['nama_akun']}: " . format_rupiah($k['saldo_saat_ini']) . "\n";
        }

        $text .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $text .= "Pembaruan otomatis dari _Dashboard Keuangan Maijek Nusantara_ 🚀";

        return $this->sendMessage($text);
    }
}
