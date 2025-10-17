<?php
require 'db_connect.php';

// Ambil kode jurusan dari URL (e.g., ?kode=rpl)
$kode_jurusan = isset($_GET['kode']) ? $_GET['kode'] : null;

if ($kode_jurusan) {
    // Ambil detail satu jurusan berdasarkan kodenya
    $stmt = $koneksi->prepare("SELECT id, nama, deskripsi FROM jurusan WHERE kode_jurusan = ?");
    $stmt->bind_param("s", $kode_jurusan);
    $stmt->execute();
    $jurusan = $stmt->get_result()->fetch_assoc();

    if ($jurusan) {
        $jurusan_id = $jurusan['id'];
        
        // Ambil data guru
        $stmt_guru = $koneksi->prepare("SELECT nama, url_foto AS foto FROM guru WHERE jurusan_id = ?");
        $stmt_guru->bind_param("i", $jurusan_id);
        $stmt_guru->execute();
        $jurusan['guru'] = $stmt_guru->get_result()->fetch_all(MYSQLI_ASSOC);

        // Ambil data keahlian
        $stmt_keahlian = $koneksi->prepare("SELECT nama FROM keahlian WHERE jurusan_id = ?");
        $stmt_keahlian->bind_param("i", $jurusan_id);
        $stmt_keahlian->execute();
        $jurusan['keahlian'] = array_column($stmt_keahlian->get_result()->fetch_all(MYSQLI_ASSOC), 'nama');
        
        // Ambil data galeri jurusan
        $stmt_galeri = $koneksi->prepare("SELECT url_gambar FROM galeri_jurusan WHERE jurusan_id = ?");
        $stmt_galeri->bind_param("i", $jurusan_id);
        $stmt_galeri->execute();
        $jurusan['gallery'] = array_column($stmt_galeri->get_result()->fetch_all(MYSQLI_ASSOC), 'url_gambar');
    }
    echo json_encode($jurusan);

} else {
    // Ambil daftar semua jurusan (kecuali 'umum') untuk sidebar
    $result = $koneksi->query("SELECT id, kode_jurusan, nama FROM jurusan WHERE kode_jurusan != 'umum'");
    echo json_encode($result->fetch_all(MYSQLI_ASSOC));
}

$koneksi->close();
?>