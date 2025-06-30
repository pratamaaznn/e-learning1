<?php
session_start();

// Include database configuration
require_once 'database.php';

// Create database connection
$database = new Database();
$db = $database->getConnection();

// Utility functions
function isLoggedIn()
{
    return isset($_SESSION['user_id']);
}

function getUser()
{
    global $db;
    if (!isLoggedIn()) return null;

    $stmt = $db->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function requireLogin()
{
    if (!isLoggedIn()) {
        header('Location: login.php');
        exit();
    }
}

function requireRole($role)
{
    requireLogin();
    $user = getUser();
    if ($user['role'] !== $role) {
        header('Location: index.php');
        exit();
    }
}
