<?php
require_once '../config/init.php';
requireRole('student');

$user = getUser();
$attempt_id = intval($_GET['attempt_id']);

// Get quiz attempt details
$stmt = $db->prepare("
    SELECT qa.*, q.title as quiz_title, q.subject, q.grade_level, u.full_name as teacher_name 
    FROM quiz_attempts qa 
    JOIN quizzes q ON qa.quiz_id = q.id 
    JOIN users u ON q.teacher_id = u.id 
    WHERE qa.id = ? AND qa.student_id = ?
");
$stmt->execute([$attempt_id, $user['id']]);
$attempt = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$attempt) {
    header('Location: dashboard.php');
    exit();
}

$percentage = round(($attempt['score'] / $attempt['total_questions']) * 100);
$grade = $percentage >= 90 ? 'A' : ($percentage >= 80 ? 'B' : ($percentage >= 70 ? 'C' : ($percentage >= 60 ? 'D' : 'E')));
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Kuis - <?php echo htmlspecialchars($attempt['quiz_title']); ?></title>
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
                        <i class="fas fa-arrow-left mr-2"></i>
                    </a>
                    <h1 class="text-xl font-bold">Hasil Kuis</h1>
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
        <div class="max-w-2xl mx-auto">
            <!-- Result Card -->
            <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                <!-- Header -->
                <div class="<?php echo $percentage >= 70 ? 'bg-green-600' : 'bg-red-600'; ?> text-white p-6 text-center">
                    <div class="inline-flex items-center justify-center w-20 h-20 bg-white bg-opacity-20 rounded-full mb-4">
                        <i class="fas fa-<?php echo $percentage >= 70 ? 'check' : 'times'; ?> text-3xl"></i>
                    </div>
                    <h2 class="text-2xl font-bold mb-2">
                        <?php echo $percentage >= 70 ? 'Selamat!' : 'Tetap Semangat!'; ?>
                    </h2>
                    <p class="text-lg opacity-90">
                        <?php echo $percentage >= 70 ? 'Anda berhasil menyelesaikan kuis dengan baik' : 'Jangan menyerah, terus belajar dan coba lagi'; ?>
                    </p>
                </div>

                <!-- Quiz Info -->
                <div class="p-6 border-b border-gray-200">
                    <h3 class="text-xl font-bold text-gray-800 mb-4"><?php echo htmlspecialchars($attempt['quiz_title']); ?></h3>
                    <div class="grid md:grid-cols-3 gap-4 text-sm">
                        <div class="flex items-center">
                            <i class="fas fa-book mr-2 text-blue-600"></i>
                            <span><?php echo htmlspecialchars($attempt['subject']); ?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-layer-group mr-2 text-green-600"></i>
                            <span>Kelas <?php echo htmlspecialchars($attempt['grade_level']); ?></span>
                        </div>
                        <div class="flex items-center">
                            <i class="fas fa-user mr-2 text-purple-600"></i>
                            <span><?php echo htmlspecialchars($attempt['teacher_name']); ?></span>
                        </div>
                    </div>
                </div>

                <!-- Score Details -->
                <div class="p-6">
                    <div class="text-center mb-6">
                        <div class="inline-flex items-center justify-center w-32 h-32 bg-gray-100 rounded-full mb-4">
                            <div class="text-center">
                                <div class="text-4xl font-bold <?php echo $percentage >= 70 ? 'text-green-600' : 'text-red-600'; ?>">
                                    <?php echo $percentage; ?>%
                                </div>
                                <div class="text-sm text-gray-500 font-medium">NILAI</div>
                            </div>
                        </div>
                        <div class="text-2xl font-bold <?php echo $percentage >= 70 ? 'text-green-600' : 'text-red-600'; ?> mb-2">
                            Grade: <?php echo $grade; ?>
                        </div>
                    </div>

                    <div class="grid md:grid-cols-2 gap-6 mb-6">
                        <div class="text-center p-4 bg-blue-50 rounded-lg">
                            <div class="text-2xl font-bold text-blue-600 mb-1">
                                <?php echo $attempt['score']; ?>
                            </div>
                            <div class="text-sm text-gray-600">Jawaban Benar</div>
                        </div>
                        <div class="text-center p-4 bg-gray-50 rounded-lg">
                            <div class="text-2xl font-bold text-gray-600 mb-1">
                                <?php echo $attempt['total_questions']; ?>
                            </div>
                            <div class="text-sm text-gray-600">Total Soal</div>
                        </div>
                    </div>

                    <div class="text-center text-sm text-gray-500 mb-6">
                        Dikerjakan pada: <?php echo date('d F Y, H:i', strtotime($attempt['completed_at'])); ?>
                    </div>

                    <!-- Actions -->
                    <div class="flex space-x-4">
                        <a href="dashboard.php" 
                           class="flex-1 bg-blue-600 text-white py-3 px-6 rounded-lg hover:bg-blue-700 transition duration-200 font-medium text-center">
                            <i class="fas fa-home mr-2"></i>Kembali ke Dashboard
                        </a>
                        <a href="quizzes.php" 
                           class="flex-1 bg-green-600 text-white py-3 px-6 rounded-lg hover:bg-green-700 transition duration-200 font-medium text-center">
                            <i class="fas fa-redo mr-2"></i>Kuis Lainnya
                        </a>
                    </div>
                </div>
            </div>

            <!-- Performance Feedback -->
            <div class="mt-6 bg-white rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-bold text-gray-800 mb-4">
                    <i class="fas fa-lightbulb mr-2 text-yellow-500"></i>Feedback
                </h3>
                <div class="text-gray-700">
                    <?php if ($percentage >= 90): ?>
                        <p class="mb-2"><strong>Luar biasa!</strong> Anda menguasai materi dengan sangat baik.</p>
                        <p>Pertahankan prestasi yang excellent ini dan terus tingkatkan kemampuan Anda.</p>
                    <?php elseif ($percentage >= 80): ?>
                        <p class="mb-2"><strong>Bagus sekali!</strong> Pemahaman Anda terhadap materi sudah baik.</p>
                        <p>Dengan sedikit usaha lagi, Anda bisa mencapai hasil yang sempurna.</p>
                    <?php elseif ($percentage >= 70): ?>
                        <p class="mb-2"><strong>Cukup baik!</strong> Anda sudah memahami sebagian besar materi.</p>
                        <p>Pelajari kembali materi yang masih kurang dipahami untuk hasil yang lebih baik.</p>
                    <?php elseif ($percentage >= 60): ?>
                        <p class="mb-2"><strong>Perlu peningkatan.</strong> Masih ada beberapa konsep yang perlu dipelajari lebih dalam.</p>
                        <p>Jangan menyerah, ulangi materi dan coba kuis serupa untuk meningkatkan pemahaman.</p>
                    <?php else: ?>
                        <p class="mb-2"><strong>Butuh lebih banyak latihan.</strong> Sepertinya materi ini masih cukup menantang untuk Anda.</p>
                        <p>Jangan berkecil hati, pelajari materi dengan lebih teliti dan minta bantuan guru jika diperlukan.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>