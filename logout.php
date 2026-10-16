<?php
// Shortcut logout
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/config/session.php';
require_once __DIR__ . '/config/functions.php';
session_destroy();
redirect(BASE_URL . '/views/auth/login.php');
