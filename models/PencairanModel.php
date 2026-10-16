<?php
// ============================================================
// Model: Pencairan Dana
// ============================================================

class PencairanModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getAll() {
        return $this->db->query("
            SELECT pc.*, o.nama_organisasi, p.nomor_proposal, p.nama_kegiatan, p.dana_diajukan
            FROM pencairan_dana pc
            JOIN pengajuan_dana p ON pc.pengajuan_id = p.id
            JOIN organisasi o     ON p.organisasi_id = o.id
            ORDER BY pc.tanggal DESC, pc.id DESC
        ")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("
            SELECT pc.*, o.nama_organisasi, p.nomor_proposal, p.nama_kegiatan, p.dana_diajukan
            FROM pencairan_dana pc
            JOIN pengajuan_dana p ON pc.pengajuan_id = p.id
            JOIN organisasi o     ON p.organisasi_id = o.id
            WHERE pc.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO pencairan_dana (pengajuan_id, nomor_pencairan, tanggal, nominal, keterangan)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['pengajuan_id'],
            $data['nomor_pencairan'],
            $data['tanggal'],
            $data['nominal'],
            $data['keterangan'],
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE pencairan_dana
            SET pengajuan_id=?, nomor_pencairan=?, tanggal=?, nominal=?, keterangan=?
            WHERE id=?
        ");
        return $stmt->execute([
            $data['pengajuan_id'],
            $data['nomor_pencairan'],
            $data['tanggal'],
            $data['nominal'],
            $data['keterangan'],
            $id,
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM pencairan_dana WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function isNomorExists($nomor, $excludeId = 0) {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM pencairan_dana WHERE nomor_pencairan=? AND id != ?");
        $stmt->execute([$nomor, $excludeId]);
        return $stmt->fetchColumn() > 0;
    }

    public function totalCairan() {
        return $this->db->query("SELECT COALESCE(SUM(nominal),0) FROM pencairan_dana")->fetchColumn();
    }
}
