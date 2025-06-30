<?php
require_once '../config/init.php';
requireRole('student');


$user = getUser();

// Get available quizzes
$stmt = $db->prepare("SELECT q.*, u.full_name as teacher_name FROM quizzes q JOIN users u ON q.teacher_id = u.id ORDER BY q.created_at DESC");
$stmt->execute();
$quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
          <a href="dashboard.php" class="text-white hover:text-gray-200">
            <i class="fas fa-arrow-left mr-2"></i>Kembali
          </a>
          
        </div>
        <div class="flex items-center space-x-4">
          <span><?php echo htmlspecialchars($user['full_name']); ?></span>
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
    <div class="mb-8">
      <h2 class="text-3xl font-bold text-gray-800 mb-2">Daftar Kuis</h2>
      <p class="text-gray-600">Daftar semua kuis</p>
    </div>

    <!-- Available Quizzes -->
    <div class="mb-8">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-gray-800">
          <i class="fas fa-question-circle mr-2 text-green-600"></i>Kuis Tersedia
        </h3>
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

  </div>
</body>

</html>