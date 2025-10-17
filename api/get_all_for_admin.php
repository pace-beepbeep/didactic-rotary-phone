<?php
require 'db_connect.php';

$response = [
    'portfolio' => [],
    'jurusan' => [],
    'guru' => [],
    'berita' => []
];

// Ambil data jurusan (kecuali 'umum')
$response['jurusan'] = $koneksi->query("SELECT id, nama FROM jurusan WHERE kode_jurusan != 'umum'")->fetch_all(MYSQLI_ASSOC);

// Ambil data portfolio dengan nama jurusannya
$sql_portfolio = "SELECT p.*, j.nama AS nama_jurusan FROM portofolio p JOIN jurusan j ON p.jurusan_id = j.id ORDER BY p.id DESC";
$response['portfolio'] = $koneksi->query($sql_portfolio)->fetch_all(MYSQLI_ASSOC);

// Ambil data guru dengan nama jurusannya
$sql_guru = "SELECT g.*, j.nama AS nama_jurusan FROM guru g JOIN jurusan j ON g.jurusan_id = j.id ORDER BY g.id DESC";
$response['guru'] = $koneksi->query($sql_guru)->fetch_all(MYSQLI_ASSOC);

// Ambil data berita
$response['berita'] = $koneksi->query("SELECT * FROM berita ORDER BY id DESC")->fetch_all(MYSQLI_ASSOC);

echo json_encode($response);

$koneksi->close();
?>