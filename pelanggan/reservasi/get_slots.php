<?php
// pelanggan/reservasi/get_slots.php
header('Content-Type: application/json');
require_once '../../config/database.php';

$lapangan_id = intval($_GET['lapangan_id'] ?? 0);
$tanggal     = trim($_GET['tanggal'] ?? '');

if (!$lapangan_id || !$tanggal) {
    echo json_encode([]);
    exit;
}

$stmt = $pdo->prepare("
    SELECT id, jam_mulai, jam_selesai, status
    FROM reservasi
    WHERE lapangan_id = ?
      AND tanggal = ?
      AND status != 'dibatalkan'
");
$stmt->execute([$lapangan_id, $tanggal]);
$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode($reservations);
