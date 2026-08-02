<?php
// pelanggan/reservasi/upload_bukti.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: ../../auth/login.php");
    exit;
}

$id = intval($_GET['id'] ?? 0);
$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT r.*, l.nama AS lapangan_nama
    FROM reservasi r
    JOIN lapangan l ON r.lapangan_id = l.id
    WHERE r.id = ? AND r.user_id = ?
");
$stmt->execute([$id, $user_id]);
$r = $stmt->fetch();

if (!$r) {
    header("Location: index.php");
    exit;
}

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Simulate payment upload update
    $filename = "qris_bukti_" . time() . ".jpg";
    $stmt = $pdo->prepare("UPDATE reservasi SET bukti_bayar = ?, status = 'lunas' WHERE id = ?");
    if ($stmt->execute([$filename, $id])) {
        $message = 'Pembayaran QRIS berhasil dikonfirmasi secara instant!';
        $r['status'] = 'lunas';
        $r['bukti_bayar'] = $filename;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran QRIS #<?= $id ?> - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen flex flex-col">

    <?php include '../../includes/navbar_pelanggan.php'; ?>

    <main class="max-w-2xl mx-auto w-full px-4 py-8 space-y-6 flex-1">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-xl text-center space-y-2">
            <span class="px-3 py-1 bg-emerald-900 text-emerald-100 rounded-full text-xs font-bold uppercase">Pembayaran Instan QRIS</span>
            <h1 class="text-2xl font-black text-white">Scan QRIS SM Sport Center</h1>
            <p class="text-xs text-emerald-200">Reservasi Kode: <strong>#<?= $r['id'] ?></strong> | Total: <strong class="text-emerald-100 text-sm">Rp <?= number_format($r['total_bayar'], 0, ',', '.') ?></strong></p>
        </div>

        <?php if ($message): ?>
            <div class="p-4 bg-emerald-900 border border-emerald-700 text-emerald-100 text-xs font-bold rounded-xl text-center">
                ✅ <?= htmlspecialchars($message) ?>
            </div>
        <?php endif; ?>

        <!-- QRIS Card -->
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-xl space-y-6 text-center">
            <?php $qrisImage = '../../assets/qris.jpeg'; ?>
            <?php if (file_exists(__DIR__ . '/../../assets/qris.jpeg')): ?>
                <img src="<?= $qrisImage ?>" alt="QRIS SM Sport Center" class="mx-auto w-72 max-w-full rounded-3xl shadow-lg">
            <?php else: ?>
                <div class="w-48 h-48 mx-auto bg-slate-900 p-3 rounded-2xl flex flex-col items-center justify-center text-white space-y-2 shadow-inner">
                    <div class="w-32 h-32 bg-slate-950 rounded-lg p-2 flex items-center justify-center border border-slate-700">
                        <div class="w-full h-full border-4 border-emerald-500 border-dashed flex items-center justify-center font-black text-emerald-200 text-xs">
                            QRIS CODE
                        </div>
                    </div>
                    <p class="text-[10px] font-mono tracking-widest uppercase">NMID: ID1029384756</p>
                </div>
                <p class="text-xs text-emerald-200">Tempatkan file <strong>assets/qris.jpeg</strong> di root proyek untuk menampilkan gambar QRIS.</p>
            <?php endif; ?>

            <div class="p-4 bg-slate-950 rounded-xl text-left space-y-1 text-xs border border-slate-800">
                <p class="font-bold text-white">Rincian Pemesanan:</p>
                <p>• Lapangan: <strong><?= htmlspecialchars($r['lapangan_nama']) ?></strong></p>
                <p>• Tanggal: <strong><?= $r['tanggal'] ?></strong> (<?= substr($r['jam_mulai'], 0, 5) ?> - <?= substr($r['jam_selesai'], 0, 5) ?>)</p>
                <p>• Status Saat Ini: <strong class="uppercase text-orange-600"><?= $r['status'] ?></strong></p>
            </div>

            <?php if ($r['status'] === 'menunggu'): ?>
                <form method="POST" class="space-y-4">
                    <div class="p-4 border-2 border-dashed border-slate-700 rounded-xl bg-slate-950/80">
                        <p class="text-xs font-bold text-white mb-1">Simulasi Konfirmasi Pembayaran Instant</p>
                        <p class="text-[11px] text-emerald-200 mb-3">Klik tombol di bawah untuk mengonfirmasi bahwa Anda telah melakukan transfer QRIS.</p>
                        <button type="submit" class="w-full py-3 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs shadow transition">
                            Konfirmasi Telah Transfer QRIS
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="p-4 bg-emerald-900 border border-emerald-700 text-emerald-100 rounded-xl text-xs font-bold">
                    🎉 Pembayaran Lunas! Slot lapangan Anda sudah ter-booking sempurna.
                </div>
            <?php endif; ?>

            <a href="index.php" class="inline-block text-xs font-bold text-emerald-300 hover:text-white">
                ← Kembali ke Riwayat Reservasi Saya
            </a>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>

</body>
</html>
