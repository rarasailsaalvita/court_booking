<?php
// admin/reservasi/detail.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$stmt = $pdo->prepare("
    SELECT r.*, u.nama AS pelanggan_nama, u.email AS pelanggan_email, u.no_hp AS pelanggan_hp, l.nama AS lapangan_nama, l.jenis AS lapangan_jenis, l.harga_per_jam
    FROM reservasi r
    JOIN users u ON r.user_id = u.id
    JOIN lapangan l ON r.lapangan_id = l.id
    WHERE r.id = ?
");
$stmt->execute([$id]);
$r = $stmt->fetch();

if (!$r) {
    header("Location: index.php");
    exit;
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Reservasi #<?= $id ?> - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 font-sans min-h-screen flex">

    <?php include '../../includes/navbar_admin.php'; ?>

    <main class="flex-1 p-8 space-y-6 overflow-y-auto max-w-3xl">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-white">Detail Reservasi #<?= $r['id'] ?></h1>
                <p class="text-xs text-slate-400 mt-1">Dibuat tanggal: <?= date('d M Y H:i', strtotime($r['created_at'])) ?></p>
            </div>
            <a href="index.php" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-100 font-bold rounded-xl text-xs transition">
                ← Kembali
            </a>
        </div>

        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm space-y-6">
            <div class="grid grid-cols-2 gap-4 text-xs">
                <div class="p-4 bg-slate-950 rounded-xl space-y-1 border border-slate-700">
                    <p class="font-bold text-slate-400 uppercase">Informasi Pelanggan</p>
                    <p class="text-base font-extrabold text-slate-100"><?= htmlspecialchars($r['pelanggan_nama']) ?></p>
                    <p class="text-slate-300"><?= htmlspecialchars($r['pelanggan_email']) ?></p>
                    <p class="text-slate-300 font-mono">No. HP: <?= htmlspecialchars($r['pelanggan_hp'] ?? '-') ?></p>
                </div>

                <div class="p-4 bg-slate-950 rounded-xl space-y-1 border border-slate-700">
                    <p class="font-bold text-slate-400 uppercase">Informasi Lapangan</p>
                    <p class="text-base font-extrabold text-slate-100"><?= htmlspecialchars($r['lapangan_nama']) ?></p>
                    <p class="text-slate-300 uppercase font-bold text-[10px]"><?= htmlspecialchars($r['lapangan_jenis']) ?></p>
                    <p class="text-slate-300">Rp <?= number_format($r['harga_per_jam'], 0, ',', '.') ?> / jam</p>
                </div>
            </div>

            <div class="border-t border-slate-700 pt-4 space-y-3">
                <h3 class="font-bold text-slate-100 text-sm">Rincian Slot Waktu Booking</h3>
                <div class="p-4 bg-slate-900 text-white rounded-xl flex items-center justify-between text-xs border border-slate-700">
                    <div>
                        <p class="text-slate-400">Tanggal Main:</p>
                        <p class="text-lg font-bold text-green-400"><?= $r['tanggal'] ?></p>
                    </div>
                    <div class="text-right">
                        <p class="text-slate-400">Jam Slot:</p>
                        <p class="text-lg font-bold text-orange-400"><?= substr($r['jam_mulai'], 0, 5) ?> - <?= substr($r['jam_selesai'], 0, 5) ?> (<?= $r['durasi'] ?> Jam)</p>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-700 pt-4 flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 font-bold uppercase">Status Pembayaran</p>
                    <span class="inline-block mt-1 px-3 py-1 rounded-full text-xs font-bold uppercase <?= $r['status'] === 'lunas' ? 'bg-emerald-800 text-emerald-100' : ($r['status'] === 'dibatalkan' ? 'bg-rose-800 text-rose-100' : 'bg-orange-800 text-orange-100') ?>">
                        <?= strtoupper($r['status']) ?>
                    </span>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400 font-bold uppercase">Total Tagihan</p>
                    <p class="text-2xl font-black text-slate-100">Rp <?= number_format($r['total_bayar'], 0, ',', '.') ?></p>
                </div>
            </div>

            <?php if ($r['catatan']): ?>
                <div class="p-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl text-xs">
                    <strong>Catatan Pelanggan:</strong> <?= htmlspecialchars($r['catatan']) ?>
                </div>
            <?php endif; ?>
        </div>
    </main>

</body>
</html>
