<?php
// admin/dashboard.php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

// Stats queries
$totalLapangan = $pdo->query("SELECT COUNT(*) FROM lapangan")->fetchColumn();
$totalPelanggan = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'pelanggan'")->fetchColumn();
$totalReservasi = $pdo->query("SELECT COUNT(*) FROM reservasi")->fetchColumn();
$totalPendapatan = $pdo->query("SELECT COALESCE(SUM(total_bayar), 0) FROM reservasi WHERE status = 'lunas'")->fetchColumn();

// Latest reservations
$stmt = $pdo->query("
    SELECT r.*, u.nama AS pelanggan_nama, l.nama AS lapangan_nama
    FROM reservasi r
    JOIN users u ON r.user_id = u.id
    JOIN lapangan l ON r.lapangan_id = l.id
    ORDER BY r.id DESC LIMIT 5
");
$latestReservasi = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 font-sans min-h-screen flex">

    <?php include '../includes/navbar_admin.php'; ?>

    <main class="flex-1 p-8 space-y-8 overflow-y-auto">
        <!-- Header Banner -->
        <div class="bg-gradient-to-r from-green-800 via-green-700 to-slate-950 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-4 border border-green-800">
            <div>
                <span class="px-3 py-1 bg-orange-500 text-white rounded-full text-xs font-bold uppercase tracking-wider">Super Admin</span>
                <h1 class="text-3xl font-black mt-2">Ringkasan Dashboard Utama</h1>
                <p class="text-green-100 text-xs sm:text-sm mt-1">Selamat datang kembali, <?= htmlspecialchars($_SESSION['nama']) ?>. Pantau statistik operasional SM Sport Center.</p>
            </div>
            <div class="flex items-center gap-3">
                <a href="pembayaran/verifikasi.php" class="px-5 py-3 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl text-xs shadow-lg transition">
                    Verifikasi Pembayaran QRIS
                </a>
                <a href="laporan/index.php" class="px-5 py-3 bg-slate-800 hover:bg-slate-700 text-white font-bold rounded-xl text-xs border border-slate-700 transition">
                    Laporan Keuangan
                </a>
            </div>
        </div>

        <!-- Metric Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm space-y-2">
                <p class="text-xs font-bold text-slate-400 uppercase">Total Lapangan</p>
                <p class="text-3xl font-black text-slate-100"><?= $totalLapangan ?></p>
                <p class="text-xs text-green-300 font-bold">Futsal & Badminton</p>
            </div>

            <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm space-y-2">
                <p class="text-xs font-bold text-slate-400 uppercase">Pelanggan Terdaftar</p>
                <p class="text-3xl font-black text-slate-100"><?= $totalPelanggan ?></p>
                <p class="text-xs text-blue-300 font-bold">Akun Terverifikasi</p>
            </div>

            <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm space-y-2">
                <p class="text-xs font-bold text-slate-400 uppercase">Total Reservasi</p>
                <p class="text-3xl font-black text-slate-100"><?= $totalReservasi ?></p>
                <p class="text-xs text-orange-300 font-bold">Seluruh Waktu</p>
            </div>

            <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm space-y-2">
                <p class="text-xs font-bold text-slate-400 uppercase">Total Pendapatan</p>
                <p class="text-2xl font-black text-slate-100">Rp <?= number_format($totalPendapatan, 0, ',', '.') ?></p>
                <p class="text-xs text-green-300 font-bold">↑ Status LUNAS</p>
            </div>
        </div>

        <!-- Latest Table -->
        <div class="bg-slate-900 rounded-2xl border border-slate-700 shadow-sm p-6 space-y-4">
            <div class="flex items-center justify-between border-b border-slate-700 pb-4">
                <h2 class="text-lg font-bold text-slate-100">Reservasi Terbaru</h2>
                <a href="reservasi/index.php" class="text-xs font-bold text-green-300 hover:text-white hover:underline">Lihat Semua →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-950 text-slate-400 text-xs font-bold uppercase border-b border-slate-700">
                            <th class="p-3.5">ID</th>
                            <th class="p-3.5">Pelanggan</th>
                            <th class="p-3.5">Lapangan</th>
                            <th class="p-3.5">Jadwal Slot</th>
                            <th class="p-3.5">Total Bayar</th>
                            <th class="p-3.5">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800">
                        <?php foreach ($latestReservasi as $r): ?>
                            <tr class="hover:bg-slate-800 transition">
                                <td class="p-3.5 text-xs text-slate-400 font-semibold">#<?= $r['id'] ?></td>
                                <td class="p-3.5 font-bold text-slate-100"><?= htmlspecialchars($r['pelanggan_nama']) ?></td>
                                <td class="p-3.5 text-slate-300"><?= htmlspecialchars($r['lapangan_nama']) ?></td>
                                <td class="p-3.5 text-slate-400 text-xs"><?= $r['tanggal'] ?> (<?= substr($r['jam_mulai'], 0, 5) ?> - <?= substr($r['jam_selesai'], 0, 5) ?>)</td>
                                <td class="p-3.5 font-black text-slate-100">Rp <?= number_format($r['total_bayar'], 0, ',', '.') ?></td>
                                <td class="p-3.5 text-xs">
                                    <span class="px-2.5 py-1 rounded-full font-bold uppercase <?= $r['status'] === 'lunas' ? 'bg-emerald-800 text-emerald-100' : ($r['status'] === 'dibatalkan' ? 'bg-rose-800 text-rose-100' : 'bg-orange-800 text-orange-100') ?>">
                                        <?= strtoupper($r['status']) ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

</body>
</html>
