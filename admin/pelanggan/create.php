<?php
// admin/pelanggan/create.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: ../../auth/login.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $no_hp    = trim($_POST['no_hp'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($nama && $email && $password) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email sudah digunakan!';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role, no_hp) VALUES (?, ?, ?, 'pelanggan', ?)");
            if ($stmt->execute([$nama, $email, $hashed, $no_hp])) {
                header("Location: index.php");
                exit;
            } else {
                $error = 'Gagal menambahkan data pelanggan.';
            }
        }
    } else {
        $error = 'Lengkapi seluruh formulir wajib!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pelanggan - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 font-sans min-h-screen flex">

    <?php include '../../includes/navbar_admin.php'; ?>

    <main class="flex-1 p-8 space-y-6 overflow-y-auto max-w-2xl">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm">
            <h1 class="text-2xl font-black text-white">Tambah Akun Pelanggan Baru</h1>
            <p class="text-xs text-slate-400 mt-1">Registrasi pelanggan secara manual oleh admin.</p>
        </div>

        <?php if ($error): ?>
            <div class="p-3 bg-rose-900 border border-rose-700 text-rose-100 text-xs font-bold rounded-xl">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-sm space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Nama Lengkap</label>
                <input type="text" name="nama" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Email</label>
                <input type="email" name="email" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">No. HP / WhatsApp</label>
                <input type="text" name="no_hp" placeholder="081234567890" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Password</label>
                <input type="password" name="password" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-100 outline-none focus:ring-2 focus:ring-orange-500">
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="submit" class="px-6 py-2.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl text-xs shadow">
                    Simpan Akun Pelanggan
                </button>
                <a href="index.php" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-100 font-bold rounded-xl text-xs transition">
                    Batal
                </a>
            </div>
        </form>
    </main>

</body>
</html>
