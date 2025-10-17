<?php
require '../db_connect.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'POST') {
    $id = $_POST['id'] ?? null;
    $action = $_POST['action'] ?? 'save';

    if ($action === 'toggle_status') {
        if ($id) {
            $stmt = $koneksi->prepare("UPDATE berita SET status = IF(status='aktif', 'nonaktif', 'aktif') WHERE id = ?");
            $stmt->bind_param("i", $id);
            if ($stmt->execute()) echo json_encode(['status' => 'success']);
            else echo json_encode(['status' => 'error', 'message' => 'Gagal mengubah status.']);
        }
    } else { // Aksi 'save' (Create/Update)
        $judul = $_POST['judul'] ?? '';
        $ringkasan = $_POST['ringkasan'] ?? '';
        $gambar_path = $_POST['url_gambar_existing'] ?? null;

        if (isset($_FILES['url_gambar']) && $_FILES['url_gambar']['error'] == 0) {
            $upload_dir = '../../public/uploads/berita/';
            if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);
            $file_name = time() . '_' . basename($_FILES['url_gambar']['name']);
            if (move_uploaded_file($_FILES['url_gambar']['tmp_name'], $upload_dir . $file_name)) {
                $gambar_path = '/uploads/berita/' . $file_name;
            }
        }

        if ($id) {
            $stmt = $koneksi->prepare("UPDATE berita SET judul=?, ringkasan=?, url_gambar=? WHERE id=?");
            $stmt->bind_param("sssi", $judul, $ringkasan, $gambar_path, $id);
        } else {
            $stmt = $koneksi->prepare("INSERT INTO berita (judul, ringkasan, url_gambar) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $judul, $ringkasan, $gambar_path);
        }

        if ($stmt->execute()) echo json_encode(['status' => 'success']);
        else echo json_encode(['status' => 'error', 'message' => $stmt->error]);
    }
} elseif ($method === 'DELETE') {
    $data = json_decode(file_get_contents("php://input"));
    $id = $data->id ?? null;
    if ($id) {
        $stmt = $koneksi->prepare("DELETE FROM berita WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) echo json_encode(['status' => 'success']);
        else echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus data.']);
    }
}

$koneksi->close();
?>