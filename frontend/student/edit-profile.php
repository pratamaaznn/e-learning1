<?php
require_once '../config/init.php';
requireRole('student');

$user = getUser();

// Ambil data user dari database
$stmt = $db->prepare("SELECT full_name, nis, ttl, phone, gender FROM users WHERE id = ?");
$stmt->execute([$user['id']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name']);
    $nis = trim($_POST['nis']);
    $ttl = trim($_POST['ttl']);
    $phone = trim($_POST['phone']);
    $gender = $_POST['gender'];

    // Update semua data termasuk NIS
    $stmt = $db->prepare("UPDATE users SET full_name = ?, nis = ?, ttl = ?, phone = ?, gender = ? WHERE id = ?");
    $stmt->execute([$full_name, $nis, $ttl, $phone, $gender, $user['id']]);

    $_SESSION['success'] = "Profil berhasil diperbarui.";
    header('Location: dashboard.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <title>Profil Siswa</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-[#dce9f8] min-h-screen">
    <div class="flex flex-col items-center py-10">

        <!-- Foto Profil -->
        <div class="w-32 h-32 rounded-full overflow-hidden shadow-lg border-4 border-white bg-gray-200">
            <img src="../logo/guru.jpg" alt="Profile" class="w-full h-full object-cover">
        </div>

        <!-- Hello -->
        <h1 class="mt-4 text-xl italic">Hallo <?= htmlspecialchars($profile['full_name']) ?></h1>

        <!-- Garis -->
        <div class="w-3/4 mt-2 border-t border-gray-600"></div>

        <!-- Form Profil -->
        <div class="bg-white shadow-lg rounded-lg mt-6 p-8 w-[90%] max-w-xl">
            <h2 class="text-center text-lg font-semibold mb-6 border-b pb-2">Data Diri / Biodata</h2>

            <?php if (!empty($_SESSION['success'])): ?>
                <div class="bg-green-100 text-green-800 px-4 py-3 rounded mb-4 text-center">
                    <?= $_SESSION['success'];
                    unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>

            <form action="" method="POST" class="space-y-4">
                <!-- Nama -->
                <div class="flex items-center">
                    <label class="w-32 text-gray-700">Nama :</label>
                    <input type="text" name="full_name" value="<?= htmlspecialchars($profile['full_name']) ?>"
                        class="w-full bg-gray-100 px-4 py-2 rounded focus:outline-none" required>
                </div>

                <!-- NIS -->
                <div class="flex items-center">
                    <label class="w-32 text-gray-700">NIS :</label>
                    <input type="text" name="nis" value="<?= htmlspecialchars($profile['nis']) ?>"
                        class="w-full bg-gray-100 px-4 py-2 rounded focus:outline-none">
                </div>

                <!-- TTL -->
                <div class="flex items-center">
                    <label class="w-32 text-gray-700">TTL :</label>
                    <input type="text" name="ttl" value="<?= htmlspecialchars($profile['ttl']) ?>"
                        class="w-full bg-gray-100 px-4 py-2 rounded focus:outline-none">
                </div>

                <!-- No HP -->
                <div class="flex items-center">
                    <label class="w-32 text-gray-700">No HP :</label>
                    <input type="text" name="phone" value="<?= htmlspecialchars($profile['phone']) ?>"
                        class="w-full bg-gray-100 px-4 py-2 rounded focus:outline-none">
                </div>

                <!-- Jenis Kelamin -->
                <div class="flex items-center">
                    <label class="w-32 text-gray-700">Jenis Kelamin :</label>
                    <div class="flex gap-6">
                        <label class="inline-flex items-center">
                            <input type="radio" name="gender" value="L" <?= $profile['gender'] === 'L' ? 'checked' : '' ?>
                                class="form-radio text-blue-600 w-5 h-5">
                            <span class="ml-2">Laki Laki</span>
                        </label>
                        <label class="inline-flex items-center">
                            <input type="radio" name="gender" value="P" <?= $profile['gender'] === 'P' ? 'checked' : '' ?>
                                class="form-radio text-blue-600 w-5 h-5">
                            <span class="ml-2">Perempuan</span>
                        </label>
                    </div>
                </div>

                <!-- Tombol Simpan -->
                <div class="text-center mt-6">
                    <button type="submit"
                        class="bg-blue-700 text-white px-10 py-2 rounded-md text-lg hover:bg-blue-800 transition">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>

</html>