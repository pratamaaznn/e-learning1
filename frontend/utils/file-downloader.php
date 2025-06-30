<?php
require_once '../config/init.php';

// Periksa apakah user sudah login (siswa atau guru)
if (!isLoggedIn()) {
    header("Location: ../auth/login.php");
    exit();
}

// Ambil ID material dari parameter
$material_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($material_id <= 0) {
    die('ID material tidak valid');
}

// Query untuk mengambil data material
try {
    $stmt = $db->prepare("SELECT * FROM materials WHERE id = ?");
    $stmt->execute([$material_id]);
    $material = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$material) {
        die('Material tidak ditemukan');
    }
    
    // Path file berdasarkan field file_path di database
    $file_path = '../' . $material['file_path'];
    
    if (!file_exists($file_path)) {
        die('File tidak ditemukan di server: ' . $material['file_path']);
    }
    
    // Ambil nama file dari path
    $file_name = basename($material['file_path']);
    $file_extension = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
    
    // Set header untuk force download
    $display_name = $material['title'] . '.' . $file_extension;
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename="' . $display_name . '"');
    header('Content-Length: ' . filesize($file_path));
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    
    // Baca dan output file
    readfile($file_path);
    exit;
    
} catch (PDOException $e) {
    die('Database Error: ' . $e->getMessage());
}
?>