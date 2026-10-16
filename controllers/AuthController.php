<?php
// ============================================================
// AuthController - Handle login & logout
// ============================================================

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';

$action = $_GET['action'] ?? 'login';

if ($action === 'logout') {
    session_destroy();
    redirect(BASE_URL . '/views/auth/login.php');
}

if ($action === 'login' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $errors   = [];

    if (empty($username)) $errors[] = 'Username wajib diisi.';
    if (empty($password)) $errors[] = 'Password wajib diisi.';

    if (empty($errors)) {
        $db   = getDB();
        $stmt = $db->prepare("SELECT * FROM users WHERE username = ? LIMIT 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);
            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['nama']     = $user['nama'];
            $_SESSION['role']     = $user['role'];
            redirect(BASE_URL . '/views/dashboard/index.php');
        } else {
            $_SESSION['login_error']    = 'Username atau password salah.';
            $_SESSION['login_username'] = $username;
            redirect(BASE_URL . '/views/auth/login.php');
        }
    } else {
        $_SESSION['login_error']    = implode(' ', $errors);
        $_SESSION['login_username'] = $username;
        redirect(BASE_URL . '/views/auth/login.php');
    }
}
