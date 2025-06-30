<?php
require_once '../config/init.php';
requireRole('student');

$user = getUser();
$quiz_id = intval($_GET['id']);

// Get quiz details
$stmt = $db->prepare("SELECT q.*, u.full_name as teacher_name FROM quizzes q JOIN users u ON q.teacher_id = u.id WHERE q.id = ?");
$stmt->execute([$quiz_id]);
$quiz = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$quiz) {
    header('Location: dashboard.php');
    exit();
}

// Get questions
$stmt = $db->prepare("SELECT * FROM questions WHERE quiz_id = ? ORDER BY id");
$stmt->execute([$quiz_id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($questions)) {
    echo "Kuis ini belum memiliki soal.";
    exit();
}

// Process quiz submission
if ($_POST && isset($_POST['submit_quiz'])) {
    $score = 0;
    $total_questions = count($questions);
    
    foreach ($questions as $question) {
        $answer_key = 'question_' . $question['id'];
        if (isset($_POST[$answer_key]) && $_POST[$answer_key] === $question['correct_answer']) {
            $score += $question['points'];
        }
    }
    
    // Save quiz attempt
    $stmt = $db->prepare("INSERT INTO quiz_attempts (quiz_id, student_id, score, total_questions) VALUES (?, ?, ?, ?)");
    $stmt->execute([$quiz_id, $user['id'], $score, $total_questions]);
    
    // Redirect to results
    header('Location: quiz-result.php?attempt_id=' . $db->lastInsertId());
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quiz: <?php echo htmlspecialchars($quiz['title']); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100">
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-xl font-bold">Quiz: <?php echo htmlspecialchars($quiz['title']); ?></h1>
                </div>
                <div class="flex items-center space-x-4">
                    <div id="timer" class="bg-red-500 px-4 py-2 rounded font-bold">
                        <i class="fas fa-clock mr-2"></i><span id="time-left"><?php echo $quiz['time_limit']; ?>:00</span>
                    </div>
                    <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                </div>
            </div>
        </div>
    </nav>

    <div class="container mx-auto px-4 py-8">
        <div class="max-w-4xl mx-auto">
            <!-- Quiz Info -->
            <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-2"><?php echo htmlspecialchars($quiz['title']); ?></h2>
                <p class="text-gray-600 mb-4"><?php echo htmlspecialchars($quiz['description']); ?></p>
                <div class="grid md:grid-cols-4 gap-4 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-book mr-2 text-blue-600"></i>
                        <span><?php echo htmlspecialchars($quiz['subject']); ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-layer-group mr-2 text-green-600"></i>
                        <span>Kelas <?php echo htmlspecialchars($quiz['grade_level']); ?></span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-question-circle mr-2 text-purple-600"></i>
                        <span><?php echo count($questions); ?> Soal</span>
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-user mr-2 text-orange-600"></i>
                        <span><?php echo htmlspecialchars($quiz['teacher_name']); ?></span>
                    </div>
                </div>
            </div>

            <!-- Questions -->
            <form method="POST" id="quiz-form">
                <input type="hidden" name="submit_quiz" value="1">
                
                <?php foreach ($questions as $index => $question): ?>
                    <div class="bg-white rounded-lg shadow-lg p-6 mb-6">
                        <div class="flex items-start justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-800">
                                Soal <?php echo $index + 1; ?>
                            </h3>
                            <span class="bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded">
                                <?php echo $question['points']; ?> poin
                            </span>
                        </div>
                        
                        <p class="text-gray-700 mb-6"><?php echo htmlspecialchars($question['question_text']); ?></p>
                        
                        <div class="space-y-3">
                            <?php
                            $options = [
                                'a' => $question['option_a'],
                                'b' => $question['option_b'],
                                'c' => $question['option_c'],
                                'd' => $question['option_d']
                            ];
                            
                            foreach ($options as $key => $option): ?>
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition duration-200">
                                    <input type="radio" 
                                           name="question_<?php echo $question['id']; ?>" 
                                           value="<?php echo $key; ?>" 
                                           class="mr-3 text-blue-600">
                                    <span class="font-medium text-gray-800 mr-2"><?php echo strtoupper($key); ?>.</span>
                                    <span class="text-gray-700"><?php echo htmlspecialchars($option); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <div class="bg-white rounded-lg shadow-lg p-6 text-center">
                    <button type="submit" 
                            class="bg-green-600 text-white px-8 py-3 rounded-lg hover:bg-green-700 transition duration-200 font-bold text-lg"
                            onclick="return confirm('Apakah Anda yakin ingin mengirim jawaban?')">
                        <i class="fas fa-paper-plane mr-2"></i>Kirim Jawaban
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Timer functionality
        let timeLeft = <?php echo $quiz['time_limit'] * 60; ?>; // Convert minutes to seconds
        const timerElement = document.getElementById('time-left');
        const form = document.getElementById('quiz-form');

        function updateTimer() {
            const minutes = Math.floor(timeLeft / 60);
            const seconds = timeLeft % 60;
            timerElement.textContent = minutes + ':' + (seconds < 10 ? '0' : '') + seconds;

            if (timeLeft <= 0) {
                alert('Waktu habis! Kuis akan dikirim otomatis.');
                form.submit();
                return;
            }

            timeLeft--;
        }

        // Update timer every second
        setInterval(updateTimer, 1000);

        // Prevent form resubmission on page refresh
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>
</body>
</html>