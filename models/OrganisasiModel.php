<?php
// ============================================================
// Model: Organisasi
// ============================================================

class OrganisasiModel {
    private $db;

    public function __construct() {
        $this->db = getDB();
    }

    public function getAll() {
        return $this->db->query("SELECT * FROM organisasi ORDER BY nama_organisasi ASC")->fetchAll();
    }

    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM organisasi WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }

    public function create($data) {
        $stmt = $this->db->prepare("
            INSERT INTO organisasi (nama_organisasi, ketua, alamat, no_hp, jumlah_anggota)
            VALUES (?, ?, ?, ?, ?)
        ");
        return $stmt->execute([
            $data['nama_organisasi'],
            $data['ketua'],
            $data['alamat'],
            $data['no_hp'],
            $data['jumlah_anggota'],
        ]);
    }

    public function update($id, $data) {
        $stmt = $this->db->prepare("
            UPDATE organisasi
            SET nama_organisasi=?, ketua=?, alamat=?, no_hp=?, jumlah_anggota=?
            WHERE id=?
        ");
        return $stmt->execute([
            $data['nama_organisasi'],
            $data['ketua'],
            $data['alamat'],
            $data['no_hp'],
            $data['jumlah_anggota'],
            $id,
        ]);
    }

    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM organisasi WHERE id=?");
        return $stmt->execute([$id]);
    }

    public function count() {
        return $this->db->query("SELECT COUNT(*) FROM organisasi")->fetchColumn();
    }
}
