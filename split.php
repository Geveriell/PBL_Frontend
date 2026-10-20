<?php
declare(strict_types=1);
require_once __DIR__ . '/includes/bootstrap.php';

$meja = meja();
$cart = Cart::summary();

// Keranjang kosong → kembali ke menu
if ($cart['items'] === 0) {
    header('Location: index.php');
    exit;
}

$lines   = Cart::lines();
$ref     = Cart::ref();
$persen  = (int) round(PAJAK_SERVIS * 100);
$title   = 'Split Bill';
$cssPage = 'split';
$jsPage  = 'split';

include __DIR__ . '/includes/head.php';
?>
  <main class="app app--split" id="split" data-mode="rata"
        data-total="<?= $cart['total'] ?>" data-rate="<?= PAJAK_SERVIS ?>"
        data-meja="<?= e($meja) ?>" data-ref="<?= e($ref) ?>"
        data-lines="<?= e(json_encode($lines, JSON_UNESCAPED_UNICODE) ?: '[]') ?>">

    <header class="pay-top">
      <a class="back" href="pembayaran.php" aria-label="Kembali ke pembayaran"><i data-ico="back"></i></a>
      <div class="pay-title"><b>MEJA <?= e($meja) ?></b><span>Payment Method</span></div>
      <span class="table-badge"><i data-ico="table"></i><?= e($meja) ?></span>
      <button class="icon-btn icon-btn--accent" aria-label="Akun"><i data-ico="user"></i></button>
    </header>

    <!-- Ponsel: satu kolom (urutan lewat CSS order). Desktop: kiri = pengaturan, kanan = QR & daftar. -->
    <div class="split-layout">

      <div class="split-col split-col--set">

        <section class="card s-total">
          <div>
            <small>TOTAL TAGIHAN MEJA <?= e($meja) ?></small>
            <strong><?= rp($cart['total']) ?></strong>
            <span class="s-count">(<?= $cart['items'] ?> Pesanan)</span>
            <p>Sudah termasuk PB1 &amp; servis <?= $persen ?>%.</p>
          </div>
          <span class="s-total-ico"><i data-ico="split"></i></span>
        </section>

        <div class="seg s-tabs" role="tablist" aria-label="Cara membagi tagihan">
          <button role="tab" id="tabRata" aria-selected="true" data-mode="rata"><i data-ico="group"></i>Bagi Rata</button>
          <button role="tab" id="tabItem" aria-selected="false" data-mode="item"><i data-ico="utensils"></i>Pilih per Item</button>
        </div>

        <section class="card s-sim">
          <div class="s-sim-head">
            <div>
              <small>SIMULASI PATUNGAN</small>
              <b>Berapa orang yang bayar?</b>
            </div>
            <div class="stepper" role="group" aria-label="Jumlah orang">
              <button id="nDec" aria-label="Kurangi orang"><i data-ico="minus"></i></button>
              <output id="nVal" aria-live="polite">4</output>
              <button id="nInc" class="plus" aria-label="Tambah orang"><i data-ico="plus"></i></button>
            </div>
          </div>
          <div class="s-portion only-rata">
            <span class="s-portion-ico"><i data-ico="wallet"></i></span>
            <div class="s-portion-main"><small>Porsi Pembayaran</small><b id="portion">—</b><span> / orang</span></div>
            <div class="s-clean"><b id="cleanTitle">Tepat &amp; Bersih</b><span id="cleanNote">Tanpa uang kembali</span></div>
          </div>
          <p class="s-hint only-item">Tentukan siapa yang memesan tiap item di bagian “Bagi Per Menu” di bawah.</p>
        </section>

        <section class="card s-menu">
          <div class="s-menu-head">
            <h2><i data-ico="receipt"></i><span id="menuTitle">Atau Pisahkan Berdasarkan Menu?</span></h2>
            <button class="link-btn" id="btnMode"><i data-ico="swap"></i><span id="btnModeText">Ubah Mode</span></button>
          </div>

          <div class="only-rata">
            <p class="s-menu-text"><?php
              echo e(implode(', ', array_map(fn($l) => $l['nama'] . ($l['qty'] > 1 ? ' ×' . $l['qty'] : '') . ' (' . rp($l['qty'] * $l['harga']) . ')', $lines)));
            ?></p>
            <ul class="mini-chips">
              <?php foreach ($lines as $l): ?><li><?= e($l['nama']) ?> (<?= $l['qty'] ?>)</li><?php endforeach; ?>
            </ul>
          </div>

          <div class="only-item">
            <p class="s-hint">Pilih siapa yang memesan tiap item. PB1 &amp; servis <?= $persen ?>% ikut dibagi sesuai pesanan.</p>
            <ul class="units" id="units"></ul>
          </div>
        </section>

      </div>

      <div class="split-col split-col--pay">

        <section class="card s-qr">
          <div class="s-qr-head">
            <h2><span class="dot"></span>QRIS Patungan Siap Dipindai</h2>
            <em id="qrWho">Orang 2 (Tamu B)</em>
          </div>
          <div class="s-qr-body">
            <div class="qr-mini">
              <svg id="qrSvg" viewBox="0 0 25 25" role="img" aria-label="QRIS patungan"></svg>
              <span class="qr-paid" id="qrPaid" hidden><i data-ico="tick"></i>Lunas</span>
            </div>
            <div class="s-qr-info">
              <small>Nominal Terkunci</small>
              <strong id="qrAmount">—</strong>
              <p>Dukungan: BCA, GoPay, OVO, Dana</p>
            </div>
          </div>
          <div class="s-qr-actions">
            <button class="btn btn--dark" id="btnSendQr"><i data-ico="share"></i> Kirim QR</button>
            <button class="btn btn--soft" id="btnZoom"><i data-ico="expand"></i> Perbesar</button>
            <!-- Demo: di produksi status lunas datang dari payment gateway (webhook / polling) -->
            <button class="link-btn" id="btnMarkPaid">Tandai lunas (demo)</button>
          </div>
        </section>

        <section class="card s-list">
          <div class="s-list-head">
            <h2>Daftar Patungan Meja</h2>
            <small id="listCount">4 Bagian</small>
          </div>
          <ul class="people" id="people"></ul>
        </section>

        <button class="card s-share" id="btnWa">
          <span class="s-share-ico"><i data-ico="chat"></i></span>
          <span class="s-share-text"><b>Bagikan Rincian Meja <?= e($meja) ?></b><small>Kirim teks &amp; tautan QR ke WhatsApp Grup</small></span>
          <i data-ico="arrow"></i>
        </button>

      </div>
    </div>
  </main>

  <div class="s-progress">
    <div class="s-progress-in">
      <div class="s-progress-text">
        <b>Progress Pembayaran</b>
        <span id="progText">1 dari 4 lunas</span>
        <div class="bar"><i id="progBar"></i></div>
      </div>
      <a class="btn btn--accent" id="btnDone" href="status.php" aria-disabled="true">Lihat Status <i data-ico="arrow"></i></a>
    </div>
  </div>

  <dialog id="qrDialog" aria-labelledby="dlgWho">
    <div class="dlg">
      <button class="icon-btn dlg-close" id="dlgClose" aria-label="Tutup"><i data-ico="close"></i></button>
      <b id="dlgWho">Orang 2 (Tamu B)</b>
      <div class="qr-big"><svg id="qrBig" viewBox="0 0 25 25"></svg></div>
      <strong id="dlgAmount">—</strong>
      <small>Scan dengan m-Banking atau dompet digital apa saja</small>
    </div>
  </dialog>

<?php include __DIR__ . '/includes/footer.php'; ?>
