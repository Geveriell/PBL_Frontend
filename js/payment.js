/* payment.js — timer, QR placeholder, aksi tombol (nilai uang dirender oleh PHP) */
(function () {
  const root = document.querySelector('.app--pay');
  const batas = +root.dataset.batas;
  let sisa = +root.dataset.sisa;
  const meja = root.dataset.meja;

  /* ----- timer hitung mundur ----- */
  const clock = document.getElementById('clock');
  const bar = document.getElementById('timerBar');
  function tick() {
    const m = String(Math.floor(sisa / 60)).padStart(2, '0');
    const s = String(sisa % 60).padStart(2, '0');
    clock.textContent = m + ':' + s;
    bar.style.width = (sisa / batas * 100) + '%';
    if (sisa <= 0) { clearInterval(t); return; }
    sisa--;
  }
  tick();
  const t = setInterval(tick, 1000);

  /* ----- QR placeholder (js/ui.js); hapus bila memakai gambar QRIS dari payment gateway ----- */
  drawQR(document.getElementById('qrGrid'), 7);

  /* ----- aksi (sambungkan ke api/ saat backend siap) ----- */
  document.getElementById('btnSave').addEventListener('click', () => toast('Gambar QR disimpan (hubungkan ke backend)'));
  document.getElementById('btnCheck').addEventListener('click', () => {
    toast('Memeriksa pembayaran…');
    // TODO: panggil api/order-status.php, lalu pindah ke status.php bila lunas
    setTimeout(() => { location.href = 'status.php'; }, 1200);
  });
  document.getElementById('btnCall').addEventListener('click', e => { e.preventDefault(); toast('Kasir dipanggil ke Meja ' + meja); });
})();
