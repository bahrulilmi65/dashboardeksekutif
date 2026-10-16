<?php
// ============================================================
// Helper Functions
// ============================================================

// Base URL aplikasi (sesuaikan jika nama folder berbeda)
define('BASE_URL', '/dashboardeksekutif');
define('APP_NAME', 'Dashboard Eksekutif');
define('APP_VERSION', '1.0.0');

// Format rupiah
function formatRupiah($angka) {
    return 'Rp ' . number_format((float)$angka, 0, ',', '.');
}

// Format tanggal ke bahasa Indonesia
function formatTanggal($tanggal, $format = 'd F Y') {
    if (empty($tanggal)) return '-';
    $bulan = [
        '01' => 'Januari', '02' => 'Februari', '03' => 'Maret',
        '04' => 'April',   '05' => 'Mei',       '06' => 'Juni',
        '07' => 'Juli',    '08' => 'Agustus',   '09' => 'September',
        '10' => 'Oktober', '11' => 'November',  '12' => 'Desember',
    ];
    $ts    = strtotime($tanggal);
    $hari  = date('d', $ts);
    $bln   = $bulan[date('m', $ts)];
    $tahun = date('Y', $ts);
    return "$hari $bln $tahun";
}

// Badge status pengajuan
function badgeStatus($status) {
    $map = [
        'Menunggu'  => ['warning',  'clock'],
        'Disetujui' => ['success',  'check-circle'],
        'Ditolak'   => ['danger',   'times-circle'],
    ];
    $cfg  = $map[$status] ?? ['secondary', 'question-circle'];
    return '<span class="badge bg-' . $cfg[0] . '"><i class="fas fa-' . $cfg[1] . ' me-1"></i>' . $status . '</span>';
}

// Sanitasi input
function clean($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

// Redirect
function redirect($url) {
    header('Location: ' . $url);
    exit();
}

// Generate nomor otomatis
function generateNomor($prefix, $table, $kolom) {
    $db    = getDB();
    $tahun = date('Y');
    $like  = $prefix . '/' . $tahun . '/%';
    $stmt  = $db->prepare("SELECT COUNT(*) as total FROM `$table` WHERE `$kolom` LIKE ?");
    $stmt->execute([$like]);
    $row   = $stmt->fetch();
    $urut  = str_pad(($row['total'] + 1), 3, '0', STR_PAD_LEFT);
    return $prefix . '/' . $tahun . '/' . $urut;
}

// Pagination helper
function paginate($total, $perPage, $currentPage, $url) {
    $totalPages = ceil($total / $perPage);
    if ($totalPages <= 1) return '';
    $html = '<nav><ul class="pagination pagination-sm justify-content-end mb-0">';
    // Prev
    $prevDisabled = $currentPage <= 1 ? 'disabled' : '';
    $prevPage     = max(1, $currentPage - 1);
    $html .= "<li class=\"page-item $prevDisabled\"><a class=\"page-link\" href=\"{$url}&page={$prevPage}\">«</a></li>";
    // Pages
    for ($i = 1; $i <= $totalPages; $i++) {
        $active = $i == $currentPage ? 'active' : '';
        $html  .= "<li class=\"page-item $active\"><a class=\"page-link\" href=\"{$url}&page={$i}\">$i</a></li>";
    }
    // Next
    $nextDisabled = $currentPage >= $totalPages ? 'disabled' : '';
    $nextPage     = min($totalPages, $currentPage + 1);
    $html .= "<li class=\"page-item $nextDisabled\"><a class=\"page-link\" href=\"{$url}&page={$nextPage}\">»</a></li>";
    $html .= '</ul></nav>';
    return $html;
}
