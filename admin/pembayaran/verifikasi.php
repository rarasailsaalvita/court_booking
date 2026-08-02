<?php
// admin/pembayaran/verifikasi.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reservasi_id = intval($_POST['reservasi_id'] ?? 0);
    $new_status   = trim($_POST['status'] ?? '');

    if ($reservasi_id > 0 && in_array($new_status, ['lunas', 'dibatalkan'])) {
        $stmt = $pdo->prepare("UPDATE reservasi SET status = ? WHERE id = ?");
        if ($stmt->execute([$new_status, $reservasi_id])) {
            $message = "Status reservasi #{$reservasi_id} berhasil diubah menjadi " . strtoupper($new_status);
        }
    }
}

$stmt = $pdo->query("
    SELECT r.*, u.nama AS pelanggan_nama, l.nama AS lapangan_nama
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
    <title>Verifikasi QRIS - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 font-sans min-h-screen flex">

    <?php include '../../includes/navbar_admin.php'; ?>

    <main class="flex-1 p-8 space-y-6 overflow-y-auto">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm">
            <h1 class="text-2xl font-black text-white">Verifikasi Pembayaran QRIS Pelanggan</h1>
            <p class="text-xs text-slate-400 mt-1">Periksa bukti transfer dan ubah status transaksi menjadi LUNAS atau DIBATALKAN.</p>
        </div>

        <?php if ($message): ?>
            <div class="p-3 bg-emerald-900 border border-emerald-700 text-emerald-100 text-xs font-bold rounded-xl">
                <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <div class="bg-slate-900 rounded-2xl border border-slate-700 shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-950 border-b border-slate-700 text-slate-400 text-xs font-bold uppercase">
                        <th class="p-4">ID</th>
                        <th class="p-4">Pelanggan</th>
                        <th class="p-4">Lapangan</th>
                        <th class="p-4">Total Bayar</th>
                        <th class="p-4">Bukti QRIS</th>
                        <th class="p-4">Status Sekarang</th>
                        <th class="p-4 text-right">Aksi Verifikasi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($reservasis as $r): ?>
                        <tr class="hover:bg-slate-800 transition">
                            <td class="p-4 text-xs font-bold text-slate-400">#<?= $r['id'] ?></td>
                            <td class="p-4 font-bold text-slate-100"><?= htmlspecialchars($r['pelanggan_nama']) ?></td>
                            <td class="p-4 text-slate-300 font-semibold"><?= htmlspecialchars($r['lapangan_nama']) ?></td>
                            <td class="p-4 font-black text-slate-100">Rp <?= number_format($r['total_bayar'], 0, ',', '.') ?></td>
                            <td class="p-4 text-xs">
                                <?php if ($r['bukti_bayar']): ?>
                                    <span class="px-2.5 py-1 bg-slate-800 text-slate-100 font-bold rounded-lg border border-slate-700">Foto Ter-upload</span>
                                <?php else: ?>
                                    <span class="text-slate-400 italic">Belum upload</span>
                                <?php endif; ?>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase <?= $r['status'] === 'lunas' ? 'bg-emerald-800 text-emerald-100' : ($r['status'] === 'dibatalkan' ? 'bg-rose-800 text-rose-100' : 'bg-orange-800 text-orange-100') ?>">
                                    <?= strtoupper($r['status']) ?>
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-1">
                                <form method="POST" class="inline-block">
                                    <input type="hidden" name="reservasi_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="status" value="lunas">
                                    <button type="submit" <?= $r['status'] === 'lunas' ? 'disabled' : '' ?> class="px-3 py-1.5 bg-green-700 hover:bg-green-800 disabled:opacity-50 text-white rounded-lg text-xs font-bold transition">
                                        Konfirmasi Lunas
                                    </button>
                                </form>

                                <form method="POST" class="inline-block">
                                    <input type="hidden" name="reservasi_id" value="<?= $r['id'] ?>">
                                    <input type="hidden" name="status" value="dibatalkan">
                                    <button type="submit" <?= $r['status'] === 'dibatalkan' ? 'disabled' : '' ?> class="px-3 py-1.5 bg-red-600 hover:bg-red-700 disabled:opacity-50 text-white rounded-lg text-xs font-bold transition">
                                        Tolak
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
