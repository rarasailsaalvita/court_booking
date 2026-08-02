<?php
// pelanggan/reservasi/index.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: ../../auth/login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT r.*, l.nama AS lapangan_nama, l.jenis AS lapangan_jenis, l.harga_per_jam
    FROM reservasi r
    JOIN lapangan l ON r.lapangan_id = l.id
    WHERE r.user_id = ?
    ORDER BY r.id DESC
");
$stmt->execute([$user_id]);
$reservasis = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Reservasi Saya - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen flex flex-col">

    <?php include '../../includes/navbar_pelanggan.php'; ?>

    <main class="max-w-7xl mx-auto w-full px-4 py-8 space-y-6 flex-1">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-xl flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-white">Riwayat Reservasi Lapangan Saya</h1>
                <p class="text-xs text-emerald-200 mt-1">Daftar transaksi sewa lapangan yang telah Anda ajukan.</p>
            </div>
            <a href="create.php" class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow transition">
                + Buat Reservasi Baru
            </a>
        </div>

        <div class="bg-slate-900 rounded-2xl border border-slate-700 shadow-xl overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-950 border-b border-slate-700 text-emerald-200 text-xs font-bold uppercase">
                        <th class="p-4">Kode ID</th>
                        <th class="p-4">Lapangan</th>
                        <th class="p-4">Tanggal Main</th>
                        <th class="p-4">Jam Slot</th>
                        <th class="p-4">Total Bayar</th>
                        <th class="p-4">Status Pembayaran</th>
                        <th class="p-4 text-right">Opsi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (count($reservasis) === 0): ?>
                        <tr>
                            <td colspan="7" class="p-8 text-center text-slate-400 text-xs italic">Anda belum memiliki riwayat reservasi lapangan.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($reservasis as $r): ?>
                            <tr class="hover:bg-slate-950 transition">
                                <td class="p-4 text-xs font-bold text-emerald-300">#<?= $r['id'] ?></td>
                                <td class="p-4 font-bold text-white"><?= htmlspecialchars($r['lapangan_nama']) ?></td>
                                <td class="p-4 font-semibold text-emerald-200"><?= $r['tanggal'] ?></td>
                                <td class="p-4 text-xs text-slate-300 font-mono"><?= substr($r['jam_mulai'], 0, 5) ?> - <?= substr($r['jam_selesai'], 0, 5) ?> (<?= $r['durasi'] ?> jam)</td>
                                <td class="p-4 font-black text-emerald-200">Rp <?= number_format($r['total_bayar'], 0, ',', '.') ?></td>
                                <td class="p-4">
                                    <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase <?= $r['status'] === 'lunas' ? 'bg-emerald-800 text-emerald-100' : ($r['status'] === 'dibatalkan' ? 'bg-rose-800 text-rose-100' : 'bg-orange-800 text-orange-100') ?>">
                                        <?= strtoupper($r['status']) ?>
                                    </span>
                                </td>
                                <td class="p-4 text-right">
                                    <?php if ($r['status'] === 'menunggu'): ?>
                                        <a href="upload_bukti.php?id=<?= $r['id'] ?>" class="px-3.5 py-1.5 bg-orange-500 hover:bg-orange-600 text-white rounded-lg text-xs font-bold shadow transition">
                                            Upload / Bayar QRIS
                                        </a>
                                    <?php else: ?>
                                        <span class="text-xs text-slate-400 font-bold">Lunas</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>

</body>
</html>
