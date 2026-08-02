<?php
// admin/pelanggan/delete.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    $stmt = $pdo->prepare("DELETE FROM users WHERE id = ? AND role = 'pelanggan'");
    $stmt->execute([$id]);
}

header("Location: index.php");
exit;
?>
