<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/session.php';
require_once __DIR__ . '/../../config/functions.php';
require_once __DIR__ . '/../../models/OrganisasiModel.php';
requireAdmin();

$model = new OrganisasiModel();
$list  = $model->getAll();

$pageTitle   = 'Data Organisasi';
$currentPage = 'organisasi';
include __DIR__ . '/../layouts/header.php';
?>

<div class="app-wrapper" id="appWrapper">
    <?php include __DIR__ . '/../layouts/sidebar.php'; ?>
    <div class="main-content">
        <?php include __DIR__ . '/../layouts/navbar.php'; ?>
        <div class="content-area">
            <?= showFlash() ?>

            <!-- Page Header -->
            <div class="page-header">
                <div>
                    <h5 class="page-header-title"><i class="fas fa-building me-2 text-primary"></i>Data Organisasi</h5>
                    <nav aria-label="breadcrumb"><ol class="breadcrumb mb-0 small">
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>/views/dashboard/index.php">Dashboard</a></li>
                        <li class="breadcrumb-item active">Organisasi</li>
                    </ol></nav>
                </div>
                <button class="btn btn-primary btn-action" data-bs-toggle="modal" data-bs-target="#modalTambah">
                    <i class="fas fa-plus me-2"></i>Tambah Organisasi
                </button>
            </div>

            <!-- Table Card -->
            <div class="card card-custom">
                <div class="card-header-custom">
                    <h6 class="card-title-custom">Daftar Organisasi</h6>
                    <span class="badge bg-primary rounded-pill"><?= count($list) ?> Data</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="tblOrganisasi" class="table table-hover table-bordered align-middle">
                            <thead class="table-head-custom">
                                <tr>
                                    <th width="50">No</th>
                                    <th>Nama Organisasi</th>
                                    <th>Ketua</th>
                                    <th>No. HP</th>
                                    <th>Jumlah Anggota</th>
                                    <th>Alamat</th>
                                    <th width="120">Aksi</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($list as $i => $org): ?>
                                <tr>
                                    <td class="text-center"><?= $i + 1 ?></td>
                                    <td><strong><?= htmlspecialchars($org['nama_organisasi']) ?></strong></td>
                                    <td><?= htmlspecialchars($org['ketua']) ?></td>
                                    <td><?= htmlspecialchars($org['no_hp']) ?></td>
                                    <td class="text-center"><span class="badge bg-info-soft text-info"><?= number_format($org['jumlah_anggota']) ?> orang</span></td>
                                    <td class="text-truncate" style="max-width:180px;"><?= htmlspecialchars($org['alamat']) ?></td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-warning-soft me-1"
                                            onclick="editOrganisasi(<?= htmlspecialchars(json_encode($org)) ?>)"
                                            title="Edit"><i class="fas fa-edit"></i></button>
                                        <button class="btn btn-sm btn-danger-soft"
                                            onclick="confirmDelete('<?= BASE_URL ?>/controllers/OrganisasiController.php?action=delete&id=<?= $org['id'] ?>', '<?= htmlspecialchars($org['nama_organisasi'], ENT_QUOTES) ?>')"
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
            <form action="<?= BASE_URL ?>/controllers/OrganisasiController.php?action=store" method="POST">
                <div class="modal-header modal-header-custom">
                    <h5 class="modal-title"><i class="fas fa-plus-circle me-2"></i>Tambah Organisasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Nama Organisasi <span class="text-danger">*</span></label>
                            <input type="text" name="nama_organisasi" class="form-control" placeholder="Masukkan nama organisasi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Nama Ketua <span class="text-danger">*</span></label>
                            <input type="text" name="ketua" class="form-control" placeholder="Nama ketua organisasi" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Nomor HP <span class="text-danger">*</span></label>
                            <input type="text" name="no_hp" class="form-control" placeholder="08xxxxxxxxxx" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Jumlah Anggota <span class="text-danger">*</span></label>
                            <input type="number" name="jumlah_anggota" class="form-control" placeholder="0" min="1" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Alamat <span class="text-danger">*</span></label>
                            <textarea name="alamat" class="form-control" rows="3" placeholder="Alamat lengkap organisasi" required></textarea>
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
                    <h5 class="modal-title"><i class="fas fa-edit me-2"></i>Edit Organisasi</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Nama Organisasi <span class="text-danger">*</span></label>
                            <input type="text" id="edit_nama_organisasi" name="nama_organisasi" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Nama Ketua <span class="text-danger">*</span></label>
                            <input type="text" id="edit_ketua" name="ketua" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Nomor HP <span class="text-danger">*</span></label>
                            <input type="text" id="edit_no_hp" name="no_hp" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label form-label-custom">Jumlah Anggota <span class="text-danger">*</span></label>
                            <input type="number" id="edit_jumlah_anggota" name="jumlah_anggota" class="form-control" min="1" required>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label form-label-custom">Alamat <span class="text-danger">*</span></label>
                            <textarea id="edit_alamat" name="alamat" class="form-control" rows="3" required></textarea>
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
    initDataTable('#tblOrganisasi');
});

function editOrganisasi(data) {
    document.getElementById('formEdit').action =
        '<?= BASE_URL ?>/controllers/OrganisasiController.php?action=update&id=' + data.id;
    document.getElementById('edit_nama_organisasi').value = data.nama_organisasi;
    document.getElementById('edit_ketua').value           = data.ketua;
    document.getElementById('edit_no_hp').value           = data.no_hp;
    document.getElementById('edit_jumlah_anggota').value  = data.jumlah_anggota;
    document.getElementById('edit_alamat').value          = data.alamat;
    new bootstrap.Modal(document.getElementById('modalEdit')).show();
}
</script>
SCRIPT;
include __DIR__ . '/../layouts/footer.php';
?>
