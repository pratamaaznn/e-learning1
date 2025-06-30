<?php
require_once '../config/init.php';

if ($_POST) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $db->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        if ($_SESSION['login_error']) {
            unset($_SESSION['user_id']);
        }

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['full_name'] = $user['full_name'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'teacher') {
            header('Location: ../teacher/dashboard.php');
        } else {
            header('Location: ../student/dashboard.php');
        }

        exit();
    } else {
        $_SESSION['login_error'] = 'Username atau password salah';
        header('Location: ../index.php');
        exit();
    }
}

header('Location: ../index.php');
exit();
