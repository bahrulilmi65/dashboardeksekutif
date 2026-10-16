<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';
require_once __DIR__ . '/../config/functions.php';
require_once __DIR__ . '/../models/OrganisasiModel.php';

requireAdmin();

$model  = new OrganisasiModel();
$action = $_GET['action'] ?? 'index';
$id     = isset($_GET['id']) ? (int)$_GET['id'] : 0;

switch ($action) {
    // ── CREATE ────────────────────────────────────────────────
    case 'store':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/views/organisasi/index.php');

        $data = [
            'nama_organisasi' => trim($_POST['nama_organisasi'] ?? ''),
            'ketua'           => trim($_POST['ketua'] ?? ''),
            'alamat'          => trim($_POST['alamat'] ?? ''),
            'no_hp'           => trim($_POST['no_hp'] ?? ''),
            'jumlah_anggota'  => (int)($_POST['jumlah_anggota'] ?? 0),
        ];

        $errors = [];
        if (empty($data['nama_organisasi'])) $errors[] = 'Nama organisasi wajib diisi.';
        if (empty($data['ketua']))           $errors[] = 'Nama ketua wajib diisi.';
        if (empty($data['alamat']))          $errors[] = 'Alamat wajib diisi.';
        if (empty($data['no_hp']))           $errors[] = 'Nomor HP wajib diisi.';
        if ($data['jumlah_anggota'] < 1)     $errors[] = 'Jumlah anggota minimal 1.';

        if ($errors) {
            setFlash('error', implode(' ', $errors));
        } else {
            $model->create($data);
            setFlash('success', 'Organisasi berhasil ditambahkan.');
        }
        redirect(BASE_URL . '/views/organisasi/index.php');

    // ── UPDATE ────────────────────────────────────────────────
    case 'update':
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') redirect(BASE_URL . '/views/organisasi/index.php');

        $data = [
            'nama_organisasi' => trim($_POST['nama_organisasi'] ?? ''),
            'ketua'           => trim($_POST['ketua'] ?? ''),
            'alamat'          => trim($_POST['alamat'] ?? ''),
            'no_hp'           => trim($_POST['no_hp'] ?? ''),
            'jumlah_anggota'  => (int)($_POST['jumlah_anggota'] ?? 0),
        ];

        $errors = [];
        if (empty($data['nama_organisasi'])) $errors[] = 'Nama organisasi wajib diisi.';
        if (empty($data['ketua']))           $errors[] = 'Nama ketua wajib diisi.';
        if (empty($data['alamat']))          $errors[] = 'Alamat wajib diisi.';
        if (empty($data['no_hp']))           $errors[] = 'Nomor HP wajib diisi.';

        if ($errors) {
            setFlash('error', implode(' ', $errors));
        } else {
            $model->update($id, $data);
            setFlash('success', 'Organisasi berhasil diperbarui.');
        }
        redirect(BASE_URL . '/views/organisasi/index.php');

    // ── DELETE ────────────────────────────────────────────────
    case 'delete':
        $org = $model->getById($id);
        if ($org) {
            $model->delete($id);
            setFlash('success', 'Organisasi "' . $org['nama_organisasi'] . '" berhasil dihapus.');
        } else {
            setFlash('error', 'Data organisasi tidak ditemukan.');
        }
        redirect(BASE_URL . '/views/organisasi/index.php');

    default:
        redirect(BASE_URL . '/views/organisasi/index.php');
}
