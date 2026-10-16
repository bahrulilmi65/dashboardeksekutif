<?php
// ============================================================
// Konfigurasi Session
// ============================================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cek apakah user sudah login
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Redirect jika belum login
function requireLogin() {
    if (!isLoggedIn()) {
        header('Location: ' . BASE_URL . '/views/auth/login.php');
        exit();
    }
}

// Cek role user
function hasRole($role) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role;
}

// Hanya admin yang boleh akses
function requireAdmin() {
    requireLogin();
    if (!hasRole('admin')) {
        $_SESSION['flash_error'] = 'Akses ditolak. Halaman ini hanya untuk Admin.';
        header('Location: ' . BASE_URL . '/views/dashboard/index.php');
        exit();
    }
}

// Set flash message
function setFlash($type, $message) {
    $_SESSION['flash_' . $type] = $message;
}

// Get dan hapus flash message
function getFlash($type) {
    if (isset($_SESSION['flash_' . $type])) {
        $msg = $_SESSION['flash_' . $type];
        unset($_SESSION['flash_' . $type]);
        return $msg;
    }
    return null;
}

// Tampilkan flash message sebagai HTML
function showFlash() {
    $html = '';
    foreach (['success', 'error', 'warning', 'info'] as $type) {
        $msg = getFlash($type);
        if ($msg) {
            $bsClass = [
                'success' => 'success',
                'error'   => 'danger',
                'warning' => 'warning',
                'info'    => 'info',
            ][$type];
            $icon = [
                'success' => 'check-circle',
                'error'   => 'times-circle',
                'warning' => 'exclamation-triangle',
                'info'    => 'info-circle',
            ][$type];
            $html .= '<div class="alert alert-' . $bsClass . ' alert-dismissible fade show shadow-sm" role="alert">
                <i class="fas fa-' . $icon . ' me-2"></i>' . htmlspecialchars($msg) . '
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>';
        }
    }
    return $html;
}
