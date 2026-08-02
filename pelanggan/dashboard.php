<?php
// pelanggan/dashboard.php
session_start();
require_once '../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: ../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Get customer's recent bookings
$stmt = $pdo->prepare("SELECT r.*, l.nama AS lapangan_nama, l.jenis AS lapangan_jenis FROM reservasi r JOIN lapangan l ON r.lapangan_id = l.id WHERE r.user_id = ? ORDER BY r.id DESC LIMIT 3");
$stmt->execute([$user_id]);
$myReservations = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Pelanggan - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen flex flex-col">

    <?php include '../includes/navbar_pelanggan.php'; ?>

    <main class="max-w-7xl mx-auto w-full px-4 py-8 space-y-8 flex-1">
        <!-- Welcome Hero -->
        <div class="bg-gradient-to-r from-slate-900 via-green-950 to-slate-900 text-white p-8 rounded-3xl shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6 border border-green-900">
            <div class="space-y-2">
                <span class="px-3 py-1 bg-orange-500 text-white rounded-full text-xs font-bold uppercase tracking-wider">Selamat Datang</span>
                <h1 class="text-3xl font-black">Halo, <?= htmlspecialchars($_SESSION['nama']) ?>! 👋</h1>
                <p class="text-slate-300 text-xs sm:text-sm">Ini adalah dashboard reservasi Anda. Lihat status booking dan lanjutkan ke halaman utama untuk memesan lapangan.</p>
            </div>
            <a href="../index.php" class="px-6 py-3.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-lg transition text-xs shrink-0 text-center">
                Buka Halaman Utama Reservasi
            </a>
        </div>

        <!-- My Recent Reservations -->
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-xl space-y-4">
            <div class="flex items-center justify-between border-b border-slate-700 pb-3">
                <h3 class="font-bold text-white text-base">Reservasi Terbaru Saya</h3>
                <a href="reservasi/index.php" class="text-xs font-bold text-emerald-300 hover:text-white">Lihat Semua History →</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead>
                        <tr class="bg-slate-950 border-b border-slate-700 text-emerald-200 text-xs font-bold uppercase">
                            <th class="p-3.5">ID</th>
                            <th class="p-3.5">Lapangan</th>
                            <th class="p-3.5">Jadwal Slot</th>
                            <th class="p-3.5">Total Bayar</th>
                            <th class="p-3.5">Status</th>
                            <th class="p-3.5 text-right">Aksi Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700">
                        <?php if (count($myReservations) === 0): ?>
                            <tr>
                                <td colspan="6" class="p-6 text-center text-slate-400 text-xs italic">Belum ada reservasi aktif. Silakan lakukan pemesanan lapangan!</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($myReservations as $r): ?>
                                <tr class="hover:bg-slate-900 transition">
                                    <td class="p-3.5 text-xs font-bold text-emerald-300">#<?= $r['id'] ?></td>
                                    <td class="p-3.5 font-bold text-white"><?= htmlspecialchars($r['lapangan_nama']) ?></td>
                                    <td class="p-3.5 text-xs text-slate-300 font-semibold"><?= $r['tanggal'] ?> (<?= substr($r['jam_mulai'], 0, 5) ?> - <?= substr($r['jam_selesai'], 0, 5) ?>)</td>
                                    <td class="p-3.5 font-black text-emerald-200">Rp <?= number_format($r['total_bayar'], 0, ',', '.') ?></td>
                                    <td class="p-3.5">
                                        <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase <?= $r['status'] === 'lunas' ? 'bg-emerald-800 text-emerald-100' : ($r['status'] === 'dibatalkan' ? 'bg-rose-800 text-rose-100' : 'bg-orange-800 text-orange-100') ?>">
                                            <?= strtoupper($r['status']) ?>
                                        </span>
                                    </td>
                                    <td class="p-3.5 text-right">
                                        <?php if ($r['status'] === 'menunggu'): ?>
                                            <a href="reservasi/upload_bukti.php?id=<?= $r['id'] ?>" class="px-3 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-xs font-bold shadow transition">
                                                Bayar QRIS
                                            </a>
                                        <?php else: ?>
                                            <span class="text-xs text-slate-400 font-semibold">Tuntas</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </main>

    <?php include '../includes/footer.php'; ?>

</body>
</html>
