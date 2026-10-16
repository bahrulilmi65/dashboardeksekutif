<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../models/PencairanModel.php';
requireLogin();

$db          = getDB();
$filterTahun = $_GET['tahun'] ?? date('Y');
$filterBulan = $_GET['bulan'] ?? '';

$sql    = "SELECT pc.*, o.nama_organisasi, p.nomor_proposal, p.nama_kegiatan
           FROM pencairan_dana pc
           JOIN pengajuan_dana p ON pc.pengajuan_id = p.id
           JOIN organisasi o     ON p.organisasi_id = o.id
           WHERE YEAR(pc.tanggal) = ?";
$params = [$filterTahun];

if ($filterBulan) { $sql .= " AND MONTH(pc.tanggal) = ?"; $params[] = $filterBulan; }
$sql .= " ORDER BY pc.tanggal DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$list = $stmt->fetchAll();

$totalNominal = array_sum(array_column($list, 'nominal'));

$namaBulan = ['','Januari','Februari','Maret','April','Mei','Juni',
              'Juli','Agustus','September','Oktober','November','Desember'];

$pageTitle   = 'Laporan Pencairan Dana';
$currentPage = 'lap-pencairan';
include __DIR__ . '/../layouts/header.php';
?>
<div class="app-wrapper" id="appWrapper">
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    <div class="main-content">
        <?php include __DIR__ . '/../layouts/navbar.php'; ?>
        <div class="content-area">

            <div class="page-header">
                <div>
                    <h5 class="page-header-title"><i class="fas fa-file-invoice me-2 text-primary"></i>Laporan Pencairan Dana</h5>
                    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/views/dashboard/index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan Pencairan</li>
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
                            <label class="form-label form-label-custom">Tahun</label>
                            <select name="tahun" class="form-select form-select-sm">
                                <?php for ($y = date('Y'); $y >= date('Y') - 4; $y--): ?>
                                <option value="<?= $y ?>" <?= $filterTahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                                <?php endfor; ?>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label form-label-custom">Bulan</label>
                            <select name="bulan" class="form-select form-select-sm">
                                <option value="">Semua Bulan</option>
                                <?php for ($m = 1; $m <= 12; $m++): ?>
                                <option value="<?= $m ?>" <?= $filterBulan == $m ? 'selected' : '' ?>><?= $namaBulan[$m] ?></option>
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

            <!-- Summary -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <div class="summary-card summary-blue">
                        <div class="summary-label">Total Transaksi</div>
                        <div class="summary-value"><?= count($list) ?> Pencairan</div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card summary-green">
                        <div class="summary-label">Total Dana Dicairkan</div>
                        <div class="summary-value"><?= formatRupiah($totalNominal) ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="summary-card summary-orange">
                        <div class="summary-label">Periode</div>
                        <div class="summary-value">
                            <?= $filterBulan ? $namaBulan[$filterBulan] . ' ' : '' ?><?= $filterTahun ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card card-custom" id="printArea">
                <div class="laporan-header d-none d-print-block">
                    <h4>Laporan Pencairan Dana Organisasi Kepemudaan</h4>
                    <p>Periode: <?= $filterBulan ? $namaBulan[$filterBulan] . ' ' : '' ?><?= $filterTahun ?></p>
                    <p>Tanggal Cetak: <?= formatTanggal(date('Y-m-d')) ?></p>
                    <hr>
                </div>
                <div class="card-header-custom d-print-none">
                    <h6 class="card-title-custom">Data Pencairan Dana</h6>
                    <span class="badge bg-primary rounded-pill"><?= count($list) ?> Data</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblLapCairan" class="table table-hover table-bordered align-middle">
                            <thead class="table-head-custom">
                                <tr>
                                    <th>No</th>
                                    <th>No. Pencairan</th>
                                    <th>Tanggal</th>
                                    <th>Organisasi</th>
                                    <th>Kegiatan</th>
                                    <th>No. Proposal</th>
                                    <th>Nominal</th>
                                    <th>Keterangan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list as $i => $row): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td><span class="mono-text"><?= htmlspecialchars($row['nomor_pencairan']) ?></span></td>
                                    <td><?= formatTanggal($row['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_organisasi']) ?></td>
                                    <td class="text-truncate" style="max-width:150px;"><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                                    <td><span class="mono-text small"><?= htmlspecialchars($row['nomor_proposal']) ?></span></td>
                                    <td class="text-end fw-semibold text-success"><?= formatRupiah($row['nominal']) ?></td>
                                    <td class="text-muted small"><?= htmlspecialchars($row['keterangan'] ?? '-') ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-success fw-semibold">
                                    <td colspan="6" class="text-end">Total Dana Dicairkan:</td>
                                    <td class="text-end text-success"><?= formatRupiah($totalNominal) ?></td>
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
        $row['nomor_pencairan'],
        formatTanggal($row['tanggal']),
        $row['nama_organisasi'],
        $row['nama_kegiatan'],
        $row['nomor_proposal'],
        'Rp ' . number_format($row['nominal'], 0, ',', '.'),
        $row['keterangan'] ?? '-',
    ];
}
$rowsJson     = json_encode($rows);
$totalFmt     = formatRupiah($totalNominal);
$periodeLabel = ($filterBulan ? $namaBulan[$filterBulan] . ' ' : '') . $filterTahun;

$extraScript = <<<SCRIPT
<script>
$(document).ready(function() { initDataTable('#tblLapCairan'); });

function printLaporan() { window.print(); }

function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'landscape' });

    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('LAPORAN PENCAIRAN DANA ORGANISASI KEPEMUDAAN', doc.internal.pageSize.width / 2, 16, { align: 'center' });

    doc.setFontSize(9);
    doc.setFont('helvetica', 'normal');
    doc.text('Periode: {$periodeLabel} | Cetak: <?= formatTanggal(date("Y-m-d")) ?>', doc.internal.pageSize.width / 2, 24, { align: 'center' });

    doc.autoTable({
        startY: 30,
        head: [['No','No. Pencairan','Tanggal','Organisasi','Kegiatan','No. Proposal','Nominal','Keterangan']],
        body: {$rowsJson},
        foot: [['','','','','','Total Dicairkan', '{$totalFmt}','']],
        headStyles: { fillColor: [37, 99, 235], textColor: 255, fontStyle: 'bold', fontSize: 8 },
        footStyles: { fillColor: [209, 250, 229], textColor: [5, 150, 105], fontStyle: 'bold', fontSize: 8 },
        bodyStyles: { fontSize: 7.5 },
        alternateRowStyles: { fillColor: [248, 250, 252] },
        columnStyles: {
            0: { cellWidth: 10, halign: 'center' },
            6: { halign: 'right', textColor: [5, 150, 105] }
        },
        margin: { left: 10, right: 10 },
    });
    doc.save('laporan-pencairan-<?= date("Y-m-d") ?>.pdf');
}
</script>
SCRIPT;
include __DIR__ . '/../layouts/footer.php';
?>
