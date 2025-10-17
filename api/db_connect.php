<?php
// Konfigurasi koneksi database
$host = "localhost";
$username = "root";
$password = "";
$dbname = "jhic";

// Membuat koneksi
$conn = new mysqli($host, $username, $password, $dbname);

// Cek koneksi
if ($conn->connect_error) {
  die("Koneksi gagal: " . $conn->connect_error);
}

// Query untuk mengambil data berita
$sql = "SELECT judul, ringkasan, gambar FROM berita WHERE aktif = 1 ORDER BY tanggal DESC LIMIT 3";
$result = $conn->query($sql);

$berita = array();
if ($result->num_rows > 0) {
  // Ambil data dari setiap baris
  while($row = $result->fetch_assoc()) {
    $berita[] = $row;
  }
}

// Set header sebagai JSON dan kirimkan hasilnya
header('Content-Type: application/json');
echo json_encode($berita);

$conn->close();
?>