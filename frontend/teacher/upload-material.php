<?php
require_once '../config/init.php';
requireRole('teacher');

$user = getUser();
$success = '';
$error = '';

if ($_POST) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $subject = $_POST['subject'];
    $grade_level = $_POST['grade_level'];
    
    // Validasi input
    if (empty($title)) {
        $error = 'Judul materi harus diisi';
    } elseif (empty($subject)) {
        $error = 'Mata pelajaran harus dipilih';
    } elseif (empty($grade_level)) {
        $error = 'Kelas harus dipilih';
    } else {
        $file_path = '';
        
        // Handle file upload
        if (isset($_FILES['material_file']) && $_FILES['material_file']['error'] === 0) {
            $upload_dir = '../uploads/materials/';
            
            // Buat direktori jika belum ada
            if (!is_dir($upload_dir)) {
                if (!mkdir($upload_dir, 0755, true)) {
                    $error = 'Gagal membuat direktori upload';
                }
            }
            
            if (!$error) {
                // Validasi ukuran file (max 10MB)
                $max_size = 10 * 1024 * 1024; // 10MB
                if ($_FILES['material_file']['size'] > $max_size) {
                    $error = 'Ukuran file terlalu besar. Maksimal 10MB';
                } else {
                    // Validasi tipe file
                    $allowed_extensions = ['pdf', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'jpg', 'jpeg', 'png'];
                    $file_extension = strtolower(pathinfo($_FILES['material_file']['name'], PATHINFO_EXTENSION));
                    
                    if (!in_array($file_extension, $allowed_extensions)) {
                        $error = 'Tipe file tidak diizinkan. Gunakan: PDF, DOC, DOCX, PPT, PPTX, TXT, JPG, PNG';
                    } else {
                        // Generate nama file unik
                        $file_name = uniqid() . '_' . time() . '.' . $file_extension;
                        $full_path = $upload_dir . $file_name;
                        
                        if (move_uploaded_file($_FILES['material_file']['tmp_name'], $full_path)) {
                            $file_path = 'uploads/materials/' . $file_name;
                            
                            // Set permissions untuk file
                            chmod($full_path, 0644);
                        } else {
                            $error = 'Gagal upload file. Periksa permissions direktori uploads/materials/';
                        }
                    }
                }
            }
        }
        
        // Simpan ke database jika tidak ada error
        if (!$error) {
            try {
                $stmt = $db->prepare("INSERT INTO materials (title, description, file_path, subject, grade_level, teacher_id, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
                if ($stmt->execute([$title, $description, $file_path, $subject, $grade_level, $user['id']])) {
                    $success = 'Materi berhasil diupload!';
                    // Reset form values
                    $_POST = array();
                } else {
                    $error = 'Gagal menyimpan materi ke database';
                    // Hapus file jika gagal simpan ke database
                    if ($file_path && file_exists('../' . $file_path)) {
                        unlink('../' . $file_path);
                    }
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
                // Hapus file jika gagal simpan ke database
                if ($file_path && file_exists('../' . $file_path)) {
                    unlink('../' . $file_path);
                }
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Materi - E-Learning SMP</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-white hover:text-gray-200">
                        <i class="fas fa-arrow-left mr-2"></i>Kembali
                    </a>
                    <h1 class="text-xl font-bold">Upload Materi</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                    <a href="../auth/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-2xl mx-auto bg-white rounded-lg shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Upload Materi Pembelajaran</h2>

            <?php if ($success): ?>
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-check-circle mr-2"></i><?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <i class="fas fa-exclamation-circle mr-2"></i><?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data" class="space-y-6">
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Materi *</label>
                    <input type="text" id="title" name="title" required 
                           value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="description" name="description" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"><?php echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; ?></textarea>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran *</label>
                        <select id="subject" name="subject" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Mata Pelajaran</option>
                            <option value="Matematika" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Matematika') ? 'selected' : ''; ?>>Matematika</option>
                            <option value="Bahasa Indonesia" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Bahasa Indonesia') ? 'selected' : ''; ?>>Bahasa Indonesia</option>
                            <option value="Bahasa Inggris" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Bahasa Inggris') ? 'selected' : ''; ?>>Bahasa Inggris</option>
                            <option value="IPA" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'IPA') ? 'selected' : ''; ?>>IPA</option>
                            <option value="IPS" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'IPS') ? 'selected' : ''; ?>>IPS</option>
                            <option value="PKn" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'PKn') ? 'selected' : ''; ?>>PKn</option>
                            <option value="Seni Budaya" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'Seni Budaya') ? 'selected' : ''; ?>>Seni Budaya</option>
                            <option value="PJOK" <?php echo (isset($_POST['subject']) && $_POST['subject'] == 'PJOK') ? 'selected' : ''; ?>>PJOK</option>
                        </select>
                    </div>

                    <div>
                        <label for="grade_level" class="block text-sm font-medium text-gray-700 mb-2">Kelas *</label>
                        <select id="grade_level" name="grade_level" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Kelas</option>
                            <option value="VII" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] == 'VII') ? 'selected' : ''; ?>>VII</option>
                            <option value="VIII" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] == 'VIII') ? 'selected' : ''; ?>>VIII</option>
                            <option value="IX" <?php echo (isset($_POST['grade_level']) && $_POST['grade_level'] == 'IX') ? 'selected' : ''; ?>>IX</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="material_file" class="block text-sm font-medium text-gray-700 mb-2">File Materi</label>
                    <input type="file" id="material_file" name="material_file" 
                           accept=".pdf,.doc,.docx,.ppt,.pptx,.txt,.jpg,.jpeg,.png" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-sm text-gray-500 mt-1">Format yang didukung: PDF, DOC, DOCX, PPT, PPTX, TXT, JPG, PNG (Maksimal 10MB)</p>
                </div>

                <div class="flex space-x-4">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                        <i class="fas fa-upload mr-2"></i>Upload Materi
                    </button>
                    <a href="dashboard.php" 
                       class="flex-1 bg-gray-500 text-white py-3 px-6 rounded-lg hover:bg-gray-600 transition duration-200 font-medium text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Validasi ukuran file di client side
        document.getElementById('material_file').addEventListener('change', function() {
            const fileSize = this.files[0]?.size;
            const maxSize = 10 * 1024 * 1024; // 10MB
            
            if (fileSize > maxSize) {
                alert('Ukuran file terlalu besar. Maksimal 10MB');
                this.value = '';
            }
        });
    </script>
</body>
</html>