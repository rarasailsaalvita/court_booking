<?php
// includes/navbar_admin.php
if (session_status() === PHP_SESSION_NONE) session_start();

$scriptName = $_SERVER['SCRIPT_NAME'];
$adminPos = strpos($scriptName, '/admin');
$siteRoot = $adminPos !== false ? substr($scriptName, 0, $adminPos) : '';
$adminBase = $siteRoot . '/admin';
$currentUri = $_SERVER['REQUEST_URI'];
?>
<aside class="w-64 bg-green-700 flex flex-col shadow-xl min-h-screen text-white shrink-0">
  <div class="p-6 border-b border-green-800/50 flex items-center gap-3">
    <div class="w-10 h-10 bg-slate-950 rounded-lg flex items-center justify-center font-black text-green-400 text-xl shadow">
      SM
    </div>
    <div>
      <h1 class="text-white font-bold leading-tight text-base">SM Sport Center</h1>
      <span class="text-[10px] text-green-200 uppercase font-extrabold tracking-widest">Admin Panel</span>
    </div>
  </div>

  <nav class="flex-1 p-4 space-y-1.5 font-bold text-sm">
    <a href="<?= $adminBase ?>/dashboard.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-600 transition <?= basename($_SERVER['PHP_SELF']) == 'dashboard.php' ? 'bg-green-800 text-white' : 'text-green-100' ?>">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a11 11 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
      Dashboard
    </a>

    <a href="<?= $adminBase ?>/lapangan/index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-600 transition <?= str_contains($currentUri, '/admin/lapangan') ? 'bg-green-800 text-white' : 'text-green-100' ?>">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
      Kelola Lapangan
    </a>

    <a href="<?= $adminBase ?>/pelanggan/index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-600 transition <?= str_contains($currentUri, '/admin/pelanggan') ? 'bg-green-800 text-white' : 'text-green-100' ?>">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
      Kelola Pelanggan
    </a>

    <a href="<?= $adminBase ?>/reservasi/index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-600 transition <?= str_contains($currentUri, '/admin/reservasi') ? 'bg-green-800 text-white' : 'text-green-100' ?>">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      Kelola Reservasi
    </a>

    <a href="<?= $adminBase ?>/pembayaran/verifikasi.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-600 transition <?= str_contains($currentUri, '/admin/pembayaran') ? 'bg-green-800 text-white' : 'text-green-100' ?>">
      <svg class="w-5 h-5 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
      Verifikasi QRIS
    </a>

    <a href="<?= $adminBase ?>/laporan/index.php" class="flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-green-600 transition <?= str_contains($currentUri, '/admin/laporan') ? 'bg-green-800 text-white' : 'text-green-100' ?>">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
      Laporan Pendapatan
    </a>
  </nav>

  <div class="p-4 border-t border-green-800/50 space-y-2">
    <div class="p-3 bg-orange-500 rounded-xl text-white text-center shadow">
      <p class="text-[10px] uppercase font-black tracking-wider">Super Administrator</p>
      <p class="text-xs font-bold truncate"><?= htmlspecialchars($_SESSION['nama'] ?? 'Admin') ?></p>
    </div>
    <a href="<?= $siteRoot ?>/auth/logout.php" class="block w-full py-2 bg-green-900 hover:bg-green-950 text-white text-center rounded-xl text-xs font-bold transition">
      Keluar (Logout)
    </a>
  </div>
</aside>
