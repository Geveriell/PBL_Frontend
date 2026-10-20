<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$meja = meja();
$cart = Cart::summary();

// Belum ada pesanan → kembali ke menu
if ($cart['items'] === 0) {
    header('Location: index.php');
    exit;
}

$lines = Cart::lines();
$ref   = Cart::ref();
$persen = (int) round(PAJAK_SERVIS * 100);

/* Waktu pesanan diterima: dicatat sekali per session */
$_SESSION['order_at'] ??= time();
$diterima = date('H:i', (int) $_SESSION['order_at']);

/* Tahap pesanan 1–4. Sambungkan ke database/api bila backend siap;
   untuk demo bisa dicoba lewat status.php?step=3 */
$step = max(1, min(4, (int) ($_GET['step'] ?? 2)));

$tahap = [
    1 => ['judul' => 'Pesanan Diterima',          'ikon' => 'tick',  'desk' => 'Sistem telah memverifikasi pembayaran QRIS. Masuk ke antrean barista.'],
    2 => ['judul' => 'Sedang Diracik & Dimasak',  'ikon' => 'pot',   'desk' => 'Barista meracik minuman, dapur memasak makanan. Total ' . $cart['items'] . ' item sedang disiapkan.'],
    3 => ['judul' => 'Siap Diantar ke Meja ' . $meja, 'ikon' => 'serve', 'desk' => 'Pelayan ' . APP_NAME . ' akan segera membawa pesanan ke meja Anda.'],
    4 => ['judul' => 'Selesai & Selamat Menikmati', 'ikon' => 'smile', 'desk' => 'Momen bersantap penuh kehangatan di ' . APP_NAME . '.'],
];
$hero = [
    1 => ['Pesanan Meja ' . $meja . ' Telah Masuk!',   'Duduk manis, nikmati suasana sejuk Malang. Barista & dapur kami segera menyiapkan pesanan Anda.', '8 - 12 Menit'],
    2 => ['Pesanan Meja ' . $meja . ' Sedang Disiapkan', 'Barista & dapur sedang bekerja. Cukup duduk manis, kami antar begitu siap.',                         '5 - 8 Menit'],
    3 => ['Pesanan Meja ' . $meja . ' Siap Diantar',   'Pesanan Anda selesai dan sedang dibawa pelayan ke meja.',                                           '1 - 3 Menit'],
    4 => ['Selamat Menikmati di Meja ' . $meja . '!',  'Terima kasih sudah memesan. Tambah pesanan kapan saja dari menu.',                                  'Selesai'],
];

$title   = 'Status Pesanan';
$cssPage = 'status';
$jsPage  = 'status';
$active  = 'status';

include __DIR__ . '/includes/head.php';
?>
  <main class="app app--status" data-meja="<?= e($meja) ?>">
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="table-bar">
      <span><span class="dot"></span>Meja <?= e($meja) ?> • <?= e(AREA_MEJA) ?></span>
      <small>Order Status</small>
    </div>

    <div class="status-layout">

      <div class="status-col">
        <section class="hero">
          <div class="hero-top">
            <span class="live"><i></i>LIVE TRACKER</span>
            <span class="hero-ref"><?= e($ref) ?></span>
          </div>
          <h2><?= e($hero[$step][0]) ?></h2>
          <p><?= e($hero[$step][1]) ?></p>
        </section>

        <section class="card eta-card">
          <div class="eta">
            <div>
              <small>ESTIMASI TIBA DI MEJA</small>
              <strong><?= e($hero[$step][2]) ?></strong>
            </div>
            <span class="eta-ico"><i data-ico="stopwatch"></i></span>
          </div>

          <ol class="tl" aria-label="Tahap pesanan">
            <?php foreach ($tahap as $no => $t):
              $cls = $no < $step ? 'is-done' : ($no === $step ? 'is-now' : ''); ?>
            <li class="tl-step <?= $cls ?>"<?= $no === $step ? ' aria-current="step"' : '' ?>>
              <span class="tl-dot"><i data-ico="<?= $no < $step ? 'tick' : e($t['ikon']) ?>"></i></span>
              <div class="tl-body">
                <div class="tl-title">
                  <b><?= e($t['judul']) ?></b>
                  <?php if ($no === 1): ?><time><?= e($diterima) ?> WIB</time><?php endif; ?>
                  <?php if ($no === $step && $step < 4): ?><span class="tl-badge">Proses</span><?php endif; ?>
                </div>
                <p><?= e($t['desk']) ?></p>
                <?php if ($no === 2 && $no === $step): ?>
                  <div class="tl-chips"><span><i data-ico="cup"></i>Bar Barista</span><span><i data-ico="utensils"></i>Dapur Utama</span></div>
                <?php endif; ?>
              </div>
            </li>
            <?php endforeach; ?>
          </ol>
        </section>
      </div>

      <div class="status-col">
        <section class="card order-card">
          <h2 class="order-h"><i data-ico="receipt"></i>Pesanan Anda</h2>
          <ul class="order-lines">
            <?php foreach ($lines as $l): ?>
            <li>
              <div class="order-photo ph">
                <?php if ($l['foto']): ?><img src="assets/img/<?= e($l['foto']) ?>" alt="" loading="lazy"><?php else: ?><i data-ico="<?= e($l['ikon']) ?>"></i><?php endif; ?>
              </div>
              <div class="order-info">
                <b><?= $l['qty'] ?>× <?= e($l['nama']) ?></b>
                <?php if ($l['opsi']): ?><span><?= e($l['opsi']) ?></span><?php endif; ?>
              </div>
              <b class="order-price"><?= rp($l['qty'] * $l['harga']) ?></b>
            </li>
            <?php endforeach; ?>
          </ul>
          <dl class="order-sum">
            <div><dt>Subtotal</dt><dd><?= rp($cart['subtotal']) ?></dd></div>
            <div><dt>PB1 &amp; Servis (<?= $persen ?>%)</dt><dd><?= rp($cart['pajak']) ?></dd></div>
            <div class="order-total"><dt>Total Pembayaran QRIS</dt><dd><?= rp($cart['total']) ?> <em>(Lunas)</em></dd></div>
          </dl>
        </section>

        <button class="call-waiter" id="btnCall">
          <i data-ico="bell"></i>
          <span>Panggil Pelayan ke Meja <?= e($meja) ?></span>
          <i data-ico="arrow"></i>
        </button>

        <div class="status-actions">
          <button class="btn-lg btn-lg--soft" id="btnReceipt"><i data-ico="receipt"></i>Struk Digital</button>
          <a class="btn-lg btn-lg--accent" href="index.php"><i data-ico="plus"></i>Pesan Tambahan</a>
        </div>
      </div>

    </div>
  </main>

  <?php include __DIR__ . '/includes/bottom-nav.php'; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
