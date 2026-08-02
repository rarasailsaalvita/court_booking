<?php
// auth/register.php
session_start();
require_once '../config/database.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama     = trim($_POST['nama'] ?? '');
    $email    = trim($_POST['email'] ?? '');
    $no_hp    = trim($_POST['no_hp'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($nama && $email && $password) {
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetch()) {
            $error = 'Email sudah terdaftar!';
        } else {
            $hashed = password_hash($password, PASSWORD_BCRYPT);
            $stmt = $pdo->prepare("INSERT INTO users (nama, email, password, role, no_hp) VALUES (?, ?, ?, 'pelanggan', ?)");
            if ($stmt->execute([$nama, $email, $hashed, $no_hp])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['nama']    = $nama;
                $_SESSION['email']   = $email;
                $_SESSION['role']    = 'pelanggan';
                header("Location: ../pelanggan/dashboard.php");
                exit;
            } else {
                $error = 'Gagal mendaftar, coba lagi.';
            }
        }
    } else {
        $error = 'Harap lengkapi seluruh formulir!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Pelanggan - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 font-sans min-h-screen flex items-center justify-center p-4">
    <div class="bg-slate-900 max-w-md w-full p-8 rounded-3xl border border-slate-700 shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 bg-orange-500 text-slate-950 rounded-2xl flex items-center justify-center font-black text-2xl mx-auto shadow-lg">
                SM
            </div>
            <h1 class="text-2xl font-black text-white">Daftar Akun Baru</h1>
            <p class="text-xs text-slate-400">Buat akun untuk melakukan booking lapangan futsal & badminton</p>
        </div>

        <?php if ($error): ?>
            <div class="p-3 bg-rose-900 border border-rose-700 text-rose-100 text-xs font-bold rounded-xl text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
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

            <button type="submit" class="w-full py-3.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow transition">
                Daftar Akun Sekarang
            </button>
        </form>

        <div class="text-center text-xs">
            <p class="text-slate-400">Sudah punya akun? <a href="login.php" class="text-emerald-400 font-bold hover:underline">Masuk Login</a></p>
            <p class="mt-2"><a href="../index.php" class="text-slate-400 hover:text-white">← Kembali ke Halaman Utama</a></p>
        </div>
    </div>
</body>
</html>
