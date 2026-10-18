/* icons.js — sprite ikon SVG (outline) + helper ico('nama') */
(function () {
  const paths = {
    translate: '<path d="M4 5h8M8 3v2M5 9c1 2 3 4 6 5M11 5c-1 4-3 7-7 9"/><path d="M13 21l4-10 4 10M14.5 17.5h5"/>',
    user: '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 4-6 8-6s8 2 8 6z"/>',
    search: '<circle cx="11" cy="11" r="7"/><path d="M21 21l-5-5"/>',
    verified: '<path d="M12 2l2.5 2 3-.3 1 3 2.6 1.6-1 3 1 3-2.6 1.6-1 3-3-.3L12 22l-2.5-2-3 .3-1-3L2.9 15.7l1-3-1-3L5.5 8l1-3 3 .3z"/><path d="M8.5 12l2.5 2.5 4.5-5"/>',
    home: '<path d="M3 21h18M5 21V10l7-5 7 5v11M9 21v-6h6v6"/>',
    star: '<path d="M12 3l2.7 5.6 6.1.9-4.4 4.3 1 6.1L12 17l-5.4 2.9 1-6.1L3.2 9.5l6.1-.9z"/>',
    sliders: '<path d="M4 6h10M18 6h2M4 12h4M12 12h8M4 18h12M20 18h0"/><circle cx="16" cy="6" r="2"/><circle cx="10" cy="12" r="2"/><circle cx="18" cy="18" r="2"/>',
    utensils: '<path d="M7 3v8M4 3v5a3 3 0 006 0V3M7 11v10M17 21V3c-3 1-4 5-4 9h4"/>',
    flame: '<path d="M12 22c4 0 7-3 7-7 0-4-3-6-4-10-3 2-4 4-4 6-1-1-2-2-2-4-3 3-4 6-4 8 0 4 3 7 7 7z"/>',
    snow: '<path d="M12 2v20M3.3 7l17.4 10M3.3 17L20.7 7M9 4l3 2 3-2M9 20l3-2 3 2"/>',
    plus: '<path d="M12 5v14M5 12h14"/>',
    minus: '<path d="M5 12h14"/>',
    basket: '<path d="M3 10h18l-2 10H5zM8 10l3-6M16 10l-3-6"/>',
    arrow: '<path d="M5 12h14M13 6l6 6-6 6"/>',
    back: '<path d="M19 12H5M11 6l-6 6 6 6"/>',
    cup: '<path d="M4 8h13v7a5 5 0 01-5 5H9a5 5 0 01-5-5zM17 10h2a2 2 0 010 5h-2M3 22h15"/>',
    bag: '<path d="M5 8h14l-1 13H6zM9 8a3 3 0 016 0"/>',
    receipt: '<path d="M6 2h12v20l-3-2-3 2-3-2-3 2zM9 7h6M9 11h6M9 15h4"/>',
    crossed: '<path d="M4 4l16 16M20 4L4 20"/>',
    stopwatch: '<circle cx="12" cy="13" r="8"/><path d="M12 9v4l3 2M9 2h6"/>',
    table: '<path d="M4 9h16M6 9v11M18 9v11M4 9l1-4h14l1 4"/>',
    download: '<path d="M12 3v12M7 10l5 5 5-5M4 20h16"/>',
    group: '<circle cx="9" cy="8" r="3.5"/><path d="M2 21c0-4 3-6 7-6s7 2 7 6M16 4.5a3.5 3.5 0 010 7M18 15c2.5.6 4 2.4 4 6"/>',
    refresh: '<path d="M20 11a8 8 0 00-14-4L4 9M4 4v5h5M4 13a8 8 0 0014 4l2-2M20 20v-5h-5"/>',
    bell: '<path d="M6 17V11a6 6 0 0112 0v6l2 2H4zM10 21h4"/>',
    check: '<path d="M12 2l2.5 2 3-.3 1 3 2.6 1.6-1 3 1 3-2.6 1.6-1 3-3-.3L12 22l-2.5-2-3 .3-1-3L2.9 15.7l1-3-1-3L5.5 8l1-3 3 .3z"/><path d="M8.5 12l2.5 2.5 4.5-5"/>'
  };

  let defs = '';
  for (const k in paths) defs += '<symbol id="i-' + k + '" viewBox="0 0 24 24">' + paths[k] + '</symbol>';

  const sprite = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
  sprite.setAttribute('style', 'display:none');
  sprite.innerHTML = defs;
  document.body.prepend(sprite);

  window.ico = (name, cls) =>
    '<svg class="ico ' + (cls || '') + '" aria-hidden="true"><use href="#i-' + name + '"/></svg>';

  // Isi semua <i data-ico="nama"> yang ada di HTML statis
  document.querySelectorAll('[data-ico]').forEach(el => { el.innerHTML = window.ico(el.dataset.ico); });
})();
