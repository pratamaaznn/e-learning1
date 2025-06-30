<?php
require_once '../config/init.php';
requireRole('teacher');

$user = getUser();

// Get statistics
$stmt = $db->prepare("SELECT COUNT(*) FROM materials WHERE teacher_id = ?");
$stmt->execute([$user['id']]);
$material_count = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM quizzes WHERE teacher_id = ?");
$stmt->execute([$user['id']]);
$quiz_count = $stmt->fetchColumn();

$stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE role = 'student'");
$stmt->execute();
$student_count = $stmt->fetchColumn();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Guru - E-Learning</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <i class="fas fa-graduation-cap text-2xl"></i>
                    <h1 class="text-xl font-bold">E-Learning</h1>
                </div>
              <div class="flex items-center space-x-4">
    <span>Selamat datang, <?php echo htmlspecialchars($user['full_name']); ?></span>

    <!-- Ikon Profil yang Bisa Diklik -->
    <a href="edit-profile.php" title="Edit Profil" class="hover:opacity-80 transition">
        <img src="../logo/guru.jpg" alt="Profil" class="w-10 h-10 rounded-full border-2 border-white shadow-md">
    </a>

    <a href="../auth/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition duration-200 flex items-center">
        <i class="fas fa-sign-out-alt mr-2"></i>Keluar
    </a>
</div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Guru</h2>
            <p class="text-gray-600">Kelola materi pembelajaran dan kuis untuk siswa</p>
        </div>

        <!-- Statistics Cards -->
        <div class="grid md:grid-cols-3 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Materi</h3>
                        <p class="text-3xl font-bold text-blue-600"><?php echo $material_count; ?></p>
                    </div>
                    <i class="fas fa-book text-4xl text-blue-200"></i>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Quiz</h3>
                        <p class="text-3xl font-bold text-green-600"><?php echo $quiz_count; ?></p>
                    </div>
                    <i class="fas fa-question-circle text-4xl text-green-200"></i>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-700">Siswa</h3>
                        <p class="text-3xl font-bold text-purple-600"><?php echo $student_count; ?></p>
                    </div>
                    <i class="fas fa-users text-4xl text-purple-200"></i>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="grid md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-upload mr-2 text-blue-600"></i>Upload Materi
                </h3>
                <p class="text-gray-600 mb-4">Upload file materi pembelajaran untuk siswa</p>
                <a href="upload-material.php" 
                   class="inline-block bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition duration-200">
                    Upload Materi
                </a>
            </div>

            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-plus mr-2 text-green-600"></i>Buat Kuis
                </h3>
                <p class="text-gray-600 mb-4">Buat kuis dan soal untuk mengukur pemahaman siswa</p>
                <a href="create-quiz.php" 
                   class="inline-block bg-green-600 text-white px-6 py-3 rounded-lg hover:bg-green-700 transition duration-200">
                    Buat Kuis
                </a>
            </div>
        </div>

        <!-- Recent Activities -->
        <div class="mt-8 bg-white rounded-lg shadow-lg p-6">
            <h3 class="text-xl font-bold text-gray-800 mb-4">
                <i class="fas fa-clock mr-2"></i>Aktivitas Terbaru
            </h3>
            <div class="space-y-4">
                <?php
                $stmt = $db->prepare("
                    SELECT 'material' as type, title, created_at FROM materials WHERE teacher_id = ?
                    UNION ALL
                    SELECT 'quiz' as type, title, created_at FROM quizzes WHERE teacher_id = ?
                    ORDER BY created_at DESC LIMIT 5
                ");
                $stmt->execute([$user['id'], $user['id']]);
                $activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

                if (empty($activities)): ?>
                    <p class="text-gray-500 italic">Belum ada aktivitas</p>
                <?php else: ?>
                    <?php foreach ($activities as $activity): ?>
                        <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg">
                            <i class="fas fa-<?php echo $activity['type'] === 'material' ? 'book' : 'question-circle'; ?> text-blue-600"></i>
                            <div>
                                <p class="font-medium"><?php echo htmlspecialchars($activity['title']); ?></p>
                                <p class="text-sm text-gray-500"><?php echo date('d/m/Y H:i', strtotime($activity['created_at'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            <div class="bg-white rounded-lg shadow-lg p-6 text-center">
    <div class="flex items-center justify-center w-16 h-16 bg-orange-500 rounded-full mx-auto mb-4">
        <i class="fas fa-chalkboard-teacher text-2xl text-white"></i>
    </div>
    <h3 class="text-xl font-bold text-gray-800 mb-2">Untuk Guru</h3>
    <p class="text-gray-600">Upload materi, Upload Quiz</p>
</div>
            </div>
        </div>
    </div>
</body>
</html>