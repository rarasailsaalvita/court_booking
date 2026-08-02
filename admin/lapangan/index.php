<?php
// admin/lapangan/index.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM lapangan ORDER BY id DESC");
$lapangans = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Lapangan - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 font-sans min-h-screen flex">

    <?php include '../../includes/navbar_admin.php'; ?>

    <main class="flex-1 p-8 space-y-6 overflow-y-auto">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-white">Kelola Data Lapangan</h1>
                <p class="text-xs text-slate-400 mt-1">Tambah, edit, atau hapus fasilitas lapangan futsal & badminton.</p>
            </div>
            <a href="create.php" class="px-5 py-2.5 bg-green-700 hover:bg-green-800 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-2">
                <span>+ Tambah Lapangan Baru</span>
            </a>
        </div>

        <div class="bg-slate-900 rounded-2xl border border-slate-700 shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-950 border-b border-slate-700 text-slate-400 text-xs font-bold uppercase">
                        <th class="p-4">ID</th>
                        <th class="p-4">Nama Lapangan</th>
                        <th class="p-4">Jenis</th>
                        <th class="p-4">Harga / Jam</th>
                        <th class="p-4">Status</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    <?php foreach ($lapangans as $l): ?>
                        <tr class="hover:bg-slate-800 transition">
                            <td class="p-4 text-xs font-bold text-slate-400">#<?= $l['id'] ?></td>
                            <td class="p-4 font-extrabold text-slate-100"><?= htmlspecialchars($l['nama']) ?></td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase <?= $l['jenis'] === 'futsal' ? 'bg-emerald-800 text-emerald-100' : 'bg-blue-800 text-blue-100' ?>">
                                    <?= htmlspecialchars($l['jenis']) ?>
                                </span>
                            </td>
                            <td class="p-4 font-black text-slate-100">Rp <?= number_format($l['harga_per_jam'], 0, ',', '.') ?></td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold uppercase <?= $l['status'] === 'aktif' ? 'bg-emerald-800 text-emerald-100' : 'bg-slate-600 text-slate-100' ?>">
                                    <?= htmlspecialchars($l['status']) ?>
                                </span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <a href="edit.php?id=<?= $l['id'] ?>" class="px-3 py-1.5 bg-slate-800 text-slate-100 hover:bg-slate-700 rounded-lg text-xs font-bold">Edit</a>
                                <a href="delete.php?id=<?= $l['id'] ?>" onclick="return confirm('Apakah Anda yakin ingin menghapus lapangan ini?')" class="px-3 py-1.5 bg-rose-900 text-rose-100 hover:bg-rose-800 rounded-lg text-xs font-bold">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
