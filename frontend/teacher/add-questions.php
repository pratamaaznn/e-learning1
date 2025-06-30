<?php
require_once '../config/init.php';
requireRole('teacher');

$user = getUser();
$quiz_id = intval($_GET['quiz_id']);

// Verify quiz ownership
$stmt = $db->prepare("SELECT * FROM quizzes WHERE id = ? AND teacher_id = ?");
$stmt->execute([$quiz_id, $user['id']]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    header('Location: dashboard.php');
    exit();
}

$success = '';
$error = '';

if ($_POST) {
    $question_text = trim($_POST['question_text']);
    $option_a = trim($_POST['option_a']);
    $option_b = trim($_POST['option_b']);
    $option_c = trim($_POST['option_c']);
    $option_d = trim($_POST['option_d']);
    $correct_answer = $_POST['correct_answer'];
    $points = intval($_POST['points']);

    $stmt = $db->prepare("INSERT INTO questions (quiz_id, question_text, option_a, option_b, option_c, option_d, correct_answer, points) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$quiz_id, $question_text, $option_a, $option_b, $option_c, $option_d, $correct_answer, $points])) {
        $success = 'Soal berhasil ditambahkan!';
    } else {
        $error = 'Gagal menambahkan soal';
    }
}

// Get existing questions
$stmt = $db->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Soal - <?php echo htmlspecialchars($quiz['title']); ?></title>
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
                    <h1 class="text-xl font-bold">Tambah Soal: <?php echo htmlspecialchars($quiz['title']); ?></h1>
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
        <div class="grid lg:grid-cols-2 gap-8">
            <!-- Add Question Form -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Tambah Soal Baru</h2>

                <?php if ($success): ?>
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($success); ?>
                    </div>
                <?php endif; ?>

                <?php if ($error): ?>
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" class="space-y-4">
                    <div>
                        <label for="question_text" class="block text-sm font-medium text-gray-700 mb-2">Pertanyaan</label>
                        <textarea id="question_text" name="question_text" rows="3" required 
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500"></textarea>
                    </div>

                    <div class="space-y-3">
                        <div>
                            <label for="option_a" class="block text-sm font-medium text-gray-700 mb-1">Pilihan A</label>
                            <input type="text" id="option_a" name="option_a" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="option_b" class="block text-sm font-medium text-gray-700 mb-1">Pilihan B</label>
                            <input type="text" id="option_b" name="option_b" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="option_c" class="block text-sm font-medium text-gray-700 mb-1">Pilihan C</label>
                            <input type="text" id="option_c" name="option_c" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>

                        <div>
                            <label for="option_d" class="block text-sm font-medium text-gray-700 mb-1">Pilihan D</label>
                            <input type="text" id="option_d" name="option_d" required 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label for="correct_answer" class="block text-sm font-medium text-gray-700 mb-1">Jawaban Benar</label>
                            <select id="correct_answer" name="correct_answer" required 
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="">Pilih Jawaban</option>
                                <option value="a">A</option>
                                <option value="b">B</option>
                                <option value="c">C</option>
                                <option value="d">D</option>
                            </select>
                        </div>

                        <div>
                            <label for="points" class="block text-sm font-medium text-gray-700 mb-1">Poin</label>
                            <input type="number" id="points" name="points" value="1" min="1" max="10" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>

                    <button type="submit" 
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                        <i class="fas fa-plus mr-2"></i>Tambah Soal
                    </button>
                </form>
            </div>

            <!-- Existing Questions -->
            <div class="bg-white rounded-lg shadow-lg p-6">
                <h2 class="text-xl font-bold text-gray-800 mb-6">Soal yang Sudah Ditambahkan (<?php echo count($questions); ?>)</h2>

                <?php if (empty($questions)): ?>
                    <p class="text-gray-500 italic">Belum ada soal yang ditambahkan</p>
                <?php else: ?>
                    <div class="space-y-4 max-h-96 overflow-y-auto">
                        <?php foreach ($questions as $index => $question): ?>
                            <div class="border border-gray-200 rounded-lg p-4">
                                <h4 class="font-semibold text-gray-800 mb-2">Soal <?php echo $index + 1; ?></h4>
                                <p class="text-gray-700 mb-3"><?php echo htmlspecialchars($question['question_text']); ?></p>
                                <div class="text-sm space-y-1">
                                    <p class="<?php echo $question['correct_answer'] === 'a' ? 'text-green-600 font-medium' : 'text-gray-600'; ?>">
                                        A. <?php echo htmlspecialchars($question['option_a']); ?>
                                    </p>
                                    <p class="<?php echo $question['correct_answer'] === 'b' ? 'text-green-600 font-medium' : 'text-gray-600'; ?>">
                                        B. <?php echo htmlspecialchars($question['option_b']); ?>
                                    </p>
                                    <p class="<?php echo $question['correct_answer'] === 'c' ? 'text-green-600 font-medium' : 'text-gray-600'; ?>">
                                        C. <?php echo htmlspecialchars($question['option_c']); ?>
                                    </p>
                                    <p class="<?php echo $question['correct_answer'] === 'd' ? 'text-green-600 font-medium' : 'text-gray-600'; ?>">
                                        D. <?php echo htmlspecialchars($question['option_d']); ?>
                                    </p>
                                </div>
                                <p class="text-xs text-gray-500 mt-2">Poin: <?php echo $question['points']; ?></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>

                <?php if (!empty($questions)): ?>
                    <div class="mt-6 text-center">
                        <a href="dashboard.php" 
                           class="bg-green-600 text-white px-6 py-2 rounded-lg hover:bg-green-700 transition duration-200">
                            <i class="fas fa-check mr-2"></i>Selesai
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</body>
</html>