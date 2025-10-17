<?php
require '../db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Aksi untuk MENAMBAH atau MENGUPDATE data
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    $nama = $_POST['nama'] ?? '';
    $nama_project = $_POST['namaProject'] ?? '';
    $deskripsi_singkat = $_POST['deskripsiSingkat'] ?? '';
    $bekerja = $_POST['bekerja'] ?? null;
    $jurusan_id = $_POST['jurusan_id'] ?? 0;
    $deskripsi_lengkap = $_POST['deskripsiLengkap'] ?? '';
    $link_project = $_POST['linkProject'] ?? '';
    
    $foto_project_path = $_POST['foto_project_existing'] ?? null; // Untuk update

    // Cek jika ada file gambar yang diunggah
    if (isset($_FILES['fotoProject']) && $_FILES['fotoProject']['error'] == 0) {
        $upload_dir = '../../public/uploads/portofolio/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_name = time() . '_' . basename($_FILES['fotoProject']['name']);
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['fotoProject']['tmp_name'], $target_path)) {
            $foto_project_path = '/uploads/portofolio/' . $file_name;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah file.']);
            exit;
        }
    }

    if ($id) {
        // UPDATE
        $stmt = $koneksi->prepare("UPDATE portofolio SET jurusan_id=?, nama=?, nama_project=?, deskripsi_singkat=?, bekerja=?, foto_project=?, deskripsi_lengkap=?, link_project=? WHERE id=?");
        $stmt->bind_param("isssssssi", $jurusan_id, $nama, $nama_project, $deskripsi_singkat, $bekerja, $foto_project_path, $deskripsi_lengkap, $link_project, $id);
    } else {
        // INSERT
        $stmt = $koneksi->prepare("INSERT INTO portofolio (jurusan_id, nama, nama_project, deskripsi_singkat, bekerja, foto_project, deskripsi_lengkap, link_project) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssss", $jurusan_id, $nama, $nama_project, $deskripsi_singkat, $bekerja, $foto_project_path, $deskripsi_lengkap, $link_project);
    }

    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menyimpan ke database: ' . $stmt->error]);
    }
    $stmt->close();

} elseif ($method === 'DELETE') {
    // Aksi untuk MENGHAPUS data
    $data = json_decode(file_get_contents("php://input"));
    $id = $data->id ?? null;

    if ($id) {
        $stmt = $koneksi->prepare("DELETE FROM portofolio WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data.']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ID tidak ditemukan.']);
    }
}

$koneksi->close();
?>