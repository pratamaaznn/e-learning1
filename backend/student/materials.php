<?php
require_once '../config/init.php';
requireRole('student');


$user = getUser();

// Get available materials
$stmt = $db->prepare("SELECT m.*, u.full_name as teacher_name FROM materials m JOIN users u ON m.teacher_id = u.id ORDER BY m.created_at DESC");
$stmt->execute();
$materials = $stmt->fetchAll(PDO::FETCH_ASSOC);
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
      <h2 class="text-3xl font-bold text-gray-800 mb-2">Daftar Materi</h2>
      <p class="text-gray-600">Daftar semua materi</p>
    </div>

    <!-- Recent Materials -->
    <div class="mb-8">
      <div class="flex justify-between items-center mb-6">
        <h3 class="text-2xl font-bold text-gray-800">
          <i class="fas fa-book mr-2 text-blue-600"></i>Materi
        </h3>
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
  </div>
</body>

</html>