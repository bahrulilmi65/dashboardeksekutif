<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';

// Jika sudah login, redirect ke dashboard
if (isLoggedIn()) {
    redirect(BASE_URL . '/views/dashboard/index.php');
}

$error    = getFlash('error') ?? (isset($_SESSION['login_error']) ? $_SESSION['login_error'] : null);
$oldUser  = $_SESSION['login_username'] ?? '';
unset($_SESSION['login_error'], $_SESSION['login_username']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?= APP_NAME ?></title>
    <!-- Bootstrap 5 -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', sans-serif; }
        body {
            min-height: 100vh;
            background: linear-gradient(135deg, #1a3a5c 0%, #2563eb 50%, #1e40af 100%);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-wrapper { width: 100%; max-width: 440px; padding: 1rem; }
        .login-card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
            overflow: hidden;
        }
        .login-header {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            padding: 2.5rem 2rem 2rem;
            text-align: center;
            color: #fff;
        }
        .login-header .logo-icon {
            width: 70px; height: 70px;
            background: rgba(255,255,255,0.2);
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            backdrop-filter: blur(4px);
        }
        .login-header h4 { font-weight: 700; margin-bottom: 0.25rem; font-size: 1.3rem; }
        .login-header p { opacity: .8; font-size: .85rem; margin: 0; }
        .login-body { padding: 2rem; }
        .form-label { font-weight: 500; font-size: .875rem; color: #374151; }
        .form-control {
            border-radius: 8px;
            border: 1.5px solid #e5e7eb;
            padding: .65rem 1rem;
            font-size: .9rem;
            transition: border-color .2s, box-shadow .2s;
        }
        .form-control:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37,99,235,.12);
        }
        .input-group-text {
            background: #f9fafb;
            border: 1.5px solid #e5e7eb;
            border-right: none;
            border-radius: 8px 0 0 8px;
            color: #6b7280;
        }
        .input-group .form-control { border-left: none; border-radius: 0 8px 8px 0; }
        .btn-login {
            background: linear-gradient(135deg, #1e40af, #2563eb);
            border: none;
            border-radius: 8px;
            padding: .75rem;
            font-weight: 600;
            font-size: .95rem;
            letter-spacing: .3px;
            transition: opacity .2s, transform .1s;
        }
        .btn-login:hover { opacity: .9; transform: translateY(-1px); }
        .demo-box {
            background: #f0f7ff;
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            padding: .9rem 1rem;
            font-size: .82rem;
            color: #1e40af;
        }
        .demo-box table td { padding: 1px 6px; }
        .footer-text { text-align: center; font-size: .78rem; color: #9ca3af; padding-bottom: 1rem; }
    </style>
</head>
<body>
<div class="login-wrapper">
    <div class="login-card">
        <!-- Header -->
        <div class="login-header">
            <div class="logo-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <h4><?= APP_NAME ?></h4>
            <p>Pengelolaan Dana Organisasi Kepemudaan</p>
        </div>

        <!-- Body -->
        <div class="login-body">
            <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show py-2 px-3" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?= htmlspecialchars($error) ?>
                <button type="button" class="btn-close py-2" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/controllers/AuthController.php?action=login" method="POST" novalidate>
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                        <input type="text" name="username" class="form-control"
                               placeholder="Masukkan username"
                               value="<?= htmlspecialchars($oldUser) ?>"
                               required autofocus>
                    </div>
                </div>
                <div class="mb-4">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" name="password" id="passwordInput"
                               class="form-control" placeholder="Masukkan password" required>
                        <button type="button" class="btn btn-outline-secondary border-start-0"
                                onclick="togglePassword()" style="border:1.5px solid #e5e7eb;border-left:none;border-radius:0 8px 8px 0;">
                            <i class="fas fa-eye" id="eyeIcon"></i>
                        </button>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-login w-100 text-white">
                    <i class="fas fa-sign-in-alt me-2"></i>Masuk
                </button>
            </form>

            <hr class="my-3">

            <!-- Demo Credentials -->
            <div class="demo-box">
                <div class="fw-semibold mb-1"><i class="fas fa-info-circle me-1"></i>Akun Demo</div>
                <table>
                    <tr><td>Admin</td><td>:</td><td><strong>admin</strong> / <strong>password</strong></td></tr>
                    <tr><td>Pimpinan</td><td>:</td><td><strong>pimpinan</strong> / <strong>password</strong></td></tr>
                </table>
            </div>
        </div>
    </div>
    <div class="footer-text mt-3 text-white-50">
        &copy; <?= date('Y') ?> <?= APP_NAME ?> &mdash; v<?= APP_VERSION ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script>
function togglePassword() {
    const input = document.getElementById('passwordInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.replace('fa-eye', 'fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.replace('fa-eye-slash', 'fa-eye');
    }
}
</script>
</body>
</html>
