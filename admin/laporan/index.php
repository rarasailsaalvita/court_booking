<?php
// admin/laporan/index.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$start_date = $_GET['start_date'] ?? date('Y-m-01');
$end_date   = $_GET['end_date'] ?? date('Y-m-t');

$stmt = $pdo->prepare("
    SELECT r.*, u.nama AS pelanggan_nama, l.nama AS lapangan_nama
    FROM reservasi r
    JOIN users u ON r.user_id = u.id
    JOIN lapangan l ON r.lapangan_id = l.id
    WHERE r.tanggal BETWEEN ? AND ? AND r.status = 'lunas'
    ORDER BY r.tanggal ASC
");
$stmt->execute([$start_date, $end_date]);
$laporan = $stmt->fetchAll();

$total_pendapatan = array_reduce($laporan, function($acc, $curr) {
    return $acc + $curr['total_bayar'];
}, 0);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 font-sans min-h-screen flex">

    <?php include '../../includes/navbar_admin.php'; ?>

    <main class="flex-1 p-8 space-y-6 overflow-y-auto">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-white">Laporan Pendapatan SM Sport Center</h1>
                <p class="text-xs text-slate-400 mt-1">Laporan rekapitulasi transaksi reservasi yang berstatus LUNAS.</p>
            </div>
            <button onclick="window.print()" class="px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white font-bold text-xs rounded-xl shadow transition print:hidden">
                🖨️ Cetak / Print Laporan
            </button>
        </div>

        <!-- Filter Form -->
        <form method="GET" class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm flex flex-wrap items-end gap-4 print:hidden">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Dari Tanggal</label>
                <input type="date" name="start_date" value="<?= htmlspecialchars($start_date) ?>" class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-xs text-slate-100">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Sampai Tanggal</label>
                <input type="date" name="end_date" value="<?= htmlspecialchars($end_date) ?>" class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-xs text-slate-100">
            </div>

            <button type="submit" class="px-5 py-2 bg-green-700 hover:bg-green-800 text-white font-bold text-xs rounded-xl shadow">
                Filter Laporan
            </button>
        </form>

        <!-- Summary Banner -->
        <div class="bg-gradient-to-r from-green-800 to-green-900 text-white p-6 rounded-2xl shadow flex items-center justify-between">
            <div>
                <p class="text-xs font-bold text-green-200 uppercase">Periode Laporan</p>
                <p class="text-lg font-bold"><?= date('d M Y', strtotime($start_date)) ?> — <?= date('d M Y', strtotime($end_date)) ?></p>
            </div>
            <div class="text-right">
                <p class="text-xs font-bold text-green-200 uppercase">Total Pendapatan Bersih</p>
                <p class="text-3xl font-black text-orange-400">Rp <?= number_format($total_pendapatan, 0, ',', '.') ?></p>
            </div>
        </div>

        <!-- Table -->
        <div class="bg-slate-900 rounded-2xl border border-slate-700 shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-950 border-b border-slate-700 text-slate-400 text-xs font-bold uppercase">
                        <th class="p-4">No</th>
                        <th class="p-4">Tanggal Main</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Lapangan</th>
                        <th class="p-4">Durasi Slot</th>
                        <th class="p-4 text-right">Total Bayar</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php if (count($laporan) === 0): ?>
                        <tr>
                            <td colspan="6" class="p-6 text-center text-slate-400 text-xs italic">Tidak ada transaksi lunas pada rentang tanggal ini.</td>
                        </tr>
                    <?php else: ?>
                        <?php $no = 1; foreach ($laporan as $row): ?>
                            <tr class="hover:bg-slate-800 transition">
                                <td class="p-4 text-xs font-bold text-slate-400"><?= $no++ ?></td>
                                <td class="p-4 font-bold text-slate-100"><?= $row['tanggal'] ?></td>
                                <td class="p-4 font-semibold text-slate-100"><?= htmlspecialchars($row['pelanggan_nama']) ?></td>
                                <td class="p-4 text-slate-300"><?= htmlspecialchars($row['lapangan_nama']) ?></td>
                                <td class="p-4 text-xs text-slate-400 font-mono"><?= substr($row['jam_mulai'], 0, 5) ?> - <?= substr($row['jam_selesai'], 0, 5) ?> (<?= $row['durasi'] ?> Jam)</td>
                                <td class="p-4 text-right font-black text-slate-100">Rp <?= number_format($row['total_bayar'], 0, ',', '.') ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
