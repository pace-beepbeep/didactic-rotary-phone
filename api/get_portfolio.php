<?php
require 'db_connect.php';

$sql = "
    SELECT 
        p.id, p.nama, p.nama_project, p.deskripsi_singkat, 
        p.bekerja, p.url_foto_project AS fotoProject, 
        p.deskripsi_lengkap AS deskripsiLengkap, p.link_project AS linkProject,
        j.nama AS nama_jurusan
    FROM portofolio p
    JOIN jurusan j ON p.jurusan_id = j.id
";

$result = $koneksi->query($sql);
$portofolio_flat = $result->fetch_all(MYSQLI_ASSOC);

// Kelompokkan data berdasarkan nama jurusan, agar formatnya sama seperti JSON asli
$portofolio_grouped = [];
foreach ($portofolio_flat as $item) {
    $jurusan_nama = $item['nama_jurusan'];
    unset($item['nama_jurusan']); // Hapus properti ini dari item
    
    if (!isset($portofolio_grouped[$jurusan_nama])) {
        $portofolio_grouped[$jurusan_nama] = [];
    }
    $portofolio_grouped[$jurusan_nama][] = $item;
}

echo json_encode($portofolio_grouped);
$koneksi->close();
?>