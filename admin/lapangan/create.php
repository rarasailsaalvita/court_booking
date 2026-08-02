<?php
// admin/lapangan/create.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama          = trim($_POST['nama'] ?? '');
    $jenis         = trim($_POST['jenis'] ?? '');
    $harga_per_jam = floatval($_POST['harga_per_jam'] ?? 0);
    $deskripsi     = trim($_POST['deskripsi'] ?? '');
    $status        = trim($_POST['status'] ?? 'aktif');

    if ($nama && $jenis && $harga_per_jam > 0) {
        $stmt = $pdo->prepare("INSERT INTO lapangan (nama, jenis, harga_per_jam, deskripsi, status) VALUES (?, ?, ?, ?, ?)");
        if ($stmt->execute([$nama, $jenis, $harga_per_jam, $deskripsi, $status])) {
            header("Location: index.php");
            exit;
        } else {
            $error = 'Gagal menyimpan data lapangan.';
        }
    } else {
        $error = 'Harap isi nama, jenis, dan harga per jam dengan benar!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Lapangan - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 font-sans min-h-screen flex">

    <?php include '../../includes/navbar_admin.php'; ?>

    <main class="flex-1 p-8 space-y-6 overflow-y-auto max-w-3xl">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm">
            <h1 class="text-2xl font-black text-white">Tambah Lapangan Baru</h1>
            <p class="text-xs text-slate-400 mt-1">Isi detail data lapangan futsal atau badminton.</p>
        </div>

        <?php if ($error): ?>
            <div class="p-3 bg-rose-900 border border-rose-700 text-rose-100 text-xs font-bold rounded-xl">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Lapangan</label>
                <input type="text" name="nama" required placeholder="Contoh: Lapangan Futsal C (Sintetis)" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-green-600">
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Jenis Olahraga</label>
                    <select name="jenis" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-green-600">
                        <option value="futsal">Futsal</option>
                        <option value="badminton">Badminton</option>
                    </select>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Harga Sewa / Jam (Rp)</label>
                    <input type="number" name="harga_per_jam" required placeholder="120000" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-green-600">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Deskripsi & Fasilitas</label>
                <textarea name="deskripsi" rows="3" placeholder="Fasilitas karpet vinyl, pencahayaan LED..." class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-xs font-medium text-slate-100 outline-none focus:ring-2 focus:ring-green-600"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Status Lapangan</label>
                <select name="status" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-green-600">
                    <option value="aktif">Aktif (Tersedia untuk disewa)</option>
                    <option value="nonaktif">Nonaktif (Perbaikan / Maintenance)</option>
                </select>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-green-700 hover:bg-green-800 text-white font-bold rounded-xl text-xs shadow">
                    Simpan Lapangan
                </button>
                <a href="index.php" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-100 font-bold rounded-xl text-xs transition">
                    Batal
                </a>
            </div>
        </form>
    </main>

</body>
</html>
