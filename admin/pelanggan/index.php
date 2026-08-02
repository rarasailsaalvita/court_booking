<?php
// admin/pelanggan/index.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$stmt = $pdo->query("SELECT * FROM users WHERE role = 'pelanggan' ORDER BY id DESC");
$pelanggans = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pelanggan - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 font-sans min-h-screen flex">

    <?php include '../../includes/navbar_admin.php'; ?>

    <main class="flex-1 p-8 space-y-6 overflow-y-auto">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-black text-white">Kelola Data Pelanggan</h1>
                <p class="text-xs text-slate-400 mt-1">Daftar pengguna terdaftar di sistem reservasi SM Sport Center.</p>
            </div>
            <a href="create.php" class="px-5 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold text-xs rounded-xl shadow transition flex items-center gap-2">
                <span>+ Tambah Pelanggan Baru</span>
            </a>
        </div>

        <div class="bg-slate-900 rounded-2xl border border-slate-700 shadow-sm overflow-hidden">
            <table class="w-full text-left text-sm">
                <thead>
                    <tr class="bg-slate-950 border-b border-slate-700 text-slate-400 text-xs font-bold uppercase">
                        <th class="p-4">ID</th>
                        <th class="p-4">Nama Pelanggan</th>
                        <th class="p-4">Email</th>
                        <th class="p-4">No. HP / WhatsApp</th>
                        <th class="p-4">Tanggal Mendaftar</th>
                        <th class="p-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    <?php foreach ($pelanggans as $p): ?>
                        <tr class="hover:bg-slate-800 transition">
                            <td class="p-4 text-xs font-bold text-slate-400">#<?= $p['id'] ?></td>
                            <td class="p-4 font-bold text-slate-100"><?= htmlspecialchars($p['nama']) ?></td>
                            <td class="p-4 text-slate-300 font-medium"><?= htmlspecialchars($p['email']) ?></td>
                            <td class="p-4 text-slate-300 font-mono text-xs"><?= htmlspecialchars($p['no_hp'] ?? '-') ?></td>
                            <td class="p-4 text-xs text-slate-400"><?= date('d M Y H:i', strtotime($p['created_at'])) ?></td>
                            <td class="p-4 text-right space-x-2">
                                <a href="edit.php?id=<?= $p['id'] ?>" class="px-3 py-1.5 bg-slate-800 text-slate-100 hover:bg-slate-700 rounded-lg text-xs font-bold">Edit</a>
                                <a href="delete.php?id=<?= $p['id'] ?>" onclick="return confirm('Yakin ingin menghapus akun pelanggan ini?')" class="px-3 py-1.5 bg-rose-900 text-rose-100 hover:bg-rose-800 rounded-lg text-xs font-bold">Hapus</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </main>

</body>
</html>
