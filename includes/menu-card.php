<?php /* menu-card.php — butuh: $m (baris menu), $qty (jumlah di keranjang) */ ?>
<article class="card menu-item" data-id="<?= (int) $m['id'] ?>" data-name="<?= e(strtolower($m['nama'] . ' ' . $m['deskripsi'])) ?>">
  <div class="menu-main">
    <div class="menu-photo ph">
      <?php if ($m['favorit']): ?><span class="badge-fav"><i data-ico="star"></i> Favorit</span><?php endif; ?>
      <?php if ($m['foto']): ?>
        <img src="assets/img/<?= e($m['foto']) ?>" alt="<?= e($m['nama']) ?>" style="position:absolute;inset:0;width:100%;height:100%;object-fit:cover;border-radius:inherit">
      <?php else: ?>foto menu<?php endif; ?>
    </div>
    <div class="menu-body">
      <h3><?= e($m['nama']) ?></h3>
      <p class="desc"><?= e($m['deskripsi']) ?></p>
      <div class="menu-buy">
        <div class="price">
          <?php if ($m['harga_coret']): ?><s><?= rp((int) $m['harga_coret']) ?></s><?php endif; ?>
          <b><?= rp((int) $m['harga']) ?></b>
        </div>
        <div class="buy-ctrl">
          <?php if ($qty > 0): ?>
            <div class="qty">
              <button data-act="dec" aria-label="Kurangi"><i data-ico="minus"></i></button>
              <span><?= $qty ?></span>
              <button class="plus" data-act="inc" aria-label="Tambah"><i data-ico="plus"></i></button>
            </div>
          <?php else: ?>
            <button class="btn btn--accent btn-add" data-act="inc"><i data-ico="plus"></i> Tambah</button>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
  <?php if ($m['opsi']): ?>
    <div class="menu-opts"><i data-ico="<?= e($m['ikon_opsi'] ?: 'sliders') ?>"></i><span><?= e($m['opsi']) ?></span></div>
  <?php endif; ?>
</article>
