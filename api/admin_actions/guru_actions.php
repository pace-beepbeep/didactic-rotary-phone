<?php
require '../db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    // Aksi untuk MENAMBAH atau MENGUPDATE data
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    $nama = $_POST['nama'] ?? '';
    $jurusan_id = $_POST['jurusan_id'] ?? 0;
    $foto_path = $_POST['foto_existing'] ?? null;

    // Cek jika ada file gambar yang diunggah
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] == 0) {
        $upload_dir = '../../public/uploads/guru/';
        if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
        
        $file_name = time() . '_' . basename($_FILES['foto']['name']);
        $target_path = $upload_dir . $file_name;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_path)) {
            $foto_path = '/uploads/guru/' . $file_name;
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Gagal mengunggah file.']);
            exit;
        }
    }

    if ($id) {
        // UPDATE
        $stmt = $koneksi->prepare("UPDATE guru SET jurusan_id=?, nama=?, foto=? WHERE id=?");
        $stmt->bind_param("issi", $jurusan_id, $nama, $foto_path, $id);
    } else {
        // INSERT
        $stmt = $koneksi->prepare("INSERT INTO guru (jurusan_id, nama, foto) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $jurusan_id, $nama, $foto_path);
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
        $stmt = $koneksi->prepare("DELETE FROM guru WHERE id = ?");
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