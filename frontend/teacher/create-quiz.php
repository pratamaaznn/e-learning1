<?php
require_once '../config/init.php';
requireRole('teacher');

$user = getUser();
$success = '';
$error = '';

if ($_POST && isset($_POST['create_quiz'])) {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $subject = $_POST['subject'];
    $grade_level = $_POST['grade_level'];
    $time_limit = intval($_POST['time_limit']);
    
    $stmt = $db->prepare("INSERT INTO quizzes (title, description, subject, grade_level, time_limit, teacher_id) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$title, $description, $subject, $grade_level, $time_limit, $user['id']])) {
        $quiz_id = $db->lastInsertId();
        $_SESSION['current_quiz_id'] = $quiz_id;
        header('Location: add-questions.php?quiz_id=' . $quiz_id);
        exit();
    } else {
        $error = 'Gagal membuat kuis';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Kuis - E-Learning SMP</title>
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
                    <h1 class="text-xl font-bold">Buat Kuis</h1>
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
            <h2 class="text-2xl font-bold text-gray-800 mb-6">Buat Kuis Baru</h2>

            <?php if ($error): ?>
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <input type="hidden" name="create_quiz" value="1">
                
                <div>
                    <label for="title" class="block text-sm font-medium text-gray-700 mb-2">Judul Kuis</label>
                    <input type="text" id="title" name="title" required 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-gray-700 mb-2">Deskripsi</label>
                    <textarea id="description" name="description" rows="4" 
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                </div>

                <div class="grid md:grid-cols-2 gap-4">
                    <div>
                        <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">Mata Pelajaran</label>
                        <select id="subject" name="subject" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Mata Pelajaran</option>
                            <option value="Matematika">Matematika</option>
                            <option value="Bahasa Indonesia">Bahasa Indonesia</option>
                            <option value="Bahasa Inggris">Bahasa Inggris</option>
                            <option value="IPA">IPA</option>
                            <option value="IPS">IPS</option>
                            <option value="PKn">PKn</option>
                            <option value="Seni Budaya">Seni Budaya</option>
                            <option value="PJOK">PJOK</option>
                        </select>
                    </div>

                    <div>
                        <label for="grade_level" class="block text-sm font-medium text-gray-700 mb-2">Kelas</label>
                        <select id="grade_level" name="grade_level" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">Pilih Kelas</option>
                            <option value="VII">VII</option>
                            <option value="VIII">VIII</option>
                            <option value="IX">IX</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label for="time_limit" class="block text-sm font-medium text-gray-700 mb-2">Batas Waktu (menit)</label>
                    <input type="number" id="time_limit" name="time_limit" value="30" min="5" max="180" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex space-x-4">
                    <button type="submit" 
                            class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                        <i class="fas fa-plus mr-2"></i>Buat Kuis & Tambah Soal
                    </button>
                    <a href="dashboard.php" 
                       class="flex-1 bg-gray-500 text-white py-3 px-6 rounded-lg hover:bg-gray-600 transition duration-200 font-medium text-center">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>