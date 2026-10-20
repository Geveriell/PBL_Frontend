<?php /* bottom-nav.php — butuh: $active ('menu' | 'pesanan' | 'status' | 'resto')
   Ponsel/tablet: bar bawah. Desktop (>=1024px): sidebar kiri (lihat css/style.css). */
$active = $active ?? 'menu'; ?>
<nav class="bottom-nav" aria-label="Navigasi utama">
  <span class="nav-brand"><?= e(APP_NAME) ?></span>
  <a href="index.php" class="<?= $active === 'menu' ? 'is-active' : '' ?>"><i data-ico="crossed"></i>Menu</a>
  <a href="pembayaran.php" class="<?= $active === 'pesanan' ? 'is-active' : '' ?>"><i data-ico="bag"></i>Pesanan</a>
  <a href="status.php" class="<?= $active === 'status' ? 'is-active' : '' ?>"><i data-ico="receipt"></i>Status</a>
  <a href="#" class="<?= $active === 'resto' ? 'is-active' : '' ?>"><i data-ico="cup"></i><?= e(APP_NAME) ?></a>
</nav>
