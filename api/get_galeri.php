<?php
require 'db_connect.php';

$sql = "SELECT id, kategori, url_gambar, judul, deskripsi FROM galeri";
$result = $koneksi->query($sql);
$galeri_flat = $result->fetch_all(MYSQLI_ASSOC);

// Kelompokkan galeri berdasarkan kategori
$galeri_grouped = [];
foreach ($galeri_flat as $item) {
    $kategori = $item['kategori'];
    if (!isset($galeri_grouped[$kategori])) {
        $galeri_grouped[$kategori] = [];
    }
    // Sesuaikan nama field agar cocok dengan JavaScript (image, title, description)
    $galeri_grouped[$kategori][] = [
        'image' => $item['url_gambar'],
        'title' => $item['judul'],
        'description' => $item['deskripsi']
    ];
}

echo json_encode($galeri_grouped);
$koneksi->close();
?>