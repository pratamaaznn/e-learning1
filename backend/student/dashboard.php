<?php
require_once '../config/init.php';
requireRole('student');


$user = getUser();

// Get available materials
$stmt = $db->prepare("SELECT m.*, u.full_name as teacher_name FROM materials m JOIN users u ON m.teacher_id = u.id ORDER BY m.created_at DESC LIMIT 6");
$stmt->execute();
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get available quizzes
$stmt = $db->prepare("SELECT q.*, u.full_name as teacher_name FROM quizzes q JOIN users u ON q.teacher_id = u.id ORDER BY q.created_at DESC LIMIT 6");
$stmt->execute();
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get recent quiz attempts
$stmt = $db->prepare("SELECT qa.*, q.title as quiz_title FROM quiz_attempts qa JOIN quizzes q ON qa.quiz_id = q.id WHERE qa.student_id = ? ORDER BY qa.completed_at DESC LIMIT 5");
$stmt->execute([$user['id']]);
$recent_attempts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siswa - E-Learning</title>
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
                    <a href="../auth/logout.php" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded transition duration-200">
                        <i class="fas fa-sign-out-alt mr-2"></i>Logout
                    </a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <?php
        if (isset($_SESSION['success'])) {
            // Tampilkan div alert dengan styling dari Tailwind CSS
            echo '
                <div class="container mx-auto px-4 py-4">
                    <div id="success-alert" class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 flex justify-between items-center" role="alert">
                        <div>
                            <p class="font-bold">Berhasil!</p>
                            <p>' . htmlspecialchars($_SESSION['success']) . '</p>
                        </div>
                        <button onclick="document.getElementById(\'success-alert\').style.display=\'none\'" class="text-xl font-bold">&times;</button>
                    </div>
                </div>
                ';

            // HAPUS pesan dari session agar tidak tampil lagi saat halaman di-refresh
            unset($_SESSION['success']);
        }
        ?>
        <div class="mb-8">
            <h2 class="text-3xl font-bold text-gray-800 mb-2">Siswa</h2>
            <p class="text-gray-600">Akses materi pembelajaran dan kerjakan quiz</p>
        </div>

        <!-- Recent Materials -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-book mr-2 text-blue-600"></i>Materi Terbaru
                </h3>
                <a href="materials.php" class="text-blue-600 hover:underline">Lihat Semua</a>
            </div>

            <?php if (empty($materials)): ?>
                <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                    <i class="fas fa-book text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Belum ada materi tersedia</p>
                </div>
            <?php else: ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($materials as $material): ?>
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-200">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                        <?php echo htmlspecialchars($material['subject']); ?>
                                    </span>
                                    <span class="text-gray-500 text-sm">Kelas <?php echo htmlspecialchars($material['grade_level']); ?></span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($material['title']); ?></h4>
                                <p class="text-gray-600 text-sm mb-3"><?php echo htmlspecialchars(substr($material['description'], 0, 100)); ?>...</p>
                                <p class="text-xs text-gray-500 mb-4">Oleh: <?php echo htmlspecialchars($material['teacher_name']); ?></p>
                                <a href="view-material.php?id=<?php echo $material['id']; ?>"
                                    class="inline-block bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700 transition duration-200 text-sm">
                                    <i class="fas fa-eye mr-1"></i>Lihat Materi
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Available Quizzes -->
        <div class="mb-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-2xl font-bold text-gray-800">
                    <i class="fas fa-question-circle mr-2 text-green-600"></i>Kuis Tersedia
                </h3>
                <a href="quizzes.php" class="text-green-600 hover:underline">Lihat Semua</a>
            </div>

            <?php if (empty($quizzes)): ?>
                <div class="bg-white rounded-lg shadow-lg p-8 text-center">
                    <i class="fas fa-question-circle text-4xl text-gray-400 mb-4"></i>
                    <p class="text-gray-500">Belum ada kuis tersedia</p>
                </div>
            <?php else: ?>
                <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-6">
                    <?php foreach ($quizzes as $quiz): ?>
                        <div class="bg-white rounded-lg shadow-lg overflow-hidden hover:shadow-xl transition duration-200">
                            <div class="p-6">
                                <div class="flex items-center justify-between mb-3">
                                    <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">
                                        <?php echo htmlspecialchars($quiz['subject']); ?>
                                    </span>
                                    <span class="text-gray-500 text-sm">Kelas <?php echo htmlspecialchars($quiz['grade_level']); ?></span>
                                </div>
                                <h4 class="text-lg font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($quiz['title']); ?></h4>
                                <p class="text-gray-600 text-sm mb-3"><?php echo htmlspecialchars(substr($quiz['description'], 0, 100)); ?>...</p>
                                <div class="flex justify-between items-center mb-4">
                                    <p class="text-xs text-gray-500">Oleh: <?php echo htmlspecialchars($quiz['teacher_name']); ?></p>
                                    <p class="text-xs text-orange-600 font-medium">
                                        <i class="fas fa-clock mr-1"></i><?php echo $quiz['time_limit']; ?> menit
                                    </p>
                                </div>
                                <a href="take-quiz.php?id=<?php echo $quiz['id']; ?>"
                                    class="inline-block bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700 transition duration-200 text-sm">
                                    <i class="fas fa-play mr-1"></i>Mulai Kuis
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Recent Quiz Results -->
        <?php if (!empty($recent_attempts)): ?>
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-xl font-bold text-gray-800 mb-4">
                    <i class="fas fa-chart-line mr-2 text-purple-600"></i>Hasil Kuis Terbaru
                </h3>
                <div class="space-y-3">
                    <?php foreach ($recent_attempts as $attempt): ?>
                        <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="font-medium text-gray-800"><?php echo htmlspecialchars($attempt['quiz_title']); ?></h4>
                                <p class="text-sm text-gray-500"><?php echo date('d/m/Y H:i', strtotime($attempt['completed_at'])); ?></p>
                            </div>
                            <div class="text-right">
                                <p class="font-bold text-lg <?php echo ($attempt['score'] / $attempt['total_questions'] * 100) >= 70 ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo $attempt['score']; ?>/<?php echo $attempt['total_questions']; ?>
                                </p>
                                <p class="text-sm text-gray-500">
                                    <?php echo round(($attempt['score'] / $attempt['total_questions']) * 100); ?>%
                                </p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</body>

</html>