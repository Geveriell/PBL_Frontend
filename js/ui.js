/* ui.js — helper bersama: window.rp(), window.toast(), window.drawQR() */
(function () {
  /* Format rupiah: 22000 -> "Rp 22.000" */
  window.rp = n => 'Rp ' + String(Math.round(n)).replace(/\B(?=(\d{3})+(?!\d))/g, '.');

  /* Notifikasi singkat di bawah layar */
  let box, timer;
  window.toast = msg => {
    if (!box) {
      box = document.createElement('div');
      box.className = 'toast';
      box.setAttribute('role', 'status');
      box.setAttribute('aria-live', 'polite');
      document.body.appendChild(box);
    }
    box.textContent = msg;
    box.classList.add('is-on');
    clearTimeout(timer);
    timer = setTimeout(() => box.classList.remove('is-on'), 2600);
  };

  /* QR placeholder (25x25). Ganti dengan gambar QRIS dari payment gateway.
     seed berbeda -> pola berbeda, jadi tiap orang punya QR sendiri. */
  window.drawQR = (svgEl, seed) => {
    const N = 25; let out = '', s = (seed || 7) * 7919 % 2147483647 || 7;
    const rnd = () => (s = (s * 16807) % 2147483647) / 2147483647;
    const inFinder = (x, y) => (x < 8 && y < 8) || (x >= N - 8 && y < 8) || (x < 8 && y >= N - 8);
    for (let y = 0; y < N; y++) for (let x = 0; x < N; x++)
      if (!inFinder(x, y) && rnd() > 0.52) out += '<rect x="' + x + '" y="' + y + '" width="1" height="1"/>';
    [[0, 0], [N - 7, 0], [0, N - 7]].forEach(([fx, fy]) => {
      out += '<rect x="' + fx + '" y="' + fy + '" width="7" height="7"/>' +
             '<rect x="' + (fx + 1) + '" y="' + (fy + 1) + '" width="5" height="5" fill="#fff"/>' +
             '<rect x="' + (fx + 2) + '" y="' + (fy + 2) + '" width="3" height="3"/>';
    });
    svgEl.setAttribute('viewBox', '0 0 ' + N + ' ' + N);
    svgEl.setAttribute('fill', '#111');
    svgEl.setAttribute('shape-rendering', 'crispEdges');
    svgEl.innerHTML = out;
  };
})();
