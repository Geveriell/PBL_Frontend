/* menu.js — interaksi menu: qty (lewat api/cart.php), pencarian, chip kategori */
(function () {
  const list = document.getElementById('menuList');
  const cartBar = document.getElementById('cartBar');
  const searchEl = document.getElementById('searchInput');
  const chipsEl = document.getElementById('chips');
  const csrf = document.querySelector('meta[name="csrf-token"]').content;

  /* ----- kontrol qty untuk satu kartu ----- */
  function ctrl(qty) {
    return qty > 0
      ? '<div class="qty"><button data-act="dec" aria-label="Kurangi">' + ico('minus') + '</button>' +
        '<span>' + qty + '</span><button class="plus" data-act="inc" aria-label="Tambah">' + ico('plus') + '</button></div>'
      : '<button class="btn btn--accent btn-add" data-act="inc">' + ico('plus') + ' Tambah</button>';
  }

  /* ----- bar keranjang ----- */
  function updateCart(d) {
    const mejaText = document.getElementById('cartText').textContent.split('•')[1] || '';
    cartBar.hidden = d.items === 0;
    document.getElementById('cartCount').textContent = d.items;
    document.getElementById('cartText').textContent = d.items + ' Item •' + mejaText;
    document.getElementById('cartTotal').textContent = d.totalFmt;
  }

  /* ----- klik tambah / kurang ----- */
  list.addEventListener('click', async e => {
    const b = e.target.closest('[data-act]'); if (!b || b.disabled) return;
    const card = b.closest('.menu-item');
    b.disabled = true;
    try {
      const res = await fetch('api/cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-Token': csrf },
        body: JSON.stringify({ id: +card.dataset.id, action: b.dataset.act })
      });
      const d = await res.json();
      if (!d.ok) throw new Error(d.error || 'Gagal');
      card.querySelector('.buy-ctrl').innerHTML = ctrl(d.qty);
      updateCart(d);
    } catch (err) {
      alert('Keranjang gagal diperbarui: ' + err.message);
      b.disabled = false;
    }
  });

  /* ----- pencarian (filter di browser) ----- */
  searchEl.addEventListener('input', () => {
    const q = searchEl.value.trim().toLowerCase();
    let n = 0;
    list.querySelectorAll('.menu-item').forEach(c => {
      const show = !q || c.dataset.name.includes(q);
      c.hidden = !show; if (show) n++;
    });
    document.getElementById('menuCount').textContent = n + ' Pilihan Tersedia';
  });

  /* ----- chip kategori (visual) ----- */
  chipsEl.addEventListener('click', e => {
    const b = e.target.closest('.chip'); if (!b) return;
    chipsEl.querySelectorAll('.chip').forEach(c => c.classList.toggle('is-active', c === b));
  });
})();
