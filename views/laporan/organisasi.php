<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../models/OrganisasiModel.php';
requireLogin();

$model = new OrganisasiModel();
$list  = $model->getAll();

$pageTitle   = 'Laporan Organisasi';
$currentPage = 'lap-organisasi';
include __DIR__ . '/../layouts/header.php';
?>
<div class="app-wrapper" id="appWrapper">
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    <div class="main-content">
        <?php include __DIR__ . '/../layouts/navbar.php'; ?>
        <div class="content-area">

            <div class="page-header">
                <div>
                    <h5 class="page-header-title"><i class="fas fa-file-alt me-2 text-primary"></i>Laporan Organisasi</h5>
                    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/views/dashboard/index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan Organisasi</li>
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

            <div class="card card-custom" id="printArea">
                <!-- Laporan Header (untuk cetak) -->
                <div class="laporan-header d-none d-print-block">
                    <h4>Laporan Data Organisasi Kepemudaan</h4>
                    <p>Tanggal Cetak: <?= formatTanggal(date('Y-m-d')) ?></p>
                    <hr>
                </div>

                <div class="card-header-custom d-print-none">
                    <h6 class="card-title-custom">Data Organisasi Kepemudaan</h6>
                    <span class="badge bg-primary rounded-pill"><?= count($list) ?> Organisasi</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblLapOrg" class="table table-hover table-bordered align-middle">
                            <thead class="table-head-custom">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Organisasi</th>
                                    <th>Ketua</th>
                                    <th>Nomor HP</th>
                                    <th>Jumlah Anggota</th>
                                    <th>Alamat</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list as $i => $row): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($row['nama_organisasi']) ?></strong></td>
                                    <td><?= htmlspecialchars($row['ketua']) ?></td>
                                    <td><?= htmlspecialchars($row['no_hp']) ?></td>
                                    <td class="text-center"><?= number_format($row['jumlah_anggota']) ?></td>
                                    <td><?= htmlspecialchars($row['alamat']) ?></td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                            <tfoot>
                                <tr class="table-primary fw-semibold">
                                    <td colspan="4" class="text-end">Total Anggota Keseluruhan:</td>
                                    <td class="text-center"><?= number_format(array_sum(array_column($list, 'jumlah_anggota'))) ?></td>
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
        $row['nama_organisasi'],
        $row['ketua'],
        $row['no_hp'],
        number_format($row['jumlah_anggota']),
        $row['alamat'],
    ];
}
$rowsJson = json_encode($rows);
$totalAnggota = number_format(array_sum(array_column($list, 'jumlah_anggota')));

$extraScript = <<<SCRIPT
<script>
$(document).ready(function() { initDataTable('#tblLapOrg'); });

function printLaporan() {
    window.print();
}

function exportPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({ orientation: 'landscape' });

    doc.setFontSize(14);
    doc.setFont('helvetica', 'bold');
    doc.text('LAPORAN DATA ORGANISASI KEPEMUDAAN', doc.internal.pageSize.width / 2, 18, { align: 'center' });

    doc.setFontSize(10);
    doc.setFont('helvetica', 'normal');
    doc.text('Tanggal Cetak: <?= formatTanggal(date("Y-m-d")) ?>', doc.internal.pageSize.width / 2, 26, { align: 'center' });

    doc.autoTable({
        startY: 32,
        head: [['No', 'Nama Organisasi', 'Ketua', 'No. HP', 'Jml Anggota', 'Alamat']],
        body: {$rowsJson},
        foot: [['', '', '', 'Total Anggota', '{$totalAnggota}', '']],
        headStyles: { fillColor: [37, 99, 235], textColor: 255, fontStyle: 'bold', fontSize: 9 },
        footStyles: { fillColor: [219, 234, 254], textColor: [30, 64, 175], fontStyle: 'bold', fontSize: 9 },
        bodyStyles: { fontSize: 8 },
        alternateRowStyles: { fillColor: [248, 250, 252] },
        columnStyles: { 0: { cellWidth: 12, halign: 'center' }, 4: { halign: 'center' } },
        margin: { left: 10, right: 10 },
    });

    doc.save('laporan-organisasi-<?= date("Y-m-d") ?>.pdf');
}
</script>
SCRIPT;
include __DIR__ . '/../layouts/footer.php';
?>
