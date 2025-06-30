<?php
require_once 'config/init.php';

if (isLoggedIn()) {
    $user = getUser();
    if ($user['role'] === 'teacher') {
        header('Location: teacher/dashboard.php');
    } else {
        header('Location: student/dashboard.php');
    }
    exit();
}

?>
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Learning School</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
</head>

<body class="bg-gradient-to-br from-blue-50 to-indigo-100 min-h-screen">
    <div class="container mx-auto px-4 py-8">
        <div class="text-center mb-12">
            <div class="flex justify-center mb-6">
                <img src="logo/elearning.png" alt="Logo E-Learning" class="w-56 h-auto">
            </div>
            <h1 class="text-2xl text-blue-900 font-semibold mb-8">Learning School</h1>
        </div>

        <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg overflow-hidden">
            <div class="px-6 py-8">
                <h2 class="text-2xl font-bold text-center text-gray-800 mb-8">Masuk Akun Anda</h2>
                <?php
                if (!empty($_SESSION['login_error'])) {
                    echo '<p class="text-center text-red-800">' . htmlspecialchars($_SESSION['login_error']) . '</p>';
                    unset($_SESSION['login_error']);
                }
                ?>

                <form action="auth/login.php" method="POST" class="space-y-6">
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">Nama Lengkap </label>
                        <input type="text" id="username" name="username" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Kata Sandi</label>
                        <input type="password" id="password" name="password" required
                            class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700 transition duration-200 font-medium">
                        Masuk
                    </button>
                </form>
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">Belum punya akun?
                        <a href="register.php" class="text-blue-600 hover:underline font-medium">Daftar di sini</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
    </div>
</body>

</html>