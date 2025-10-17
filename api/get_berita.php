<?php
require 'db_connect.php';

// Ambil parameter 'limit' dari URL, jika ada, untuk halaman utama
$limit = isset($_GET['limit']) ? intval($_GET['limit']) : 0;

// Ambil hanya berita yang statusnya 'aktif'
$sql = "SELECT id, judul, ringkasan, url_gambar FROM berita WHERE status = 'aktif' ORDER BY id DESC";

if ($limit > 0) {
    $sql .= " LIMIT " . $limit;
}

$result = $koneksi->query($sql);
$berita = $result->fetch_all(MYSQLI_ASSOC);

echo json_encode($berita);

$koneksi->close();
?>