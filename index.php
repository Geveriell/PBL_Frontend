<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$meja    = meja();
$menu    = Menu::all();
$cart    = Cart::summary();
$kategori = ['Best Seller', 'Kopi Khas Malang', 'Makanan Utama'];

$title   = 'Menu';
$cssPage = 'menu';
$jsPage  = 'menu';
$active  = 'menu';

include __DIR__ . '/includes/head.php';
?>
  <main class="app">
    <?php include __DIR__ . '/includes/header.php'; ?>

    <div class="table-bar">
      <span><span class="dot"></span>Meja <?= e($meja) ?> • <?= e(AREA_MEJA) ?></span>
      <small>Menu Catalog</small>
    </div>
    <div class="table-bar table-bar--detail">
      <span class="tb-ico"><i data-ico="home"></i></span>
      <div class="tb-text"><span><span class="dot"></span><b style="display:inline">Meja <?= e($meja) ?> • <?= e(AREA_DETAIL) ?></b></span><?= e(APP_NAME) ?>, <?= e(APP_ADDRESS) ?></div>
      <button class="btn">Ganti</button>
    </div>

    <label class="search">
      <i data-ico="search"></i>
      <input id="searchInput" type="search" placeholder="Cari kopi, makanan, snack...">
    </label>

    <section class="promo">
      <div class="promo-top"><span class="promo-tag">PROMO JAM NGOPI</span><span>s/d 17:00 WIB</span></div>
      <h2>Diskon 15% Manual Brew &amp; Toast</h2>
      <p>Nikmati sore sejuk Malang dengan seduhan...</p>
      <i class="promo-art" data-ico="cup"></i>
    </section>

    <nav class="chips" id="chips" aria-label="Kategori">
      <?php foreach ($kategori as $i => $k): ?>
        <button class="chip<?= $i === 0 ? ' is-active' : '' ?>"><?= $i === 0 ? '<i data-ico="star"></i>' : '' ?><?= e($k) ?></button>
      <?php endforeach; ?>
    </nav>

    <div class="section-head">
      <h2>Menu Favorit</h2>
      <small id="menuCount"><?= count($menu) ?> Pilihan Tersedia</small>
      <a href="#">Kearifan Lokal <i data-ico="flame"></i></a>
    </div>

    <section class="menu-list" id="menuList">
      <?php foreach ($menu as $m): $qty = Cart::qty((int) $m['id']); include __DIR__ . '/includes/menu-card.php'; endforeach; ?>
    </section>
  </main>

  <a class="cart-bar" id="cartBar" href="pembayaran.php" <?= $cart['items'] === 0 ? 'hidden' : '' ?>>
    <span class="cart-ico"><i data-ico="basket"></i><span class="cart-count" id="cartCount"><?= $cart['items'] ?></span></span>
    <span class="cart-info"><span id="cartText"><?= $cart['items'] ?> Item • Meja <?= e($meja) ?></span><b id="cartTotal"><?= rp($cart['subtotal']) ?></b></span>
    <span class="btn btn--accent">Lihat Pesanan <i data-ico="arrow"></i></span>
  </a>

  <?php include __DIR__ . '/includes/bottom-nav.php'; ?>
<?php include __DIR__ . '/includes/footer.php'; ?>
