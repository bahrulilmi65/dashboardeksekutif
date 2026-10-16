<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../models/PencairanModel.php';

requireAdmin();

$model  = new PencairanModel();
$action = $_GET['action'] ?? 'index';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

switch ($action) {
    case 'store':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/views/pencairan/index.php');

        $data = [
            'pengajuan_id'    => (int)($_POST['pengajuan_id'] ?? 0),
            'nomor_pencairan' => trim($_POST['nomor_pencairan'] ?? ''),
            'tanggal'         => $_POST['tanggal'] ?? '',
            'nominal'         => (float)str_replace(['Rp', '.', ' '], '', $_POST['nominal'] ?? 0),
            'keterangan'      => trim($_POST['keterangan'] ?? ''),
        ];

        $errors = [];
        if ($data['pengajuan_id'] < 1)        $errors[] = 'Pengajuan wajib dipilih.';
        if (empty($data['nomor_pencairan']))   $errors[] = 'Nomor pencairan wajib diisi.';
        if (empty($data['tanggal']))           $errors[] = 'Tanggal wajib diisi.';
        if ($data['nominal'] <= 0)             $errors[] = 'Nominal harus lebih dari 0.';
        if ($model->isNomorExists($data['nomor_pencairan'])) $errors[] = 'Nomor pencairan sudah digunakan.';

        if ($errors) {
            setFlash('error', implode(' ', $errors));
        } else {
            $model->create($data);
            setFlash('success', 'Data pencairan berhasil ditambahkan.');
        }
        redirect(BASE_URL . '/views/pencairan/index.php');

    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/views/pencairan/index.php');

        $data = [
            'pengajuan_id'    => (int)($_POST['pengajuan_id'] ?? 0),
            'nomor_pencairan' => trim($_POST['nomor_pencairan'] ?? ''),
            'tanggal'         => $_POST['tanggal'] ?? '',
            'nominal'         => (float)str_replace(['Rp', '.', ' '], '', $_POST['nominal'] ?? 0),
            'keterangan'      => trim($_POST['keterangan'] ?? ''),
        ];

        $errors = [];
        if ($data['pengajuan_id'] < 1)       $errors[] = 'Pengajuan wajib dipilih.';
        if (empty($data['nomor_pencairan']))  $errors[] = 'Nomor pencairan wajib diisi.';
        if (empty($data['tanggal']))          $errors[] = 'Tanggal wajib diisi.';
        if ($model->isNomorExists($data['nomor_pencairan'], $id)) $errors[] = 'Nomor pencairan sudah digunakan.';

        if ($errors) {
            setFlash('error', implode(' ', $errors));
        } else {
            $model->update($id, $data);
            setFlash('success', 'Data pencairan berhasil diperbarui.');
        }
        redirect(BASE_URL . '/views/pencairan/index.php');

    case 'delete':
        $row = $model->getById($id);
        if ($row) {
            $model->delete($id);
            setFlash('success', 'Pencairan "' . $row['nomor_pencairan'] . '" berhasil dihapus.');
        } else {
            setFlash('error', 'Data tidak ditemukan.');
        }
        redirect(BASE_URL . '/views/pencairan/index.php');

    default:
        redirect(BASE_URL . '/views/pencairan/index.php');
}
