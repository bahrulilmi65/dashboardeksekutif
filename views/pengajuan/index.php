<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../models/PengajuanModel.php';
require_once __DIR__ . '/../../models/OrganisasiModel.php';
requireAdmin();

$model     = new PengajuanModel();
$orgModel  = new OrganisasiModel();
$list      = $model->getAll();
$orgs      = $orgModel->getAll();
$autoNomor = generateNomor('PROP', 'pengajuan_dana', 'nomor_proposal');

$pageTitle   = 'Pengajuan Dana';
$currentPage = 'pengajuan';
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
                    <h5 class="page-header-title"><i class="fas fa-file-invoice-dollar me-2 text-primary"></i>Pengajuan Dana</h5>
                    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/views/dashboard/index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pengajuan Dana</li>
                    </ol></nav>
                </div>
                <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus me-2"></i>Tambah Pengajuan
                </button>
            </div>

            <!-- Summary Badges -->
            <div class="d-flex flex-wrap gap-2 mb-4">
                <?php
                $counts = ['Menunggu'=>0,'Disetujui'=>0,'Ditolak'=>0];
                foreach ($list as $r) $counts[$r['status']]++;
                $colors = ['Menunggu'=>'warning','Disetujui'=>'success','Ditolak'=>'danger'];
                foreach ($counts as $s => $c):
                ?>
                <span class="badge bg-<?= $colors[$s] ?> px-3 py-2 fs-6">
                    <?= badgeStatus($s) ?> <strong class="ms-1"><?= $c ?></strong>
                </span>
                <?php endforeach; ?>
            </div>

            <div class="card card-custom">
                <div class="card-header-custom">
                    <h6 class="card-title-custom">Daftar Pengajuan Dana</h6>
                    <span class="badge bg-primary rounded-pill"><?= count($list) ?> Data</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblPengajuan" class="table table-hover table-bordered align-middle">
                            <thead class="table-head-custom">
                                <tr>
                                    <th width="50">No</th>
                                    <th>No. Proposal</th>
                                    <th>Tanggal</th>
                                    <th>Organisasi</th>
                                    <th>Nama Kegiatan</th>
                                    <th>Dana Diajukan</th>
                                    <th>Status</th>
                                    <th width="120">Aksi</th>
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
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning-soft me-1"
                                            onclick="editPengajuan(<?= htmlspecialchars(json_encode($row)) ?>)"
                                            title="Edit"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger-soft"
                                            onclick="confirmDelete('<?= BASE_URL ?>/controllers/PengajuanController.php?action=delete&id=<?= $row['id'] ?>', '<?= htmlspecialchars($row['nomor_proposal'], ENT_QUOTES) ?>')"
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
            <form action="<?= BASE_URL ?>/controllers/PengajuanController.php?action=store" method="POST">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tambah Pengajuan Dana</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">No. Proposal <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_proposal" class="form-control mono-input"
                                   value="<?= htmlspecialchars($autoNomor) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Tanggal Pengajuan <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal" class="form-control" value="<?= date('Y-m-d') ?>" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Organisasi <span class="text-danger">*</span></label>
                            <select name="organisasi_id" class="form-select" required>
                                <option value="">-- Pilih Organisasi --</option>
                                <?php foreach ($orgs as $org): ?>
                                <option value="<?= $org['id'] ?>"><?= htmlspecialchars($org['nama_organisasi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" name="nama_kegiatan" class="form-control" placeholder="Nama kegiatan yang diajukan" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Dana Diajukan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="dana_diajukan" class="form-control" placeholder="0" min="1" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Status</label>
                            <select name="status" class="form-select">
                                <option value="Menunggu">Menunggu</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Catatan</label>
                            <textarea name="catatan" class="form-control" rows="2" placeholder="Catatan tambahan (opsional)"></textarea>
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
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Pengajuan Dana</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">No. Proposal <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nomor" name="nomor_proposal" class="form-control mono-input" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Tanggal <span class="text-danger">*</span></label>
                            <input type="date" id="edit_tanggal" name="tanggal" class="form-control" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Organisasi <span class="text-danger">*</span></label>
                            <select id="edit_org" name="organisasi_id" class="form-select" required>
                                <option value="">-- Pilih Organisasi --</option>
                                <?php foreach ($orgs as $org): ?>
                                <option value="<?= $org['id'] ?>"><?= htmlspecialchars($org['nama_organisasi']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Nama Kegiatan <span class="text-danger">*</span></label>
                            <input type="text" id="edit_kegiatan" name="nama_kegiatan" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Dana Diajukan (Rp)</label>
                            <input type="number" id="edit_dana" name="dana_diajukan" class="form-control" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Status</label>
                            <select id="edit_status" name="status" class="form-select">
                                <option value="Menunggu">Menunggu</option>
                                <option value="Disetujui">Disetujui</option>
                                <option value="Ditolak">Ditolak</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Catatan</label>
                            <textarea id="edit_catatan" name="catatan" class="form-control" rows="2"></textarea>
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
    initDataTable('#tblPengajuan');
});

function editPengajuan(data) {
    document.getElementById('formEdit').action =
        '<?= BASE_URL ?>/controllers/PengajuanController.php?action=update&id=' + data.id;
    document.getElementById('edit_nomor').value    = data.nomor_proposal;
    document.getElementById('edit_tanggal').value  = data.tanggal;
    document.getElementById('edit_org').value      = data.organisasi_id;
    document.getElementById('edit_kegiatan').value = data.nama_kegiatan;
    document.getElementById('edit_dana').value     = data.dana_diajukan;
    document.getElementById('edit_status').value   = data.status;
    document.getElementById('edit_catatan').value  = data.catatan || '';
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>
SCRIPT;
include __DIR__ . '/../layouts/footer.php';
?>
