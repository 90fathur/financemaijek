<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Monitoring Keuangan & Investasi Maijek</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Bootstrap 5.3.3 & Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
            background: linear-gradient(135deg, #090d16 0%, #0f172a 50%, #064e3b 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
            font-size: 12.5px;
            color: #1e293b;
        }

        .login-card {
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
            width: 100%;
            max-width: 420px;
            overflow: hidden;
            border: 1px solid rgba(255, 255, 255, 0.1);
        }

        .login-header {
            background: #0f172a;
            padding: 28px 24px;
            text-align: center;
            border-bottom: 1px solid #1e293b;
            position: relative;
        }

        .brand-logo {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #10b981 0%, #059669 100%);
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 24px;
            margin-bottom: 12px;
            box-shadow: 0 6px 15px rgba(16, 185, 129, 0.35);
        }

        .login-body {
            padding: 28px 24px;
        }

        .form-control {
            font-size: 12px;
            padding: 9px 12px;
            border-radius: 6px;
            border-color: #cbd5e1;
        }
        .form-control:focus {
            border-color: #10b981;
            box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.2);
        }

        .btn-emerald {
            background-color: #059669;
            color: #ffffff;
            font-weight: 600;
            padding: 9px 14px;
            font-size: 12.5px;
            border-radius: 6px;
            border: none;
            transition: all 0.15s ease;
        }
        .btn-emerald:hover {
            background-color: #047857;
            color: #ffffff;
            transform: translateY(-1px);
            box-shadow: 0 4px 12px rgba(5, 150, 105, 0.3);
        }

        .micro-text {
            font-size: 10.5px;
            letter-spacing: 0.02em;
        }
    </style>
</head>
<body>

    <div class="login-card">
        <div class="login-header">
            <div class="brand-logo">
                <i class="bi bi-send-fill" style="transform: rotate(45deg);"></i>
            </div>
            <h4 class="text-white fw-bold mb-1" style="font-size: 16px; letter-spacing: -0.01em;">MAIJEK FINANCE</h4>
            <div class="text-emerald" style="font-size: 11px; font-weight: 600;">Sistem Monitoring Keuangan & Investasi</div>
        </div>

        <div class="login-body">
            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger py-2 px-3 mb-3 border-0 d-flex align-items-center gap-2" style="font-size: 11.5px; background: #ffe4e6; color: #9f1239;" role="alert">
                    <i class="bi bi-exclamation-circle-fill flex-shrink-0"></i>
                    <div><?= session()->getFlashdata('error') ?></div>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success py-2 px-3 mb-3 border-0 d-flex align-items-center gap-2" style="font-size: 11.5px; background: #d1fae5; color: #065f46;" role="alert">
                    <i class="bi bi-check-circle-fill flex-shrink-0"></i>
                    <div><?= session()->getFlashdata('success') ?></div>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/process-login') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 11px;">Username atau Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="username" id="inputUsername" class="form-control border-start-0" placeholder="Masukkan username atau email" value="<?= old('username') ?>" required autofocus autocomplete="username">
                    </div>
                </div>

                <div class="mb-4">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label text-secondary fw-semibold mb-0" style="font-size: 11px;">Kata Sandi</label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" id="inputPassword" class="form-control border-start-0 border-end-0" placeholder="Masukkan kata sandi" required autocomplete="current-password">
                        <button class="btn btn-outline-secondary border-start-0 bg-light text-muted" type="button" onclick="togglePasswordVisibility()" title="Lihat kata sandi">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-emerald w-100 d-flex align-items-center justify-content-center gap-2 py-2">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <div class="mt-4 pt-3 border-top text-center text-muted micro-text">
                <div class="d-flex align-items-center justify-content-center gap-1 mb-1 text-secondary" style="font-size: 11px;">
                    <i class="bi bi-shield-lock-fill text-success"></i>
                    <span>Koneksi Aman & Terenkripsi</span>
                </div>
                <div>&copy; <?= date('Y') ?> PT Maijek Transportasi Nusantara</div>
            </div>
        </div>
    </div>

    <script>
        function togglePasswordVisibility() {
            const passInput = document.getElementById('inputPassword');
            const icon = document.getElementById('togglePasswordIcon');
            if (passInput.type === 'password') {
                passInput.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                passInput.type = 'password';
                icon.className = 'bi bi-eye';
            }
        }
    </script>
</body>
</html>
