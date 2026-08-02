<?php
// admin/reservasi/index.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$stmt = $pdo->query("
    SELECT r.*, u.nama AS pelanggan_nama, u.email AS pelanggan_email, l.nama AS lapangan_nama
    FROM reservasi r
    JOIN users u ON r.user_id = u.id
    JOIN lapangan l ON r.lapangan_id = l.id
    ORDER BY r.id DESC
");
$reservasis = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Reservasi - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 font-sans min-h-screen flex">

    <?php include '../../includes/navbar_admin.php'; ?>

    <main class="flex-1 p-8 space-y-6 overflow-y-auto">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-white">Kelola Seluruh Reservasi</h1>
                <p class="text-xs text-slate-400 mt-1">Daftar transaksi booking pelanggan di SM Sport Center.</p>
            </div>
        </div>

        <div class="bg-slate-900 rounded-2xl border border-slate-700 shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-950 border-b border-slate-700 text-slate-400 text-xs font-bold uppercase">
                        <th class="p-4">ID</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Lapangan</th>
                        <th class="p-4">Tanggal & Jam Slot</th>
                        <th class="p-4">Durasi</th>
                        <th class="p-4">Total Bayar</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($reservasis as $r): ?>
                        <tr class="hover:bg-slate-800 transition">
                            <td class="p-4 text-xs font-bold text-slate-400">#<?= $r['id'] ?></td>
                            <td class="p-4">
                                <p class="font-bold text-slate-100"><?= htmlspecialchars($r['pelanggan_nama']) ?></p>
                                <p class="text-[11px] text-slate-400"><?= htmlspecialchars($r['pelanggan_email']) ?></p>
                            </td>
                            <td class="p-4 text-slate-300 font-semibold"><?= htmlspecialchars($r['lapangan_nama']) ?></td>
                            <td class="p-4 text-xs text-slate-400 font-medium">
                                <?= $r['tanggal'] ?><br>
                                <span class="font-bold text-slate-100"><?= substr($r['jam_mulai'], 0, 5) ?> - <?= substr($r['jam_selesai'], 0, 5) ?></span>
                            </td>
                            <td class="p-4 font-bold text-slate-100"><?= $r['durasi'] ?> Jam</td>
                            <td class="p-4 font-black text-slate-100">Rp <?= number_format($r['total_bayar'], 0, ',', '.') ?></td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase <?= $r['status'] === 'lunas' ? 'bg-emerald-800 text-emerald-100' : ($r['status'] === 'dibatalkan' ? 'bg-rose-800 text-rose-100' : 'bg-orange-800 text-orange-100') ?>">
                                    <?= strtoupper($r['status']) ?>
                                </span>
                            </td>
                            <td class="p-4 text-right">
                                <a href="detail.php?id=<?= $r['id'] ?>" class="px-3 py-1.5 bg-slate-900 text-white hover:bg-green-700 rounded-lg text-xs font-bold transition">
                                    Detail Rincian
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
