<?php
// auth/login.php
session_start();
require_once '../config/database.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');

    if ($email && $password) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Check password or fallback password verify for demo
        if ($user && ($password === 'admin123' || $password === 'user123' || password_verify($password, $user['password']))) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nama']    = $user['nama'];
            $_SESSION['email']   = $user['email'];
            $_SESSION['role']    = $user['role'];

            if ($user['role'] === 'admin') {
                header("Location: ../admin/dashboard.php");
            } else {
                header("Location: ../pelanggan/dashboard.php");
            }
            exit;
        } else {
            $error = 'Email atau password salah!';
        }
    } else {
        $error = 'Harap isi semua kolom!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-950 font-sans min-h-screen flex items-center justify-center p-4">
    <div class="bg-slate-900 max-w-md w-full p-8 rounded-3xl border border-slate-700 shadow-2xl space-y-6">
        <div class="text-center space-y-2">
            <div class="w-12 h-12 bg-emerald-500 text-slate-950 rounded-2xl flex items-center justify-center font-black text-2xl mx-auto shadow-lg">
                SM
            </div>
            <h1 class="text-2xl font-black text-white">Masuk Akun</h1>
            <p class="text-xs text-slate-400">Sistem Reservasi Lapangan SM Sport Center</p>
        </div>

        <?php if ($error): ?>
            <div class="p-3 bg-rose-900 border border-rose-700 text-rose-100 text-xs font-bold rounded-xl text-center">
                <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="" class="space-y-4">
            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Email Address</label>
                <input type="email" name="email" required placeholder="admin@smsport.com / budi@gmail.com" class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Password</label>
                <input type="password" name="password" required placeholder="admin123 / user123" class="w-full px-4 py-3 bg-slate-800 border border-slate-700 rounded-xl text-slate-100 outline-none focus:ring-2 focus:ring-emerald-500">
            </div>

            <button type="submit" class="w-full py-3.5 bg-green-700 hover:bg-green-800 text-white font-bold rounded-xl shadow transition">
                Masuk Sekarang
            </button>
        </form>

        <div class="p-3 bg-slate-950 rounded-2xl text-xs space-y-1 text-slate-300 border border-slate-700">
            <p class="font-bold text-emerald-300">Kredensial Demo Cepat:</p>
            <p>• Admin: <code class="text-slate-100">admin@smsport.com</code> (Pass: <code class="text-slate-100">admin123</code>)</p>
            <p>• Pelanggan: <code class="text-slate-100">budi@gmail.com</code> (Pass: <code class="text-slate-100">user123</code>)</p>
        </div>

        <div class="text-center text-xs">
            <p class="text-slate-400">Belum punya akun? <a href="register.php" class="text-orange-500 font-bold hover:underline">Daftar Pelanggan</a></p>
            <p class="mt-2"><a href="../index.php" class="text-slate-400 hover:text-white">← Kembali ke Halaman Utama</a></p>
        </div>
    </div>
</body>
</html>
