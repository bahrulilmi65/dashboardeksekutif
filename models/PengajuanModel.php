<?php
// ============================================================
// Model: Pengajuan Dana
// ============================================================

class PengajuanModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getAll() {
        return $this->db->query("
            SELECT p.*, o.nama_organisasi
            FROM pengajuan_dana p
            JOIN organisasi o ON p.organisasi_id = o.id
            ORDER BY p.tanggal DESC, p.id DESC
        ")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT p.*, o.nama_organisasi
            FROM pengajuan_dana p
            JOIN organisasi o ON p.organisasi_id = o.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO pengajuan_dana (organisasi_id, nomor_proposal, tanggal, nama_kegiatan, dana_diajukan, status, catatan)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['organisasi_id'],
            $data['nomor_proposal'],
            $data['tanggal'],
            $data['nama_kegiatan'],
            $data['dana_diajukan'],
            $data['status'],
            $data['catatan'],
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE pengajuan_dana
            SET organisasi_id=?, nomor_proposal=?, tanggal=?, nama_kegiatan=?,
                dana_diajukan=?, status=?, catatan=?
            WHERE id=?
        ");
        return $stmt->execute([
            $data['organisasi_id'],
            $data['nomor_proposal'],
            $data['tanggal'],
            $data['nama_kegiatan'],
            $data['dana_diajukan'],
            $data['status'],
            $data['catatan'],
            $id,
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM pengajuan_dana WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function getApproved() {
        return $this->db->query("
            SELECT p.*, o.nama_organisasi
            FROM pengajuan_dana p
            JOIN organisasi o ON p.organisasi_id = o.id
            WHERE p.status = 'Disetujui'
            ORDER BY p.tanggal DESC
        ")->fetchAll();
    }

    public function isNomorExists($nomor, $excludeId = 0) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM pengajuan_dana WHERE nomor_proposal=? AND id != ?");
        $stmt->execute([$nomor, $excludeId]);
        return $stmt->fetchColumn() > 0;
    }
}
