<?php
// ============================================================
// Konfigurasi Database
// ============================================================

define('DB_HOST', 'localhost');
define('DB_NAME', 'dashboard_eksekutif');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

function getDB() {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            die('<div style="font-family:sans-serif;padding:20px;background:#f8d7da;color:#721c24;border:1px solid #f5c6cb;border-radius:5px;margin:20px;">
                <strong>Koneksi Database Gagal!</strong><br>
                Pesan: ' . htmlspecialchars($e->getMessage()) . '<br><br>
                Pastikan MySQL berjalan dan konfigurasi di <code>config/database.php</code> sudah benar.
            </div>');
        }
    }
    return $pdo;
}
