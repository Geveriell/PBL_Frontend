/* status.js — aksi halaman status pesanan (tampilan tahap dirender oleh PHP) */
(function () {
  const meja = document.querySelector('.app--status').dataset.meja;
  const call = document.getElementById('btnCall');
  let cooling = false;

  /* Panggil pelayan: jeda 30 detik supaya tidak terkirim berulang */
  call.addEventListener('click', () => {
    if (cooling) { toast('Pelayan sudah dipanggil, mohon tunggu sebentar'); return; }
    // TODO: POST ke api/panggil.php
    toast('Pelayan telah dikirim ke Meja ' + meja);
    cooling = true;
    call.classList.add('is-cooling');
    setTimeout(() => { cooling = false; call.classList.remove('is-cooling'); }, 30000);
  });

  document.getElementById('btnReceipt').addEventListener('click', () => {
    // TODO: buka/unduh struk dari api/struk.php
    toast('Struk digital disiapkan (hubungkan ke backend)');
  });
})();
