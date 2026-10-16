<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../models/PencairanModel.php';
require_once __DIR__ . '/../../models/PengajuanModel.php';
requireAdmin();

$model     = new PencairanModel();
$pModel    = new PengajuanModel();
$list      = $model->getAll();
$approved  = $pModel->getApproved();
$autoNomor = generateNomor('CAIR', 'pencairan_dana', 'nomor_pencairan');
$totalCair = $model->totalCairan();

$pageTitle   = 'Pencairan Dana';
$currentPage = 'pencairan';
include __DIR__ . '/../layouts/header.php';
?>

<div class="app-wrapper" id="appWrapper">
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    <div class="main-content">
        <?php include __DIR__ . '/../layouts/navbar.php'; ?>
        <div class="content-area">
            <?= showFlash() ?>

            <div class="page-header">
                <div>
                    <h5 class="page-header-title"><i class="fas fa-money-bill-wave me-2 text-primary"></i>Pencairan Dana</h5>
                    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/views/dashboard/index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pencairan Dana</li>
                    </ol></nav>
                </div>
                <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus me-2"></i>Tambah Pencairan
                </button>
            </div>

            <!-- Total Banner -->
            <div class="total-banner mb-4">
                <div class="total-banner-label"><i class="fas fa-coins me-2"></i>Total Dana Telah Dicairkan</div>
                <div class="total-banner-value"><?= formatRupiah($totalCair) ?></div>
                <div class="total-banner-sub"><?= count($list) ?> transaksi pencairan</div>
            </div>

            <div class="card card-custom">
                <div class="card-header-custom">
                    <h6 class="card-title-custom">Daftar Pencairan Dana</h6>
                    <span class="badge bg-primary rounded-pill"><?= count($list) ?> Data</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblPencairan" class="table table-hover table-bordered align-middle">
                            <thead class="table-head-custom">
                                <tr>
                                    <th width="50">No</th>
                                    <th>No. Pencairan</th>
                                    <th>Tanggal</th>
                                    <th>Organisasi</th>
                                    <th>Nama Kegiatan</th>
                                    <th>No. Proposal</th>
                                    <th>Nominal</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list as $i => $row): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td><span class="mono-text"><?= htmlspecialchars($row['nomor_pencairan']) ?></span></td>
                                    <td><?= formatTanggal($row['tanggal']) ?></td>
                                    <td><?= htmlspecialchars($row['nama_organisasi']) ?></td>
                                    <td class="text-truncate" style="max-width:160px;"><?= htmlspecialchars($row['nama_kegiatan']) ?></td>
                                    <td><span class="mono-text small"><?= htmlspecialchars($row['nomor_proposal']) ?></span></td>
                                    <td class="text-end fw-semibold text-success"><?= formatRupiah($row['nominal']) ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning-soft me-1"
                                            onclick="editPencairan(<?= htmlspecialchars(json_encode($row)) ?>)"
                                            title="Edit"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger-soft"
                                            onclick="confirmDelete('<?= BASE_URL ?>/controllers/PencairanController.php?action=delete&id=<?= $row['id'] ?>', '<?= htmlspecialchars($row['nomor_pencairan'], ENT_QUOTES) ?>')"
                                            title="Hapus"><i class="fas fa-trash"></i></button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── Modal Tambah ──────────────────────────────────────── -->
<div class="modal fade" id="modalTambah" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= BASE_URL ?>/controllers/PencairanController.php?action=store" method="POST">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tambah Pencairan Dana</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">No. Pencairan <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_pencairan" class="form-control mono-input"
                                   value="<?= htmlspecialchars($autoNomor) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Pengajuan Dana (Disetujui) <span class="text-danger">*</span></label>
                            <select name="pengajuan_id" class="form-select" required onchange="fillNominal(this)">
                                <option value="">-- Pilih Nomor Proposal --</option>
                                <?php foreach ($approved as $p): ?>
                                <option value="<?= $p['id'] ?>" data-dana="<?= $p['dana_diajukan'] ?>">
                                    <?= htmlspecialchars($p['nomor_proposal']) ?> - <?= htmlspecialchars($p['nama_organisasi']) ?>
                                    (<?= formatRupiah($p['dana_diajukan']) ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Nominal Dicairkan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="nominal" id="nominalInput" class="form-control" placeholder="0" min="1" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Keterangan</label>
                            <textarea name="keterangan" class="form-control" rows="2" placeholder="Keterangan pencairan (opsional)"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-2"></i>Simpan</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── Modal Edit ────────────────────────────────────────── -->
<div class="modal fade" id="modalEdit" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="formEdit" action="" method="POST">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Pencairan Dana</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">No. Pencairan</label>
                            <input type="text" id="edit_nomor" name="nomor_pencairan" class="form-control mono-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Tanggal</label>
                            <input type="date" id="edit_tanggal" name="tanggal" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Pengajuan Dana</label>
                            <select id="edit_pengajuan" name="pengajuan_id" class="form-select" required>
                                <?php foreach ($approved as $p): ?>
                                <option value="<?= $p['id'] ?>">
                                    <?= htmlspecialchars($p['nomor_proposal']) ?> - <?= htmlspecialchars($p['nama_organisasi']) ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Nominal (Rp)</label>
                            <input type="number" id="edit_nominal" name="nominal" class="form-control" min="0">
                        </div>
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Keterangan</label>
                            <textarea id="edit_keterangan" name="keterangan" class="form-control" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-warning"><i class="fas fa-save me-2"></i>Perbarui</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php
$extraScript = <<<SCRIPT
<script>
$(document).ready(function() {
    initDataTable('#tblPencairan');
});

function fillNominal(sel) {
    const opt = sel.options[sel.selectedIndex];
    const dana = opt.getAttribute('data-dana');
    if (dana) document.getElementById('nominalInput').value = dana;
}

function editPencairan(data) {
    document.getElementById('formEdit').action =
        '<?= BASE_URL ?>/controllers/PencairanController.php?action=update&id=' + data.id;
    document.getElementById('edit_nomor').value      = data.nomor_pencairan;
    document.getElementById('edit_tanggal').value    = data.tanggal;
    document.getElementById('edit_pengajuan').value  = data.pengajuan_id;
    document.getElementById('edit_nominal').value    = data.nominal;
    document.getElementById('edit_keterangan').value = data.keterangan || '';
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>
SCRIPT;
include __DIR__ . '/../layouts/footer.php';
?>
