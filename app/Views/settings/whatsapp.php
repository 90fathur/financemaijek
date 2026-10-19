<?= $this->extend('layout/main') ?>

<?= $this->section('content') ?>

<!-- Page Title & Actions -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
    <div>
        <h1 class="h5 mb-0 fw-bold text-dark d-flex align-items-center gap-2">
            <i class="bi bi-whatsapp text-success"></i>
            <span>Pengaturan WhatsApp Gateway Dinamis</span>
        </h1>
        <p class="text-muted small mb-0">Integrasi pengiriman pesan otomatis ke Grup WhatsApp internal & investor melalui <strong>CloudChat.id</strong>.</p>
    </div>
    <div class="d-flex gap-2">
        <form action="<?= base_url('settings/whatsapp/send-daily') ?>" method="POST" onsubmit="return confirm('Kirim ringkasan arus kas hari ini ke grup WhatsApp sekarang?')">
            <?= csrf_field() ?>
            <button type="submit" class="btn btn-outline-success btn-compact d-flex align-items-center gap-1 shadow-sm">
                <i class="bi bi-send-check"></i>
                <span>Kirim Ringkasan Hari Ini ke Grup</span>
            </button>
        </form>
    </div>
</div>

<!-- Status Gateway Indicator -->
<?php if ($isReady): ?>
    <div class="alert alert-success d-flex align-items-center justify-content-between py-2 px-3 mb-3 border-0 shadow-sm" style="background: #ecfdf5; border-left: 4px solid #10b981 !important; color: #065f46;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-check-circle-fill fs-5 text-success"></i>
            <div>
                <strong class="d-block" style="font-size: 12.5px;">WhatsApp Gateway Aktif & Siap Mengirim Notifikasi</strong>
                <span class="micro-text text-muted">Target Grup: <code><?= esc($settings['wa_group_id']) ?></code> • Provider: CloudChat.id</span>
            </div>
        </div>
        <span class="badge bg-success-subtle text-success border border-success-subtle py-1 px-2" style="font-size: 11px;">
            <i class="bi bi-broadcast me-1"></i>ONLINE
        </span>
    </div>
<?php else: ?>
    <div class="alert alert-warning d-flex align-items-center justify-content-between py-2 px-3 mb-3 border-0 shadow-sm" style="background: #fffbeb; border-left: 4px solid #f59e0b !important; color: #92400e;">
        <div class="d-flex align-items-center gap-2">
            <i class="bi bi-exclamation-triangle-fill fs-5 text-warning"></i>
            <div>
                <strong class="d-block" style="font-size: 12.5px;">WhatsApp Gateway Belum Dikonfigurasi Lengkap</strong>
                <span class="micro-text text-muted">Masukkan API Key dari CloudChat.id dan ID Grup WhatsApp target di bawah ini, lalu simpan.</span>
            </div>
        </div>
        <span class="badge bg-warning-subtle text-warning border border-warning-subtle py-1 px-2" style="font-size: 11px;">
            <i class="bi bi-dash-circle me-1"></i>STANDBY
        </span>
    </div>
<?php endif; ?>

<div class="row g-3">
    <!-- Form Pengaturan Utama -->
    <div class="col-12 col-lg-7">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white border-bottom py-2 px-3 d-flex align-items-center justify-content-between">
                <span class="fw-bold text-dark" style="font-size: 13px;">
                    <i class="bi bi-sliders text-emerald me-1"></i> Konfigurasi CloudChat.id API
                </span>
                <span class="badge bg-light text-muted border" style="font-size: 10px;">CloudChat REST API v1</span>
            </div>
            <div class="card-body p-3">
                <form action="<?= base_url('settings/whatsapp/save') ?>" method="POST">
                    <?= csrf_field() ?>

                    <!-- Toggle Aktif/Nonaktif -->
                    <div class="p-2 mb-3 rounded bg-light border d-flex align-items-center justify-content-between">
                        <div>
                            <label class="form-check-label fw-bold text-dark mb-0 d-block" for="waEnabledToggle" style="font-size: 12px;">
                                Aktifkan Notifikasi WhatsApp Otomatis
                            </label>
                            <span class="text-muted micro-text">Kirim notifikasi otomatis ke grup WA setiap ada transaksi / dividen baru</span>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="wa_enabled" value="1" id="waEnabledToggle" role="switch" <?= ($settings['wa_enabled'] === '1') ? 'checked' : '' ?> style="width: 2.5em; height: 1.3em; cursor: pointer;">
                        </div>
                    </div>

                    <!-- API URL Endpoint -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 11px;">
                            Endpoint API URL <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-link-45deg"></i></span>
                            <input type="url" name="wa_api_url" class="form-control" value="<?= esc($settings['wa_api_url']) ?>" required placeholder="https://app.cloudchat.id/api/public/v1/messages">
                        </div>
                        <div class="form-text micro-text text-muted">Default resmi CloudChat: <code>https://app.cloudchat.id/api/public/v1/messages</code></div>
                    </div>

                    <!-- API Key / Bearer Token -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 11px;">
                            API Key / Bearer Token CloudChat <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-key-fill"></i></span>
                            <input type="password" name="wa_api_key" id="inputApiKey" class="form-control" value="<?= esc($settings['wa_api_key']) ?>" required placeholder="Masukkan API Key dari dashboard CloudChat.id">
                            <button class="btn btn-outline-secondary" type="button" onclick="toggleVisibility('inputApiKey', this)">
                                <i class="bi bi-eye"></i>
                            </button>
                        </div>
                        <div class="form-text micro-text text-muted">Didapatkan dari menu <em>API Docs / Integrations / Tokens</em> di akun CloudChat.id Anda.</div>
                    </div>

                    <!-- Group ID / JID -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold text-secondary mb-1" style="font-size: 11px;">
                            WhatsApp Group ID / JID Target <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <span class="input-group-text bg-light text-muted"><i class="bi bi-people-fill text-success"></i></span>
                            <input type="text" name="wa_group_id" class="form-control" value="<?= esc($settings['wa_group_id']) ?>" required placeholder="Contoh: 1203630291823910@g.us">
                        </div>
                        <div class="form-text micro-text text-muted">Format ID Grup WhatsApp pada CloudChat (biasanya berupa angka berakhiran <code>@g.us</code> atau ID grup dari kontak).</div>
                    </div>

                    <hr class="my-3 text-muted opacity-25">

                    <!-- Event Pemicu Notifikasi -->
                    <label class="form-label fw-bold text-dark mb-2" style="font-size: 11.5px;">
                        <i class="bi bi-bell-fill text-amber me-1"></i> Pilih Event yang Mengirimkan Pesan ke Grup:
                    </label>

                    <div class="row g-2 mb-3">
                        <div class="col-12 col-md-6">
                            <div class="form-check p-2 rounded border bg-white d-flex align-items-center gap-2">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="wa_notify_income" value="1" id="eventIncome" <?= ($settings['wa_notify_income'] === '1') ? 'checked' : '' ?>>
                                <label class="form-check-label text-dark" for="eventIncome" style="font-size: 11.5px;">
                                    <span class="fw-semibold text-success">🟢 Pemasukan Baru</span>
                                    <span class="d-block micro-text text-muted">Setoran driver, komisi resto, dll.</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-check p-2 rounded border bg-white d-flex align-items-center gap-2">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="wa_notify_expense" value="1" id="eventExpense" <?= ($settings['wa_notify_expense'] === '1') ? 'checked' : '' ?>>
                                <label class="form-check-label text-dark" for="eventExpense" style="font-size: 11.5px;">
                                    <span class="fw-semibold text-danger">🔴 Pengeluaran Baru</span>
                                    <span class="d-block micro-text text-muted">Biaya server, sewa, belanja operasional</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-check p-2 rounded border bg-white d-flex align-items-center gap-2">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="wa_notify_capital" value="1" id="eventCapital" <?= ($settings['wa_notify_capital'] === '1') ? 'checked' : '' ?>>
                                <label class="form-check-label text-dark" for="eventCapital" style="font-size: 11.5px;">
                                    <span class="fw-semibold text-primary">💼 Suntik Modal Investor</span>
                                    <span class="d-block micro-text text-muted">Penerimaan dana investasi baru</span>
                                </label>
                            </div>
                        </div>

                        <div class="col-12 col-md-6">
                            <div class="form-check p-2 rounded border bg-white d-flex align-items-center gap-2">
                                <input class="form-check-input ms-0 me-2" type="checkbox" name="wa_notify_dividend" value="1" id="eventDividend" <?= ($settings['wa_notify_dividend'] === '1') ? 'checked' : '' ?>>
                                <label class="form-check-label text-dark" for="eventDividend" style="font-size: 11.5px;">
                                    <span class="fw-semibold text-warning">🎉 Pembagian Dividen</span>
                                    <span class="d-block micro-text text-muted">Pencairan bagi hasil investor</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex justify-content-end gap-2 pt-2 border-top">
                        <button type="submit" class="btn btn-emerald btn-compact d-flex align-items-center gap-1 px-4">
                            <i class="bi bi-save"></i>
                            <span>Simpan Konfigurasi</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Uji Coba & Panduan -->
    <div class="col-12 col-lg-5">
        <!-- Card Uji Coba Kirim Pesan -->
        <div class="card border-0 shadow-sm mb-3">
            <div class="card-header bg-white border-bottom py-2 px-3">
                <span class="fw-bold text-dark" style="font-size: 13px;">
                    <i class="bi bi-send-fill text-success me-1"></i> Uji Coba Kirim Pesan (Live Test)
                </span>
            </div>
            <div class="card-body p-3">
                <p class="text-muted small mb-2">Gunakan formulir ini untuk mengetes apakah token dan Group ID target sudah valid dan pesan berhasil masuk ke grup WhatsApp.</p>

                <form action="<?= base_url('settings/whatsapp/test-send') ?>" method="POST">
                    <?= csrf_field() ?>

                    <div class="mb-2">
                        <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 11px;">Pesan Uji Coba (Opsional)</label>
                        <textarea name="test_message" rows="3" class="form-control" style="font-size: 11.5px;" placeholder="Tulis pesan kustom atau biarkan kosong untuk pesan salam otomatis..."></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 11px;">Target Group ID (Opsional ganti sementara)</label>
                        <input type="text" name="target_group" class="form-control" value="<?= esc($settings['wa_group_id']) ?>" placeholder="Gunakan ID default di sebelah atau isi ID lain">
                    </div>

                    <button type="submit" class="btn btn-success btn-compact w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                        <i class="bi bi-whatsapp"></i>
                        <span>Kirim Pesan Uji Coba Sekarang</span>
                    </button>
                </form>
            </div>
        </div>

        <!-- Panduan Singkat CloudChat -->
        <div class="card border-0 shadow-sm bg-light">
            <div class="card-body p-3">
                <h6 class="fw-bold text-dark mb-2" style="font-size: 12px;">
                    <i class="bi bi-info-circle-fill text-blue me-1"></i> Cara Setup CloudChat.id:
                </h6>
                <ol class="small text-muted ps-3 mb-0" style="font-size: 11.5px; line-height: 1.6;">
                    <li>Login ke panel <strong><a href="https://app.cloudchat.id" target="_blank" class="text-decoration-none text-emerald">app.cloudchat.id</a></strong>.</li>
                    <li>Hubungkan nomor bot WhatsApp Anda via menu <strong>Devices / WhatsApp QR Code</strong>.</li>
                    <li>Salin <strong>API Key</strong> dari menu <em>API & Integrations</em> dan tempel di kolom sebelah kiri.</li>
                    <li>Masukkan nomor bot Anda ke dalam <strong>Grup WhatsApp</strong> yang ingin menerima notifikasi keuangan Maijek.</li>
                    <li>Dapatkan <strong>Group ID</strong> (format <code>... @g.us</code>) dari menu Contacts/Groups di CloudChat, lalu tempel di kolom Group ID.</li>
                    <li>Klik <strong>Kirim Pesan Uji Coba</strong> untuk memverifikasi!</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<script>
    function toggleVisibility(inputId, btn) {
        const input = document.getElementById(inputId);
        const icon = btn.querySelector('i');
        if (input.type === 'password') {
            input.type = 'text';
            icon.className = 'bi bi-eye-slash';
        } else {
            input.type = 'password';
            icon.className = 'bi bi-eye';
        }
    }
</script>

<?= $this->endSection() ?>
