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
            padding: 26px 24px;
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
            padding: 24px;
        }

        .form-control {
            font-size: 12px;
            padding: 8px 12px;
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
            padding: 8px 14px;
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

        .demo-box {
            background: #f8fafc;
            border: 1px dashed #cbd5e1;
            border-radius: 8px;
            padding: 10px 12px;
            margin-top: 18px;
            font-size: 11px;
        }

        .demo-btn {
            font-size: 10.5px;
            padding: 3px 8px;
            border-radius: 4px;
            font-weight: 600;
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
                <div class="alert alert-danger py-2 px-3 mb-3 border-0" style="font-size: 11.5px; background: #ffe4e6; color: #9f1239;" role="alert">
                    <i class="bi bi-exclamation-circle-fill me-1"></i> <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success py-2 px-3 mb-3 border-0" style="font-size: 11.5px; background: #d1fae5; color: #065f46;" role="alert">
                    <i class="bi bi-check-circle-fill me-1"></i> <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <form action="<?= base_url('auth/process-login') ?>" method="POST">
                <?= csrf_field() ?>
                
                <div class="mb-3">
                    <label class="form-label text-secondary fw-semibold mb-1" style="font-size: 11px;">Username / Email</label>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-person-fill"></i></span>
                        <input type="text" name="username" id="inputUsername" class="form-control border-start-0" placeholder="Masukkan username" value="<?= old('username', 'admin') ?>" required autofocus>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="d-flex justify-content-between align-items-center mb-1">
                        <label class="form-label text-secondary fw-semibold mb-0" style="font-size: 11px;">Kata Sandi</label>
                    </div>
                    <div class="input-group">
                        <span class="input-group-text bg-light text-muted border-end-0"><i class="bi bi-lock-fill"></i></span>
                        <input type="password" name="password" id="inputPassword" class="form-control border-start-0" placeholder="Masukkan password" value="admin123" required>
                    </div>
                </div>

                <button type="submit" class="btn btn-emerald w-100 d-flex align-items-center justify-content-center gap-2 mt-4">
                    <i class="bi bi-box-arrow-in-right"></i>
                    <span>Masuk ke Dashboard</span>
                </button>
            </form>

            <!-- Quick Demo Login Presets -->
            <div class="demo-box">
                <div class="d-flex align-items-center justify-content-between mb-2">
                    <span class="fw-bold text-dark"><i class="bi bi-lightning-charge-fill text-warning me-1"></i> Akun Demo Cepat:</span>
                    <span class="text-muted" style="font-size: 10px;">Klik untuk isi form</span>
                </div>
                <div class="d-flex gap-2">
                    <button type="button" class="btn btn-outline-success demo-btn flex-fill" onclick="setDemo('admin', 'admin123')">
                        <i class="bi bi-shield-check"></i> Super Admin
                    </button>
                    <button type="button" class="btn btn-outline-primary demo-btn flex-fill" onclick="setDemo('finance', 'finance123')">
                        <i class="bi bi-cash-stack"></i> Staff Finance
                    </button>
                    <button type="button" class="btn btn-outline-dark demo-btn flex-fill" onclick="setDemo('investor_bahar', 'investor123')">
                        <i class="bi bi-person-badge"></i> Investor
                    </button>
                </div>
            </div>

            <div class="text-center mt-4 text-muted micro-text">
                &copy; <?= date('Y') ?> Maijek Transportasi Nusantara • All Rights Reserved
            </div>
        </div>
    </div>

    <script>
        function setDemo(user, pass) {
            document.getElementById('inputUsername').value = user;
            document.getElementById('inputPassword').value = pass;
        }
    </script>
</body>
</html>
