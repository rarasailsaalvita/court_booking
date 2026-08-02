<?php
// pelanggan/reservasi/create.php
session_start();
require_once '../../config/database.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'pelanggan') {
    header("Location: ../../auth/login.php");
    exit;
}

$selected_lapangan_id = intval($_GET['lapangan_id'] ?? 0);
$error = '';

$stmt = $pdo->query("SELECT * FROM lapangan WHERE status = 'aktif' ORDER BY id ASC");
$lapangans = $stmt->fetchAll();

if (!$selected_lapangan_id && !empty($lapangans)) {
    $selected_lapangan_id = $lapangans[0]['id'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id     = $_SESSION['user_id'];
    $lapangan_id = intval($_POST['lapangan_id'] ?? 0);
    $tanggal     = trim($_POST['tanggal'] ?? '');
    $jam_mulai   = trim($_POST['jam_mulai'] ?? '');
    $durasi      = intval($_POST['durasi'] ?? 1);
    $catatan     = trim($_POST['catatan'] ?? '');

    if ($lapangan_id && $tanggal && $jam_mulai && $durasi > 0) {
        $start_timestamp = strtotime("$tanggal $jam_mulai");
        $end_timestamp   = $start_timestamp + ($durasi * 3600);
        
        $str_jam_mulai   = date('H:i:s', $start_timestamp);
        $str_jam_selesai = date('H:i:s', $end_timestamp);

        // Validate Anti-Double Booking Conflict
        $checkStmt = $pdo->prepare("
            SELECT id FROM reservasi
            WHERE lapangan_id = ?
              AND tanggal = ?
              AND status != 'dibatalkan'
              AND (
                  (jam_mulai < ? AND jam_selesai > ?)
              )
        ");
        $checkStmt->execute([$lapangan_id, $tanggal, $str_jam_selesai, $str_jam_mulai]);
        $conflict = $checkStmt->fetch();

        if ($conflict) {
            $error = 'MOHON MAAF: Jadwal slot waktu ini BENTROK dengan reservasi lain yang sudah terisi!';
        } else {
            // Get court price
            $lapStmt = $pdo->prepare("SELECT harga_per_jam FROM lapangan WHERE id = ?");
            $lapStmt->execute([$lapangan_id]);
            $lap = $lapStmt->fetch();

            $total_bayar = ($lap['harga_per_jam'] ?? 50000) * $durasi;

            $insertStmt = $pdo->prepare("
                INSERT INTO reservasi (user_id, lapangan_id, tanggal, jam_mulai, jam_selesai, durasi, total_bayar, status, catatan)
                VALUES (?, ?, ?, ?, ?, ?, ?, 'menunggu', ?)
            ");
            if ($insertStmt->execute([$user_id, $lapangan_id, $tanggal, $str_jam_mulai, $str_jam_selesai, $durasi, $total_bayar, $catatan])) {
                $new_id = $pdo->lastInsertId();
                header("Location: upload_bukti.php?id=$new_id");
                exit;
            } else {
                $error = 'Gagal memproses reservasi, coba lagi.';
            }
        }
    } else {
        $error = 'Harap isi seluruh formulir pemesanan dengan benar!';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Reservasi - SM Sport Center</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        .slot-btn.active {
            background-color: #059669 !important;
            color: #ffffff !important;
            border-color: #059669 !important;
            box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
        }
    </style>
</head>
<body class="bg-slate-950 text-slate-100 font-sans min-h-screen flex flex-col">

    <?php include '../../includes/navbar_pelanggan.php'; ?>

    <main class="max-w-5xl mx-auto w-full px-4 py-8 space-y-6 flex-1">
        <div class="bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-xl flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-white">Form Pemesanan Lapangan</h1>
                <p class="text-xs text-emerald-200 mt-1">Sistem dilengkapi fitur pemilih slot jam instant & anti-double booking.</p>
            </div>
            <a href="../dashboard.php" class="inline-flex items-center gap-1.5 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-100 font-bold rounded-xl text-xs transition">
                ← Kembali ke Dashboard
            </a>
        </div>

        <?php if ($error): ?>
            <div class="p-4 bg-red-900 border border-red-700 text-red-100 text-xs font-bold rounded-xl flex items-center gap-2">
                <span>⚠️ <?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
            <!-- Form Input Section (7 cols) -->
            <form method="POST" id="bookingForm" class="lg:col-span-7 bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-xl space-y-5">
                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Pilih Lapangan</label>
                    <select name="lapangan_id" id="lapanganSelect" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-emerald-500 text-sm">
                        <option value="" class="bg-slate-950 text-slate-100">-- Pilih Lapangan --</option>
                        <?php foreach ($lapangans as $l): ?>
                            <option value="<?= $l['id'] ?>" data-harga="<?= $l['harga_per_jam'] ?>" <?= $selected_lapangan_id == $l['id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($l['nama']) ?> (<?= strtoupper($l['jenis']) ?>) — Rp <?= number_format($l['harga_per_jam'], 0, ',', '.') ?> / jam
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Tanggal Main</label>
                        <input type="date" name="tanggal" id="tanggalInput" min="<?= date('Y-m-d') ?>" value="<?= date('Y-m-d') ?>" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-emerald-500 text-xs">
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Jam Mulai</label>
                        <select name="jam_mulai" id="jamMulaiSelect" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-emerald-500 text-xs">
                            <?php for ($h = 8; $h <= 21; $h++): ?>
                                <?php $time = sprintf('%02d:00', $h); ?>
                                <option value="<?= $time ?>"><?= $time ?> WIB</option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div>
                        <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Durasi (Jam)</label>
                        <select name="durasi" id="durasiSelect" required class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl font-semibold text-slate-100 outline-none focus:ring-2 focus:ring-emerald-500 text-xs">
                            <option value="1">1 Jam</option>
                            <option value="2" selected>2 Jam</option>
                            <option value="3">3 Jam</option>
                            <option value="4">4 Jam</option>
                        </select>
                    </div>
                </div>

                <div>
                    <label class="block text-xs font-bold text-slate-300 uppercase mb-1">Catatan Tambahan (Opsional)</label>
                    <textarea name="catatan" rows="2" placeholder="Contoh: Tim Sparta Futsal / Butuh kok badminton tambahan" class="w-full px-4 py-2.5 bg-slate-800 border border-slate-700 rounded-xl text-slate-100 text-xs outline-none focus:ring-2 focus:ring-emerald-500"></textarea>
                </div>

                <!-- Price Estimator -->
                <div class="p-4 bg-slate-800 rounded-xl border border-emerald-600 flex items-center justify-between">
                    <div>
                        <p class="text-xs font-semibold text-emerald-300">Estimasi Total Bayar:</p>
                        <p class="text-xs text-emerald-200" id="priceDetailText">1 Jam @ Rp 0</p>
                    </div>
                    <p class="text-xl font-black text-emerald-400" id="totalPriceText">Rp 0</p>
                </div>

                <div class="flex items-center justify-between pt-4 border-t border-slate-700">
                    <a href="../dashboard.php" class="px-5 py-2.5 bg-slate-800 text-slate-100 font-bold rounded-xl text-xs hover:bg-slate-700 transition">
                        Batal
                    </a>
                    <button type="submit" class="px-6 py-2.5 bg-emerald-500 hover:bg-emerald-600 text-slate-950 font-bold rounded-xl text-xs shadow-lg transition">
                        Lanjut ke Pembayaran QRIS →
                    </button>
                </div>
            </form>

            <!-- Time Slots Interactive Grid Section (5 cols) -->
            <div class="lg:col-span-5 bg-slate-900 p-6 rounded-2xl border border-slate-700 shadow-xl space-y-4">
                <div class="border-b border-slate-700 pb-3">
                    <h2 class="text-base font-extrabold text-white flex items-center gap-2">
                        <span>⏰</span> Slot Jam Tersedia
                    </h2>
                    <p class="text-xs text-emerald-200 mt-0.5">
                        Pilih slot jam secara instant sesuai tanggal & lapangan yang Anda pilih.
                    </p>
                </div>

                <!-- Loading Indicator -->
                <div id="slotsLoading" class="hidden text-center py-8 text-emerald-200 text-xs">
                    <span>🔄 Memuat ketersediaan slot...</span>
                </div>

                <!-- Slots Container -->
                <div id="slotsGrid" class="grid grid-cols-2 gap-2 max-h-[360px] overflow-y-auto pr-1">
                    <!-- Dynamic slots injected via JS -->
                </div>

                <!-- Legend -->
                <div class="pt-3 text-[11px] text-emerald-200 flex items-center justify-around border-t border-slate-700">
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-emerald-600"></span> Dipilih
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-emerald-500 border border-emerald-400"></span> Tersedia
                    </span>
                    <span class="flex items-center gap-1.5">
                        <span class="w-3 h-3 rounded bg-slate-700 border border-slate-600"></span> Terisi (Disabled)
                    </span>
                </div>
            </div>
        </div>
    </main>

    <?php include '../../includes/footer.php'; ?>

    <script>
        const lapanganSelect = document.getElementById('lapanganSelect');
        const tanggalInput = document.getElementById('tanggalInput');
        const jamMulaiSelect = document.getElementById('jamMulaiSelect');
        const durasiSelect = document.getElementById('durasiSelect');
        const slotsGrid = document.getElementById('slotsGrid');
        const slotsLoading = document.getElementById('slotsLoading');
        const totalPriceText = document.getElementById('totalPriceText');
        const priceDetailText = document.getElementById('priceDetailText');

        let bookedReservations = [];

        function updatePrice() {
            const selectedOpt = lapanganSelect.options[lapanganSelect.selectedIndex];
            const harga = selectedOpt ? parseInt(selectedOpt.getAttribute('data-harga') || '0') : 0;
            const durasi = parseInt(durasiSelect.value || '1');
            const total = harga * durasi;

            totalPriceText.textContent = `Rp ${total.toLocaleString('id-ID')}`;
            priceDetailText.textContent = `${durasi} Jam @ Rp ${harga.toLocaleString('id-ID')}/jam`;
        }

        async function fetchSlots() {
            const lapanganId = lapanganSelect.value;
            const tanggal = tanggalInput.value;

            if (!lapanganId || !tanggal) {
                slotsGrid.innerHTML = '<p class="col-span-2 text-xs text-emerald-200 text-center py-4">Silakan pilih lapangan dan tanggal.</p>';
            }

            slotsGrid.classList.add('hidden');
            slotsLoading.classList.remove('hidden');

            try {
                const res = await fetch(`get_slots.php?lapangan_id=${lapanganId}&tanggal=${tanggal}`);
                bookedReservations = await res.json();
                renderSlots();
            } catch (err) {
                console.error("Error loading slots:", err);
                slotsGrid.innerHTML = '<p class="col-span-2 text-xs text-red-500 text-center py-4">Gagal memuat slot jam.</p>';
            } finally {
                slotsLoading.classList.add('hidden');
                slotsGrid.classList.remove('hidden');
            }
        }

        function renderSlots() {
            slotsGrid.innerHTML = '';
            const selectedJam = jamMulaiSelect.value;

            for (let h = 8; h <= 21; h++) {
                const slotStartStr = `${h.toString().padStart(2, '0')}:00`;
                const slotEndStr = `${(h + 1).toString().padStart(2, '0')}:00`;
                const slotStartFull = slotStartStr + ':00';
                const slotEndFull = slotEndStr + ':00';

                // Check collision with existing reservations
                const isOccupied = bookedReservations.some(r => {
                    const rStart = r.jam_mulai.length === 5 ? r.jam_mulai + ':00' : r.jam_mulai;
                    const rEnd = r.jam_selesai.length === 5 ? r.jam_selesai + ':00' : r.jam_selesai;
                    return slotStartFull < rEnd && slotEndFull > rStart;
                });

                const isSelected = (selectedJam === slotStartStr);

                const btn = document.createElement('button');
                btn.type = 'button';
                btn.disabled = isOccupied;

                let btnClass = "p-2.5 rounded-xl text-left border text-xs transition flex items-center justify-between slot-btn ";
                if (isOccupied) {
                    btnClass += "bg-slate-900 border-slate-700 text-slate-500 cursor-not-allowed opacity-80";
                } else if (isSelected) {
                    btnClass += "bg-emerald-600 text-white border-emerald-600 shadow-sm active";
                } else {
                    btnClass += "bg-slate-950 hover:bg-emerald-800 border border-emerald-600 text-emerald-100 hover:text-white";
                }

                btn.className = btnClass;
                btn.innerHTML = `
                    <div>
                        <span class="block font-bold">${slotStartStr} - ${slotEndStr}</span>
                        <span class="text-[10px] opacity-80 block">${isOccupied ? 'Penghuni: FULL' : 'Tersedia'}</span>
                    </div>
                    ${isOccupied ? '<span class="px-1 py-0.5 bg-rose-800 text-rose-100 font-bold text-[9px] rounded">FULL</span>' : (isSelected ? '<span>✓</span>' : '')}
                `;

                if (!isOccupied) {
                    btn.addEventListener('click', () => {
                        jamMulaiSelect.value = slotStartStr;
                        renderSlots();
                        updatePrice();
                    });
                }

                slotsGrid.appendChild(btn);
            }
        }

        // Event listeners
        lapanganSelect.addEventListener('change', () => {
            updatePrice();
            fetchSlots();
        });

        tanggalInput.addEventListener('change', fetchSlots);

        jamMulaiSelect.addEventListener('change', () => {
            renderSlots();
            updatePrice();
        });

        durasiSelect.addEventListener('change', updatePrice);

        // Initial trigger
        updatePrice();
        fetchSlots();
    </script>
</body>
</html>
