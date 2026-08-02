<?php
// sm_sport_center/index.php - Landing Page
session_start();
require_once 'config/database.php';

// Fetch Lapangan Aktif
$stmt = $pdo->query("SELECT * FROM lapangan WHERE status = 'aktif' ORDER BY id ASC");
$lapangans = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SM Sport Center - Reservasi Lapangan Futsal & Badminton</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen flex flex-col justify-between">

    <!-- Header / Navbar -->
    <header class="bg-slate-900 border-b border-slate-800 sticky top-0 z-40">
        <div class="max-w-7xl mx-auto px-4 py-4 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-green-600 text-white flex items-center justify-center font-black text-xl shadow">
                    SM
                </div>
                <div>
                    <h1 class="text-xl font-extrabold text-white leading-tight">SM SPORT <span class="text-green-400">CENTER</span></h1>
                    <p class="text-[10px] text-orange-400 font-bold uppercase tracking-widest">Futsal & Badminton Court</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="<?= $_SESSION['role'] === 'admin' ? 'admin/dashboard.php' : 'pelanggan/dashboard.php' ?>" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-bold rounded-lg text-sm transition">
                        Dashboard Saya (<?= htmlspecialchars($_SESSION['nama']) ?>)
                    </a>
                    <a href="auth/logout.php" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 font-semibold rounded-lg text-sm">
                        Keluar
                    </a>
                <?php else: ?>
                    <a href="auth/login.php" class="px-4 py-2 text-slate-100 font-bold text-sm hover:text-green-400">
                        Masuk
                    </a>
                    <a href="auth/register.php" class="px-4 py-2 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-lg text-sm shadow transition">
                        Daftar Pelanggan
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <main class="max-w-7xl mx-auto px-4 py-10 space-y-12 w-full">
        <!-- Hero Section -->
        <section class="bg-gradient-to-br from-slate-900 via-slate-800 to-green-950 text-white p-8 sm:p-12 rounded-3xl shadow-2xl space-y-6">
            <span class="inline-block px-3 py-1 bg-emerald-900 text-emerald-100 font-bold text-xs uppercase tracking-wider rounded-full border border-emerald-700">
                Sistem Reservasi Online
            </span>
            <h1 class="text-4xl sm:text-5xl font-black text-white leading-tight">
                Sewa Lapangan Futsal & Badminton <span class="text-green-400">Anti-Double Booking</span>
            </h1>
            <p class="text-slate-300 max-w-2xl text-base sm:text-lg">
                Jadwal interaktif real-time, konfirmasi otomatis pembayaran QRIS, dan fasilitas lapangan olahraga berstandar tinggi.
            </p>
            <div class="flex flex-wrap items-center gap-4 pt-2">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'pelanggan'): ?>
                    <a href="pelanggan/reservasi/create.php" class="px-6 py-3.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-lg transition">
                        Booking Lapangan Sekarang
                    </a>
                <?php else: ?>
                    <a href="auth/login.php" class="px-6 py-3.5 bg-orange-500 hover:bg-orange-600 text-white font-bold rounded-xl shadow-lg transition">
                        Booking Lapangan Sekarang
                    </a>
                    <a href="auth/register.php" class="px-6 py-3.5 bg-slate-800 hover:bg-slate-700 text-white font-semibold rounded-xl border border-slate-700 transition">
                        Buat Akun Baru
                    </a>
                <?php endif; ?>
            </div>
        </section>

        <!-- Listing Lapangan -->
        <section class="space-y-6">
            <div>
                <h2 class="text-2xl font-extrabold text-slate-100">Daftar Lapangan & Harga Sewa</h2>
                <p class="text-slate-400 text-sm">Pilih lapangan favorit Anda di bawah ini:</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <?php foreach ($lapangans as $lap): ?>
                    <div class="bg-slate-900 p-6 rounded-2xl border border-slate-800 shadow-sm space-y-4 hover:shadow-lg transition">
                        <div class="flex items-center justify-between">
                            <span class="px-3 py-1 text-xs font-bold uppercase rounded-full <?= $lap['jenis'] === 'futsal' ? 'bg-emerald-800 text-emerald-100' : 'bg-blue-800 text-blue-100' ?>">
                                <?= htmlspecialchars($lap['jenis']) ?>
                            </span>
                            <span class="text-xs font-bold px-2 py-1 bg-green-800 text-green-100 rounded">Tersedia</span>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-white"><?= htmlspecialchars($lap['nama']) ?></h3>
                            <p class="text-slate-400 text-xs mt-1 min-h-[36px]"><?= htmlspecialchars($lap['deskripsi']) ?></p>
                        </div>
                        <div class="p-3 bg-slate-950 rounded-xl border border-slate-800">
                            <p class="text-[11px] text-slate-400 font-bold uppercase">Harga Sewa</p>
                            <p class="text-2xl font-black text-green-400">Rp <?= number_format($lap['harga_per_jam'], 0, ',', '.') ?> <span class="text-xs font-normal text-slate-400">/ jam</span></p>
                        </div>
                        <?php if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'pelanggan'): ?>
                            <a href="pelanggan/reservasi/create.php?lapangan_id=<?= $lap['id'] ?>" class="block w-full text-center py-2.5 bg-slate-900 hover:bg-green-600 text-white font-bold rounded-xl text-sm transition">
                                Pilih & Booking
                            </a>
                        <?php else: ?>
                            <a href="auth/login.php" class="block w-full text-center py-2.5 bg-slate-900 hover:bg-green-600 text-white font-bold rounded-xl text-sm transition">
                                Pilih & Booking
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-6 border-t border-slate-800 text-center text-xs">
        <p>&copy; <?= date('Y') ?> SM Sport Center. Sistem Reservasi Lapangan Futsal & Badminton PHP Native + MySQL.</p>
    </footer>

</body>
</html>
