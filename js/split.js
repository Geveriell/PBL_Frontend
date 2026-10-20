/* split.js — simulasi patungan: bagi rata / pilih per item, QR per orang, progres.
   Total & item datang dari PHP lewat data-* di #split. */
(function () {
  const root = document.getElementById('split');
  const total = +root.dataset.total;
  const rate  = +root.dataset.rate;
  const meja  = root.dataset.meja;
  const lines = JSON.parse(root.dataset.lines || '[]');
  const $ = id => document.getElementById(id);

  const MIN = 2, MAX = 12;
  let mode = 'rata';          // 'rata' | 'item'
  let n = 4;                  // jumlah orang
  let sel = 1;                // orang yang QR-nya tampil
  const paid = new Set([0]);  // indeks orang yang sudah lunas (host lunas lebih dulu, seperti desain)

  /* satu baris per porsi, supaya "2× Kopi" bisa dibagi ke dua orang */
  const units = [];
  lines.forEach(l => { for (let i = 0; i < l.qty; i++) units.push({ nama: l.nama, harga: l.harga, owner: units.length }); });
  const ownerOf = u => u.owner % n;

  const name = i => i === 0 ? 'Orang 1 (Anda)' : 'Orang ' + (i + 1) + ' (Tamu ' + String.fromCharCode(65 + i) + ')';
  const esc = t => String(t).replace(/[&<>"']/g, c => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c]));
  const letter = i => String.fromCharCode(65 + i);

  /* ----- hitung nominal tiap orang; Host (orang 1) menanggung selisih pembulatan ----- */
  function amounts() {
    const a = new Array(n).fill(0);
    if (mode === 'rata') {
      const g = Math.floor(total / n / 100) * 100;
      for (let i = 1; i < n; i++) a[i] = g;
      a[0] = total - g * (n - 1);
    } else {
      const raw = new Array(n).fill(0);
      units.forEach(u => { raw[ownerOf(u)] += u.harga; });
      let sum = 0;
      for (let i = 1; i < n; i++) { a[i] = Math.round(raw[i] * (1 + rate) / 100) * 100; sum += a[i]; }
      a[0] = total - sum;
    }
    return a;
  }

  /* ----- render ----- */
  function render() {
    if (sel >= n) sel = n - 1;
    const a = amounts();
    root.dataset.mode = mode;
    $('nVal').textContent = n;
    $('nDec').disabled = n <= MIN;
    $('nInc').disabled = n >= MAX;

    // tab + tombol ubah mode
    document.querySelectorAll('.s-tabs [role=tab]').forEach(t => t.setAttribute('aria-selected', t.dataset.mode === mode));
    $('menuTitle').textContent = mode === 'rata' ? 'Atau Pisahkan Berdasarkan Menu?' : 'Bagi Per Menu';
    $('btnModeText').textContent = mode === 'rata' ? 'Ubah Mode' : 'Bagi Rata';

    // porsi bagi rata
    const g = a[1];
    $('portion').textContent = rp(g);
    const even = a[0] === g;
    $('cleanTitle').textContent = even ? 'Tepat & Bersih' : 'Dibulatkan Rp100';
    $('cleanNote').textContent = even ? 'Tanpa uang kembali' : 'Host: ' + rp(a[0]);

    // QR orang terpilih
    $('qrWho').textContent = name(sel);
    $('qrAmount').textContent = rp(a[sel]);
    drawQR($('qrSvg'), sel + 3);
    $('qrPaid').hidden = !paid.has(sel);
    $('qrSvg').style.opacity = paid.has(sel) ? .12 : 1;
    $('btnMarkPaid').textContent = paid.has(sel) ? 'Batalkan lunas (demo)' : 'Tandai lunas (demo)';

    // daftar orang
    $('listCount').textContent = n + ' Bagian';
    $('people').innerHTML = a.map((amt, i) => {
      const lunas = paid.has(i);
      const sub = lunas ? (i === 0 ? 'QRIS ShopeePay' : 'Lunas') : 'Menunggu bayar';
      const act = lunas
        ? '<span class="pill pill--ok">' + ico('tick') + 'Lunas</span>'
        : '<button class="btn ' + (i === sel ? 'btn--dark' : 'btn--soft') + ' js-qr" data-i="' + i + '">' + ico('qr') + ' QR Saya</button>' +
          '<button class="icon-btn js-link" data-i="' + i + '" aria-label="Bagi tautan ' + name(i) + '">' + ico('link') + '</button>';
      return '<li class="person' + (i === sel ? ' is-sel' : '') + '">' +
        '<span class="avatar">' + letter(i) + '</span>' +
        '<div class="person-info"><b>' + name(i) + (i === 0 ? ' <em class="tag">Host</em>' : '') + '</b>' +
        '<span>' + rp(amt) + ' • ' + sub + '</span></div>' +
        '<div class="person-act">' + act + '</div></li>';
    }).join('');

    // editor per item
    $('units').innerHTML = units.map((u, k) => {
      const opts = a.map((_, i) => '<option value="' + i + '"' + (ownerOf(u) === i ? ' selected' : '') + '>' + name(i) + '</option>').join('');
      return '<li><span class="unit-name">' + esc(u.nama) + '<small>' + rp(u.harga) + '</small></span>' +
        '<select class="unit-pick" data-k="' + k + '" aria-label="Dipesan oleh">' + opts + '</select></li>';
    }).join('');

    // progres
    const done = [...paid].filter(i => i < n);
    const got = done.reduce((s, i) => s + a[i], 0);
    $('progText').textContent = done.length + ' dari ' + n + ' lunas • ' + rp(got) + ' / ' + rp(total);
    $('progBar').style.width = (got / total * 100) + '%';
    const all = done.length === n;
    $('btnDone').setAttribute('aria-disabled', all ? 'false' : 'true');
  }

  /* ----- teks untuk dibagikan ----- */
  function summary() {
    const a = amounts();
    return 'Patungan Meja ' + meja + ' — Bon Surya\nTotal ' + rp(total) + ' (' + n + ' orang)\n\n' +
      a.map((x, i) => '• ' + name(i) + ': ' + rp(x) + (paid.has(i) ? ' ✓ lunas' : '')).join('\n') +
      '\n\nBayar lewat QRIS: ' + location.href.split('#')[0];
  }

  /* ----- event ----- */
  $('nDec').addEventListener('click', () => { if (n > MIN) { n--; render(); } });
  $('nInc').addEventListener('click', () => { if (n < MAX) { n++; render(); } });

  document.querySelector('.s-tabs').addEventListener('click', e => {
    const t = e.target.closest('[role=tab]'); if (!t) return;
    mode = t.dataset.mode; render();
  });
  $('btnMode').addEventListener('click', () => { mode = mode === 'rata' ? 'item' : 'rata'; render(); });

  $('units').addEventListener('change', e => {
    const s = e.target.closest('.unit-pick'); if (!s) return;
    units[+s.dataset.k].owner = +s.value; render();
  });

  $('people').addEventListener('click', async e => {
    const q = e.target.closest('.js-qr');
    const l = e.target.closest('.js-link');
    if (q) { sel = +q.dataset.i; render(); document.querySelector('.s-qr').scrollIntoView({ block: 'nearest', behavior: 'smooth' }); }
    if (l) {
      const url = location.href.split('#')[0] + '#bayar-' + (+l.dataset.i + 1);
      try { await navigator.clipboard.writeText(url); toast('Tautan ' + name(+l.dataset.i) + ' disalin'); }
      catch (_) { toast(url); }
    }
  });

  $('btnMarkPaid').addEventListener('click', () => {
    if (paid.has(sel)) paid.delete(sel); else paid.add(sel);
    render();
  });

  $('btnSendQr').addEventListener('click', async () => {
    const text = name(sel) + ' — bayar ' + rp(amounts()[sel]) + ' untuk Meja ' + meja + '\n' + location.href.split('#')[0];
    if (navigator.share) { try { await navigator.share({ title: 'QRIS Patungan', text }); return; } catch (_) { return; } }
    try { await navigator.clipboard.writeText(text); toast('Detail QR disalin'); } catch (_) { toast('Bagikan tidak didukung di browser ini'); }
  });

  const dlg = $('qrDialog');
  $('btnZoom').addEventListener('click', () => {
    $('dlgWho').textContent = name(sel);
    $('dlgAmount').textContent = rp(amounts()[sel]);
    drawQR($('qrBig'), sel + 3);
    dlg.showModal();
  });
  $('dlgClose').addEventListener('click', () => dlg.close());
  dlg.addEventListener('click', e => { if (e.target === dlg) dlg.close(); });

  $('btnWa').addEventListener('click', () => {
    window.open('https://wa.me/?text=' + encodeURIComponent(summary()), '_blank', 'noopener');
  });

  $('btnDone').addEventListener('click', e => {
    if (e.currentTarget.getAttribute('aria-disabled') === 'true') {
      e.preventDefault();
      toast('Masih ada bagian yang belum lunas');
    }
  });

  render();
})();
