<?php
$role        = $_SESSION['role']        ?? 'pimpinan';
$nama        = $_SESSION['nama']        ?? 'Pengguna';
$currentPage = $currentPage ?? '';

// Helper: active class
function sidebarActive($page, $current) {
    return ($page === $current) ? 'active' : '';
}
function sidebarParentActive($pages, $current) {
    return in_array($current, $pages) ? 'active' : '';
}
?>
<!-- ============================================================
     SIDEBAR
============================================================ -->
<nav class="sidebar" id="sidebar">
    <!-- Brand -->
    <div class="sidebar-brand">
        <div class="sidebar-brand-icon">
            <i class="fas fa-chart-line"></i>
        </div>
        <div class="sidebar-brand-text">
            <span class="brand-title">Dashboard</span>
            <span class="brand-sub">Eksekutif</span>
        </div>
        <button class="sidebar-close-btn d-lg-none" onclick="closeSidebar()">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- User Info -->
    <div class="sidebar-user">
        <div class="sidebar-user-avatar">
            <i class="fas fa-user-circle"></i>
        </div>
        <div class="sidebar-user-info">
            <div class="sidebar-user-name"><?= htmlspecialchars($nama) ?></div>
            <div class="sidebar-user-role">
                <span class="badge <?= $role === 'admin' ? 'bg-warning text-dark' : 'bg-info' ?> badge-sm">
                    <?= $role === 'admin' ? 'Administrator' : 'Pimpinan' ?>
                </span>
            </div>
        </div>
    </div>

    <!-- Navigation -->
    <div class="sidebar-nav">
        <div class="sidebar-section-label">MENU UTAMA</div>

        <!-- Dashboard -->
        <a href="<?= BASE_URL ?>/views/dashboard/index.php"
           class="sidebar-link <?= sidebarActive('dashboard', $currentPage) ?>">
            <span class="sidebar-link-icon"><i class="fas fa-tachometer-alt"></i></span>
            <span class="sidebar-link-text">Dashboard</span>
        </a>

        <?php if ($role === 'admin'): ?>
        <div class="sidebar-section-label mt-2">MASTER DATA</div>

        <!-- Organisasi -->
        <a href="<?= BASE_URL ?>/views/organisasi/index.php"
           class="sidebar-link <?= sidebarActive('organisasi', $currentPage) ?>">
            <span class="sidebar-link-icon"><i class="fas fa-building"></i></span>
            <span class="sidebar-link-text">Organisasi</span>
        </a>

        <div class="sidebar-section-label mt-2">PENGELOLAAN DANA</div>

        <!-- Pengajuan Dana -->
        <a href="<?= BASE_URL ?>/views/pengajuan/index.php"
           class="sidebar-link <?= sidebarActive('pengajuan', $currentPage) ?>">
            <span class="sidebar-link-icon"><i class="fas fa-file-invoice-dollar"></i></span>
            <span class="sidebar-link-text">Pengajuan Dana</span>
        </a>

        <!-- Pencairan Dana -->
        <a href="<?= BASE_URL ?>/views/pencairan/index.php"
           class="sidebar-link <?= sidebarActive('pencairan', $currentPage) ?>">
            <span class="sidebar-link-icon"><i class="fas fa-money-bill-wave"></i></span>
            <span class="sidebar-link-text">Pencairan Dana</span>
        </a>
        <?php endif; ?>

        <div class="sidebar-section-label mt-2">LAPORAN</div>

        <!-- Laporan Organisasi -->
        <a href="<?= BASE_URL ?>/views/laporan/organisasi.php"
           class="sidebar-link <?= sidebarActive('lap-organisasi', $currentPage) ?>">
            <span class="sidebar-link-icon"><i class="fas fa-file-alt"></i></span>
            <span class="sidebar-link-text">Lap. Organisasi</span>
        </a>

        <!-- Laporan Pengajuan -->
        <a href="<?= BASE_URL ?>/views/laporan/pengajuan.php"
           class="sidebar-link <?= sidebarActive('lap-pengajuan', $currentPage) ?>">
            <span class="sidebar-link-icon"><i class="fas fa-file-chart-line"></i></span>
            <span class="sidebar-link-text">Lap. Pengajuan</span>
        </a>

        <!-- Laporan Pencairan -->
        <a href="<?= BASE_URL ?>/views/laporan/pencairan.php"
           class="sidebar-link <?= sidebarActive('lap-pencairan', $currentPage) ?>">
            <span class="sidebar-link-icon"><i class="fas fa-file-invoice"></i></span>
            <span class="sidebar-link-text">Lap. Pencairan</span>
        </a>

        <div class="sidebar-divider"></div>

        <!-- Logout -->
        <a href="javascript:void(0)" onclick="confirmLogout()" class="sidebar-link sidebar-link-logout">
            <span class="sidebar-link-icon"><i class="fas fa-sign-out-alt"></i></span>
            <span class="sidebar-link-text">Keluar</span>
        </a>
    </div>
</nav>
