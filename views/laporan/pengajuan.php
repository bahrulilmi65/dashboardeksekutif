<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../models/PengajuanModel.php';
requireLogin();

$model = new PengajuanModel();
$db    = getDB();

// Filter
$filterStatus = $_GET['status'] ?? '';
$filterTahun  = $_GET['tahun']  ?? date('Y');

$sql    = "SELECT p.*, o.nama_organisasi FROM pengajuan_dana p JOIN organisasi o ON p.organisasi_id = o.id WHERE 1=1";
$params = [];

if ($filterStatus) { $sql .= " AND p.status = ?"; $params[] = $filterStatus; }
if ($filterTahun)  { $sql .= " AND YEAR(p.tanggal) = ?"; $params[] = $filterTahun; }
$sql .= " ORDER BY p.tanggal DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$list = $stmt->fetchAll();

$totalDana     = array_sum(array_column($list, 'dana_diajukan'));
$totalDisetujui = array_sum(array_map(fn($r) => $r['status'] === 'Disetujui' ? $r['dana_diajukan'] : 0, $list));

$pageTitle   = 'Laporan Pengajuan Dana';
$currentPage = 'lap-pengajuan';
include __DIR__ . '/../layouts/header.php';
?>
<div class="app-wrapper" id="appWrapper">
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    <div class="main-content">
        <?php include __DIR__ . '/../layouts/navbar.php'; ?>
        <div class="content-area">

            <div class="page-header">
                <div>
                    <h5 class="page-header-title"><i class="fas fa-file-chart-line me-2 text-primary"></i>Laporan Pengajuan Dana</h5>
                    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/views/dashboard/index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan Pengajuan</li>
                    </ol></nav>
                </div>
                <div class="d-flex gap-2">
                    <button onclick="printLaporan()" class="btn btn-outline-secondary btn-action">
                        <i class="fas fa-print me-2"></i>Cetak
                    </button>
                    <button onclick="exportPDF()" class="btn btn-danger btn-action">
                        <i class="fas fa-file-pdf me-2"></i>Export PDF
                    </button>
                </div>
            </div>

            <!-- Filter -->
            <div class="card card-custom mb-4 d-print-none">
                <div class="card-body py-3">
                    <form method="GET" class="row g-3 align-items-end">
                        <div class="col-md-3">
                            <label class="form-label form-label-custom">Status</label>
                            <select name="status" class="form-select form-select-sm">
                                <option value="">Semua Status</option>
                                <?php foreach (['Menunggu','Disetujui','Ditolak'] as $s): ?>
                                <option value="<?= $s ?>" <?= $filterStatus === $s ? 'selected' : '' ?>><?= $s ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label form-label-custom">Tahun</label>
                            <select name="tahun" class="form-select form-select-sm">
                                <?php for ($y = date('Y'); $y >= date('Y') - 4; $y--): ?>
                                <option value="<?= $y ?>" <?= $filterTahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <button type="submit" class="btn btn-primary btn-sm w-100">
                                <i class="fas fa-filter me-1"></i>Filter
                            </button>
                        </div>
                        <div class="col-md-2">
                            <a href="?" class="btn btn-outline-secondary btn-sm w-100">
                                <i class="fas fa-redo me-1"></i>Reset
                            </a>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Summary Cards -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="summary-card summary-blue">
                        <div class="summary-label">Total Pengajuan</div>
                        <div class="summary-value"><?= count($list) ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card summary-green">
                        <div class="summary-label">Total Dana Diajukan</div>
                        <div class="summary-value"><?= formatRupiah($totalDana) ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card summary-orange">
                        <div class="summary-label">Dana Disetujui</div>
                        <div class="summary-value"><?= formatRupiah($totalDisetujui) ?></div>
                    </div>
                </div>
            </div>

            <div class="card card-custom" id="printArea">
                <div class="laporan-header d-none d-print-block">
                    <h4>Laporan Pengajuan Dana Organisasi Kepemudaan</h4>
                    <p>Tahun: <?= $filterTahun ?> <?= $filterStatus ? "| Status: $filterStatus" : '' ?></p>
                    <p>Tanggal Cetak: <?= formatTanggal(date('Y-m-d')) ?></p>
                    <hr>
                </div>
                <div class="card-header-custom d-print-none">
                    <h6 class="card-title-custom">Data Pengajuan Dana Tahun <?= $filterTahun ?></h6>
                    <span class="badge bg-primary rounded-pill"><?= count($list) ?> Data</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblLapPengajuan" class="table table-hover table-bordered align-middle">
                            <thead class="table-head-custom">
                                <tr>
                                    <th>No</th>
                                    <th>No. Proposal</th>
                                    <th>Tanggal</th>
                                    <th>Organisasi</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Dana Diajukan</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list as $i => $row): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td><span class="mono-text"><?= htmlspecialchars($row['nomor_proposal']) ?></span></td>
                                    <td><?= formatTanggal($row['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_organisasi']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                                    <td class="text-end fw-semibold"><?= formatRupiah($row['dana_diajukan']) ?></td>
                                    <td><?= badgeStatus($row['status']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-primary fw-semibold">
                                    <td colspan="5" class="text-end">Total Dana Diajukan:</td>
                                    <td class="text-end"><?= formatRupiah($totalDana) ?></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

<?php
$rows = [];
foreach ($list as $i => $row) {
    $rows[] = [
        $i + 1,
        $row['nomor_proposal'],
        formatTanggal($row['tanggal']),
        $row['nama_organisasi'],
        $row['nama_kegiatan'],
        'Rp ' . number_format($row['dana_diajukan'], 0, ',', '.'),
        $row['status'],
    ];
}
$rowsJson      = json_encode($rows);
$totalDanaFmt  = formatRupiah($totalDana);
$filterLabel   = $filterStatus ? "Status: $filterStatus | " : '';

$extraScript = <<<SCRIPT
<script>
$(document).ready(function() { initDataTable('#tblLapPengajuan'); });

function printLaporan() { window.print(); }

function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'landscape' });

    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('LAPORAN PENGAJUAN DANA ORGANISASI KEPEMUDAAN', doc.internal.pageSize.width / 2, 16, { align: 'center' });

    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    doc.text('{$filterLabel}Tahun: <?= $filterTahun ?> | Cetak: <?= formatTanggal(date("Y-m-d")) ?>', doc.internal.pageSize.width / 2, 24, { align: 'center' });

    const statusColors = { 'Menunggu': [245,158,11], 'Disetujui': [16,185,129], 'Ditolak': [239,68,68] };

    doc.autoTable({
        startY: 30,
        head: [['No','No. Proposal','Tanggal','Organisasi','Nama Kegiatan','Dana Diajukan','Status']],
        body: {$rowsJson},
        foot: [['','','','','Total Dana', '{$totalDanaFmt}', '']],
        headStyles: { fillColor: [37, 99, 235], textColor: 255, fontStyle: 'bold', fontSize: 8 },
        footStyles: { fillColor: [219, 234, 254], textColor: [30, 64, 175], fontStyle: 'bold', fontSize: 8 },
        bodyStyles: { fontSize: 7.5 },
        alternateRowStyles: { fillColor: [248, 250, 252] },
        columnStyles: { 0: { cellWidth: 10, halign: 'center' }, 5: { halign: 'right' }, 6: { halign: 'center' } },
        margin: { left: 10, right: 10 },
        didParseCell: function(data) {
            if (data.section === 'body' && data.column.index === 6) {
                const status = data.cell.raw;
                const c = statusColors[status];
                if (c) data.cell.styles.textColor = c;
            }
        }
    });
    doc.save('laporan-pengajuan-<?= date("Y-m-d") ?>.pdf');
}
</script>
SCRIPT;
include __DIR__ . '/../layouts/footer.php';
?>
