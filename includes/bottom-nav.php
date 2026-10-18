<?php /* bottom-nav.php — butuh: $active ('menu' | 'pesanan' | 'status' | 'resto') */
$active = $active ?? 'menu'; ?>
<nav class="bottom-nav">
  <a href="index.php" class="<?= $active === 'menu' ? 'is-active' : '' ?>"><i data-ico="crossed"></i>Menu</a>
  <a href="#" class="<?= $active === 'pesanan' ? 'is-active' : '' ?>"><i data-ico="bag"></i>Pesanan</a>
  <a href="#" class="<?= $active === 'status' ? 'is-active' : '' ?>"><i data-ico="receipt"></i>Status</a>
  <a href="#" class="<?= $active === 'resto' ? 'is-active' : '' ?>"><i data-ico="cup"></i><?= e(APP_NAME) ?></a>
</nav>
