<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../models/PengajuanModel.php';

requireAdmin();

$model  = new PengajuanModel();
$action = $_GET['action'] ?? 'index';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

switch ($action) {
    case 'store':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/views/pengajuan/index.php');

        $data = [
            'organisasi_id'  => (int)($_POST['organisasi_id'] ?? 0),
            'nomor_proposal' => trim($_POST['nomor_proposal'] ?? ''),
            'tanggal'        => $_POST['tanggal'] ?? '',
            'nama_kegiatan'  => trim($_POST['nama_kegiatan'] ?? ''),
            'dana_diajukan'  => (float)str_replace(['Rp', '.', ' '], '', $_POST['dana_diajukan'] ?? 0),
            'status'         => $_POST['status'] ?? 'Menunggu',
            'catatan'        => trim($_POST['catatan'] ?? ''),
        ];

        $errors = [];
        if ($data['organisasi_id'] < 1)       $errors[] = 'Organisasi wajib dipilih.';
        if (empty($data['nomor_proposal']))    $errors[] = 'Nomor proposal wajib diisi.';
        if (empty($data['tanggal']))           $errors[] = 'Tanggal wajib diisi.';
        if (empty($data['nama_kegiatan']))     $errors[] = 'Nama kegiatan wajib diisi.';
        if ($data['dana_diajukan'] <= 0)       $errors[] = 'Dana diajukan harus lebih dari 0.';
        if ($model->isNomorExists($data['nomor_proposal'])) $errors[] = 'Nomor proposal sudah digunakan.';

        if ($errors) {
            setFlash('error', implode(' ', $errors));
        } else {
            $model->create($data);
            setFlash('success', 'Pengajuan dana berhasil ditambahkan.');
        }
        redirect(BASE_URL . '/views/pengajuan/index.php');

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/views/pengajuan/index.php');

        $data = [
            'organisasi_id'  => (int)($_POST['organisasi_id'] ?? 0),
            'nomor_proposal' => trim($_POST['nomor_proposal'] ?? ''),
            'tanggal'        => $_POST['tanggal'] ?? '',
            'nama_kegiatan'  => trim($_POST['nama_kegiatan'] ?? ''),
            'dana_diajukan'  => (float)str_replace(['Rp', '.', ' '], '', $_POST['dana_diajukan'] ?? 0),
            'status'         => $_POST['status'] ?? 'Menunggu',
            'catatan'        => trim($_POST['catatan'] ?? ''),
        ];

        $errors = [];
        if ($data['organisasi_id'] < 1)    $errors[] = 'Organisasi wajib dipilih.';
        if (empty($data['nomor_proposal'])) $errors[] = 'Nomor proposal wajib diisi.';
        if (empty($data['tanggal']))        $errors[] = 'Tanggal wajib diisi.';
        if (empty($data['nama_kegiatan']))  $errors[] = 'Nama kegiatan wajib diisi.';
        if ($model->isNomorExists($data['nomor_proposal'], $id)) $errors[] = 'Nomor proposal sudah digunakan.';

        if ($errors) {
            setFlash('error', implode(' ', $errors));
        } else {
            $model->update($id, $data);
            setFlash('success', 'Pengajuan dana berhasil diperbarui.');
        }
        redirect(BASE_URL . '/views/pengajuan/index.php');

    case 'delete':
        $row = $model->getById($id);
        if ($row) {
            $model->delete($id);
            setFlash('success', 'Pengajuan "' . $row['nomor_proposal'] . '" berhasil dihapus.');
        } else {
            setFlash('error', 'Data tidak ditemukan.');
        }
        redirect(BASE_URL . '/views/pengajuan/index.php');

    default:
        redirect(BASE_URL . '/views/pengajuan/index.php');
}
