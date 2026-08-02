<?php
// includes/navbar_pelanggan.php
if (session_status() === PHP_SESSION_NONE) session_start();

$scriptName = $_SERVER['SCRIPT_NAME'];
$pelangganPos = strpos($scriptName, '/pelanggan');
$siteRoot = $pelangganPos !== false ? substr($scriptName, 0, $pelangganPos) : '';
$pelangganBase = $siteRoot . '/pelanggan';
?>
<header class="bg-slate-950 border-b border-slate-800 sticky top-0 z-40 shadow-xl">
  <div class="max-w-7xl mx-auto px-4 py-3 flex items-center justify-between">
    <a href="<?= $pelangganBase ?>/dashboard.php" class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-xl bg-emerald-500 text-slate-950 flex items-center justify-center font-black text-xl shadow-lg">
        SM
      </div>
      <div>
        <h1 class="text-xl font-black text-white leading-tight">SM SPORT <span class="text-emerald-400">CENTER</span></h1>
        <p class="text-[10px] text-orange-400 font-extrabold uppercase tracking-widest">Futsal & Badminton Court</p>
      </div>
    </a>

    <div class="flex items-center gap-3 font-bold text-xs sm:text-sm">
      <a href="<?= $siteRoot ?>/index.php" class="px-3.5 py-2 rounded-xl text-emerald-200 hover:text-white">
        Beranda
      </a>
      <a href="<?= $pelangganBase ?>/dashboard.php" class="px-3.5 py-2 rounded-xl text-emerald-200 hover:text-white">
        Dashboard
      </a>
      <a href="<?= $pelangganBase ?>/reservasi/create.php" class="px-3.5 py-2 bg-orange-500 hover:bg-orange-600 text-white rounded-xl shadow-lg transition flex items-center gap-1.5">
        <span>+ Buat Reservasi</span>
      </a>
      <a href="<?= $pelangganBase ?>/reservasi/index.php" class="px-3.5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-xl shadow-lg transition">
        Riwayat Reservasi Saya
      </a>
      <a href="<?= $siteRoot ?>/auth/logout.php" class="px-3 py-2 bg-slate-800 hover:bg-slate-700 text-slate-100 rounded-xl">
        Keluar
      </a>
    </div>
  </div>
</header>
