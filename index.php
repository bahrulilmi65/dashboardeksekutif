<?php
// ============================================================
// Entry Point - Redirect ke dashboard atau login
// ============================================================
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/functions.php';

if (isLoggedIn()) {
    redirect(BASE_URL . '/views/dashboard/index.php');
} else {
    redirect(BASE_URL . '/views/auth/login.php');
}
