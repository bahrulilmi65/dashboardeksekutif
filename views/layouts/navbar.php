<?php
$pageTitle = $pageTitle ?? 'Dashboard';
$role      = $_SESSION['role'] ?? 'pimpinan';
$nama      = $_SESSION['nama'] ?? 'Pengguna';
?>
<!-- ============================================================
     TOP NAVBAR
============================================================ -->
<nav class="top-navbar">
    <div class="navbar-left">
        <button class="navbar-toggle" onclick="toggleSidebar()" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="navbar-breadcrumb">
            <span class="breadcrumb-title"><?= htmlspecialchars($pageTitle) ?></span>
        </div>
    </div>
    <div class="navbar-right">
        <!-- Tanggal -->
        <div class="navbar-date d-none d-md-flex">
            <i class="fas fa-calendar-alt me-2 text-primary"></i>
            <span id="currentDate"></span>
        </div>

        <!-- User Dropdown -->
        <div class="navbar-user dropdown">
            <button class="navbar-user-btn dropdown-toggle" data-bs-toggle="dropdown">
                <div class="navbar-user-avatar">
                    <i class="fas fa-user"></i>
                </div>
                <div class="navbar-user-info d-none d-md-block">
                    <span class="user-name"><?= htmlspecialchars($nama) ?></span>
                    <span class="user-role"><?= $role === 'admin' ? 'Administrator' : 'Pimpinan' ?></span>
                </div>
                <i class="fas fa-chevron-down ms-1 small"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end dropdown-menu-user shadow">
                <li>
                    <div class="dropdown-header">
                        <div class="fw-semibold"><?= htmlspecialchars($nama) ?></div>
                        <small class="text-muted"><?= ucfirst($role) ?></small>
                    </div>
                </li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a class="dropdown-item text-danger" href="javascript:void(0)" onclick="confirmLogout()">
                        <i class="fas fa-sign-out-alt me-2"></i>Keluar
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Page Wrapper -->
<div class="page-wrapper">
