<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
requireLogin();

$db   = getDB();
$role = $_SESSION['role'];

// ── Statistik Cards ──────────────────────────────────────────
$totalOrg     = $db->query("SELECT COUNT(*) FROM organisasi")->fetchColumn();
$totalPengajuan = $db->query("SELECT COUNT(*) FROM pengajuan_dana")->fetchColumn();
$totalDisetujui = $db->query("SELECT COUNT(*) FROM pengajuan_dana WHERE status='Disetujui'")->fetchColumn();
$totalDiajukan  = $db->query("SELECT COALESCE(SUM(dana_diajukan),0) FROM pengajuan_dana WHERE status='Disetujui'")->fetchColumn();
$totalCair      = $db->query("SELECT COALESCE(SUM(nominal),0) FROM pencairan_dana")->fetchColumn();

// ── Chart 1: Pengajuan per Bulan (tahun ini) ─────────────────
$tahun = date('Y');
$stmtBar = $db->prepare("
    SELECT MONTH(tanggal) AS bln, COUNT(*) AS total
    FROM pengajuan_dana
    WHERE YEAR(tanggal) = ?
    GROUP BY MONTH(tanggal)
    ORDER BY bln
");
$stmtBar->execute([$tahun]);
$barRows  = $stmtBar->fetchAll();
$barData  = array_fill(0, 12, 0);
foreach ($barRows as $r) $barData[$r['bln'] - 1] = (int)$r['total'];

// ── Chart 2: Status Pengajuan (Pie) ──────────────────────────
$stmtPie = $db->query("SELECT status, COUNT(*) AS total FROM pengajuan_dana GROUP BY status");
$pieRows  = $stmtPie->fetchAll();
$pieLabels = $pieData = [];
foreach ($pieRows as $r) {
    $pieLabels[] = $r['status'];
    $pieData[]   = (int)$r['total'];
}

// ── 5 Pengajuan Terbaru ───────────────────────────────────────
$recentPengajuan = $db->query("
    SELECT p.*, o.nama_organisasi
    FROM pengajuan_dana p
    JOIN organisasi o ON p.organisasi_id = o.id
    ORDER BY p.created_at DESC LIMIT 5
")->fetchAll();

// ── 5 Pencairan Terbaru ───────────────────────────────────────
$recentCairan = $db->query("
    SELECT pc.*, o.nama_organisasi, p.nama_kegiatan
    FROM pencairan_dana pc
    JOIN pengajuan_dana p  ON pc.pengajuan_id = p.id
    JOIN organisasi o      ON p.organisasi_id = o.id
    ORDER BY pc.created_at DESC LIMIT 5
")->fetchAll();

$pageTitle   = 'Dashboard';
$currentPage = 'dashboard';
include __DIR__ . '/../layouts/header.php';
?>

<div class="app-wrapper" id="appWrapper">
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

    <div class="main-content">
        <?php include __DIR__ . '/../layouts/navbar.php'; ?>

        <div class="content-area">
            <?= showFlash() ?>

            <!-- Welcome Banner -->
            <div class="welcome-banner mb-4">
                <div class="welcome-text">
                    <h5 class="mb-1">Selamat Datang, <strong><?= htmlspecialchars($_SESSION['nama']) ?></strong>! 👋</h5>
                    <p class="mb-0 opacity-75">
                        <?php if ($role === 'admin'): ?>
                            Kelola data organisasi, pengajuan, dan pencairan dana dari sini.
                        <?php else: ?>
                            Berikut adalah ringkasan data pengelolaan dana organisasi kepemudaan.
                        <?php endif; ?>
                    </p>
                </div>
                <div class="welcome-date d-none d-md-block">
                    <i class="fas fa-calendar-check fa-2x opacity-50"></i>
                </div>
            </div>

            <!-- ── STAT CARDS ──────────────────────────────── -->
            <div class="row g-4 mb-4">
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-card-blue">
                        <div class="stat-card-body">
                            <div class="stat-card-info">
                                <div class="stat-card-label">Total Organisasi</div>
                                <div class="stat-card-value"><?= number_format($totalOrg) ?></div>
                                <div class="stat-card-sub">Organisasi terdaftar</div>
                            </div>
                            <div class="stat-card-icon"><i class="fas fa-building"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-card-indigo">
                        <div class="stat-card-body">
                            <div class="stat-card-info">
                                <div class="stat-card-label">Total Pengajuan</div>
                                <div class="stat-card-value"><?= number_format($totalPengajuan) ?></div>
                                <div class="stat-card-sub"><?= $totalDisetujui ?> disetujui</div>
                            </div>
                            <div class="stat-card-icon"><i class="fas fa-file-invoice-dollar"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-card-green">
                        <div class="stat-card-body">
                            <div class="stat-card-info">
                                <div class="stat-card-label">Dana Disetujui</div>
                                <div class="stat-card-value"><?= formatRupiah($totalDiajukan) ?></div>
                                <div class="stat-card-sub">Total nominal disetujui</div>
                            </div>
                            <div class="stat-card-icon"><i class="fas fa-check-circle"></i></div>
                        </div>
                    </div>
                </div>
                <div class="col-xl-3 col-md-6">
                    <div class="stat-card stat-card-orange">
                        <div class="stat-card-body">
                            <div class="stat-card-info">
                                <div class="stat-card-label">Dana Dicairkan</div>
                                <div class="stat-card-value"><?= formatRupiah($totalCair) ?></div>
                                <div class="stat-card-sub">Total realisasi</div>
                            </div>
                            <div class="stat-card-icon"><i class="fas fa-money-bill-wave"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── CHARTS ────────────────────────────────────── -->
            <div class="row g-4 mb-4">
                <div class="col-lg-8">
                    <div class="card card-custom h-100">
                        <div class="card-header-custom">
                            <div>
                                <h6 class="card-title-custom">Pengajuan Dana per Bulan</h6>
                                <small class="text-muted">Tahun <?= $tahun ?></small>
                            </div>
                            <span class="badge bg-primary-soft text-primary">Bar Chart</span>
                        </div>
                        <div class="card-body p-3">
                            <canvas id="barChart" style="max-height:300px;"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4">
                    <div class="card card-custom h-100">
                        <div class="card-header-custom">
                            <div>
                                <h6 class="card-title-custom">Status Pengajuan</h6>
                                <small class="text-muted">Distribusi status</small>
                            </div>
                            <span class="badge bg-info-soft text-info">Pie Chart</span>
                        </div>
                        <div class="card-body p-3 d-flex flex-column align-items-center">
                            <canvas id="pieChart" style="max-height:260px;"></canvas>
                            <div class="chart-legend mt-3 w-100" id="pieLegend"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── RECENT TABLES ──────────────────────────────── -->
            <div class="row g-4">
                <div class="col-lg-7">
                    <div class="card card-custom">
                        <div class="card-header-custom">
                            <h6 class="card-title-custom">Pengajuan Dana Terbaru</h6>
                            <?php if ($role === 'admin'): ?>
                            <a href="<?= BASE_URL ?>/views/pengajuan/index.php" class="btn btn-sm btn-outline-primary rounded-pill">
                                Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-borderless mb-0 recent-table">
                                    <thead>
                                        <tr>
                                            <th>No. Proposal</th>
                                            <th>Organisasi</th>
                                            <th>Dana</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentPengajuan as $p): ?>
                                        <tr>
                                            <td><span class="mono-text"><?= htmlspecialchars($p['nomor_proposal']) ?></span></td>
                                            <td class="text-truncate" style="max-width:140px;"><?= htmlspecialchars($p['nama_organisasi']) ?></td>
                                            <td><?= formatRupiah($p['dana_diajukan']) ?></td>
                                            <td><?= badgeStatus($p['status']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-5">
                    <div class="card card-custom">
                        <div class="card-header-custom">
                            <h6 class="card-title-custom">Pencairan Terbaru</h6>
                            <?php if ($role === 'admin'): ?>
                            <a href="<?= BASE_URL ?>/views/pencairan/index.php" class="btn btn-sm btn-outline-primary rounded-pill">
                                Lihat Semua <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                            <?php endif; ?>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table table-hover table-borderless mb-0 recent-table">
                                    <thead>
                                        <tr>
                                            <th>No. Pencairan</th>
                                            <th>Organisasi</th>
                                            <th>Nominal</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($recentCairan as $c): ?>
                                        <tr>
                                            <td><span class="mono-text"><?= htmlspecialchars($c['nomor_pencairan']) ?></span></td>
                                            <td class="text-truncate" style="max-width:130px;"><?= htmlspecialchars($c['nama_organisasi']) ?></td>
                                            <td><?= formatRupiah($c['nominal']) ?></td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div><!-- /.content-area -->
    </div><!-- /.main-content -->
</div><!-- /.app-wrapper -->

<?php
$bulanLabels = json_encode(['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agt','Sep','Okt','Nov','Des']);
$barDataJson = json_encode($barData);
$pieLabelJson = json_encode($pieLabels);
$pieDataJson  = json_encode($pieData);

$extraScript = <<<SCRIPT
<script>
// ── Bar Chart ─────────────────────────────────────────────────
const ctxBar = document.getElementById('barChart').getContext('2d');
new Chart(ctxBar, {
    type: 'bar',
    data: {
        labels: {$bulanLabels},
        datasets: [{
            label: 'Jumlah Pengajuan',
            data: {$barDataJson},
            backgroundColor: 'rgba(37,99,235,0.15)',
            borderColor: 'rgba(37,99,235,1)',
            borderWidth: 2,
            borderRadius: 6,
            borderSkipped: false,
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: { legend: { display: false } },
        scales: {
            y: {
                beginAtZero: true,
                ticks: { stepSize: 1, font: { size: 11 } },
                grid: { color: 'rgba(0,0,0,0.05)' }
            },
            x: { ticks: { font: { size: 11 } }, grid: { display: false } }
        }
    }
});

// ── Pie Chart ─────────────────────────────────────────────────
const ctxPie  = document.getElementById('pieChart').getContext('2d');
const pieColors = ['#f59e0b','#10b981','#ef4444'];
const pieLabels = {$pieLabelJson};
const pieData   = {$pieDataJson};
const total     = pieData.reduce((a, b) => a + b, 0);

new Chart(ctxPie, {
    type: 'doughnut',
    data: {
        labels: pieLabels,
        datasets: [{
            data: pieData,
            backgroundColor: pieColors,
            borderWidth: 2,
            borderColor: '#fff',
            hoverOffset: 8
        }]
    },
    options: {
        responsive: true,
        cutout: '65%',
        plugins: {
            legend: { display: false },
            tooltip: {
                callbacks: {
                    label: ctx => ' ' + ctx.label + ': ' + ctx.raw + ' (' + Math.round(ctx.raw/total*100) + '%)'
                }
            }
        }
    }
});

// Custom legend
const legendEl = document.getElementById('pieLegend');
pieLabels.forEach((lbl, i) => {
    const pct = total > 0 ? Math.round(pieData[i]/total*100) : 0;
    legendEl.innerHTML += `
        <div class="d-flex justify-content-between align-items-center mb-2">
            <div class="d-flex align-items-center gap-2">
                <span style="width:12px;height:12px;border-radius:3px;background:\${pieColors[i]};display:inline-block;"></span>
                <small>\${lbl}</small>
            </div>
            <div class="d-flex align-items-center gap-2">
                <small class="fw-semibold">\${pieData[i]}</small>
                <small class="text-muted">\${pct}%</small>
            </div>
        </div>`;
});
</script>
SCRIPT;

include __DIR__ . '/../layouts/footer.php';
?>
