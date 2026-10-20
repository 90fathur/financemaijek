<?php

namespace App\Controllers;

use App\Libraries\WhatsAppService;
use App\Models\SettingModel;

class WhatsAppSettingController extends BaseController
{
    protected SettingModel $settingModel;
    protected WhatsAppService $waService;

    public function __construct()
    {
        $this->settingModel = new SettingModel();
        $this->waService    = new WhatsAppService();
        helper(['finance', 'form', 'url']);
    }

    /**
     * Tampilkan formulir pengaturan WhatsApp Dinamis
     */
    public function index()
    {
        $settings = [
            'wa_enabled'         => $this->settingModel->getVal('wa_enabled', '0'),
            'wa_provider'        => $this->settingModel->getVal('wa_provider', 'cloudchat'),
            'wa_api_url'         => $this->settingModel->getVal('wa_api_url', 'https://app.cloudchat.id/api/public/v1/messages'),
            'wa_api_key'         => $this->settingModel->getVal('wa_api_key', ''),
            'wa_group_id'        => $this->settingModel->getVal('wa_group_id', ''),
            'wa_notify_income'   => $this->settingModel->getVal('wa_notify_income', '1'),
            'wa_notify_expense'  => $this->settingModel->getVal('wa_notify_expense', '1'),
            'wa_notify_capital'  => $this->settingModel->getVal('wa_notify_capital', '1'),
            'wa_notify_dividend' => $this->settingModel->getVal('wa_notify_dividend', '1'),
        ];

        $data = [
            'title'    => 'Pengaturan Notifikasi WhatsApp Grup',
            'settings' => $settings,
            'isReady'  => $this->waService->isReady(),
        ];

        return view('settings/whatsapp', $data);
    }

    /**
     * Simpan perubahan pengaturan WhatsApp
     */
    public function save()
    {
        $waEnabled        = $this->request->getPost('wa_enabled') ? '1' : '0';
        $waProvider       = $this->request->getPost('wa_provider') ?: 'cloudchat';
        $waApiUrl         = trim((string)$this->request->getPost('wa_api_url')) ?: 'https://app.cloudchat.id/api/public/v1/messages';
        $waApiKey         = trim((string)$this->request->getPost('wa_api_key'));
        $waGroupId        = trim((string)$this->request->getPost('wa_group_id'));
        $waNotifyIncome   = $this->request->getPost('wa_notify_income') ? '1' : '0';
        $waNotifyExpense  = $this->request->getPost('wa_notify_expense') ? '1' : '0';
        $waNotifyCapital  = $this->request->getPost('wa_notify_capital') ? '1' : '0';
        $waNotifyDividend = $this->request->getPost('wa_notify_dividend') ? '1' : '0';

        $this->settingModel->setVal('wa_enabled', $waEnabled);
        $this->settingModel->setVal('wa_provider', $waProvider);
        $this->settingModel->setVal('wa_api_url', $waApiUrl);
        $this->settingModel->setVal('wa_api_key', $waApiKey);
        $this->settingModel->setVal('wa_group_id', $waGroupId);
        $this->settingModel->setVal('wa_notify_income', $waNotifyIncome);
        $this->settingModel->setVal('wa_notify_expense', $waNotifyExpense);
        $this->settingModel->setVal('wa_notify_capital', $waNotifyCapital);
        $this->settingModel->setVal('wa_notify_dividend', $waNotifyDividend);

        return redirect()->to(base_url('settings/whatsapp'))->with('success', 'Konfigurasi WhatsApp Gateway berhasil disimpan!');
    }

    /**
     * Uji kirim pesan langsung ke Grup WhatsApp via CloudChat API
     */
    public function testSend()
    {
        $customMessage = trim((string)$this->request->getPost('test_message'));
        $targetGroup   = trim((string)$this->request->getPost('target_group'));

        if (empty($customMessage)) {
            $db = \Config\Database::connect();
            $totalSisaKas = (float)($db->table('kas_accounts')->selectSum('saldo_saat_ini')->get()->getRow()->saldo_saat_ini ?? 0);
            $allKas = $db->table('kas_accounts')->orderBy('saldo_saat_ini', 'DESC')->get()->getResultArray();

            $kasList = '';
            foreach ($allKas as $k) {
                $kasList .= "• {$k['nama_akun']}: " . format_rupiah($k['saldo_saat_ini']) . "\n";
            }

            $message = "🔔 *TES KONEKSI WHATSAPP GATEWAY MAIJEK*\n" .
                "━━━━━━━━━━━━━━━━━━━━━\n" .
                "Halo Tim & Manajemen Maijek! Pesan ini dikirim secara dinamis via *CloudChat.id*.\n\n" .
                "📅 *Waktu:* " . format_tanggal_indo(date('Y-m-d'), true) . " - " . date('H:i') . " WITA\n" .
                "✅ *Status:* Terhubung & Aktif\n\n" .
                "━━━━━━━━━━━━━━━━━━━━━\n" .
                "💰 *POSISI SISA KAS SAAT INI*\n" .
                "━━━━━━━━━━━━━━━━━━━━━\n" .
                "💎 *Total Kas & Bank:* *" . format_rupiah($totalSisaKas) . "*\n\n" .
                "*Rincian Rekening Kas:*\n" .
                ($kasList ?: "• Belum ada akun kas terdaftar\n") .
                "━━━━━━━━━━━━━━━━━━━━━\n" .
                "_Maijek Finance System_ 🚀";
        } else {
            $message = $customMessage;
        }

        // Instansiasi ulang untuk memuat setting terbaru
        $service = new WhatsAppService();
        $result  = $service->sendMessage($message, $targetGroup ?: null);

        if ($result['success']) {
            return redirect()->to(base_url('settings/whatsapp'))->with('success', 'Berhasil! Pesan tes telah terkirim ke Grup WhatsApp.');
        }

        return redirect()->to(base_url('settings/whatsapp'))->with('error', 'Gagal mengirim pesan tes: ' . ($result['message'] ?? 'Error tidak diketahui'));
    }

    /**
     * Kirim Laporan Arus Kas Hari Ini ke Grup WhatsApp
     */
    public function sendDailySummary()
    {
        $service = new WhatsAppService();
        $result  = $service->sendDailySummaryNotice();

        if ($result['success']) {
            return redirect()->back()->with('success', 'Ringkasan keuangan hari ini berhasil dikirim ke Grup WhatsApp!');
        }

        return redirect()->back()->with('error', 'Gagal mengirim rekap ke grup WA: ' . ($result['message'] ?? 'Periksa konfigurasi WhatsApp'));
    }
}
