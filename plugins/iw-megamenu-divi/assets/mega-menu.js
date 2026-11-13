/* Mantiene el panel abierto al cruzar el espacio interior de la cabecera. */
(() => {
  'use strict';
  const desktop = window.matchMedia('(min-width: 981px)');
  const selector = '#mod-header .et-menu > li.iw-mega-menu';
  let active = null;
  let timer;

  const close = () => {
    window.clearTimeout(timer);
    if (active) {
      active.classList.remove('iw-mega-open');
      const link = active.querySelector(':scope > a');
      if (link) link.setAttribute('aria-expanded', 'false');
    }
    active = null;
  };

  // Delegación: también funciona si Divi reconstruye la cabecera sticky.
  document.addEventListener('pointerover', (event) => {
    if (!desktop.matches || event.pointerType === 'touch') return;
    const target = event.target;
    if (!(target instanceof Element)) return;
    const item = target.closest(selector);
    if (item) {
      window.clearTimeout(timer);
      if (active !== item) close();
      active = item;
      active.classList.remove('iw-mega-dismissed');
      active.classList.add('iw-mega-open');
      const link = active.querySelector(':scope > a');
      if (link) link.setAttribute('aria-expanded', 'true');
      return;
    }
    if (!active) return;
    const header = active.closest('#mod-header');
    if (header.contains(target)) {
      // Abrir otro elemento principal cierra el mega menú inmediatamente.
      const other = target.closest('.et-menu > li');
      if (other && other !== active) close();
      else window.clearTimeout(timer);
    }
  });

  document.addEventListener('pointerout', (event) => {
    if (!active) return;
    const header = active.closest('#mod-header');
    if (event.relatedTarget instanceof Node && header.contains(event.relatedTarget)) return;
    window.clearTimeout(timer);
    timer = window.setTimeout(close, 250);
  });

  document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape' && active) {
      const dismissed = active;
      close();
      dismissed.classList.add('iw-mega-dismissed');
    }
  });
  document.addEventListener('focusin', (event) => {
    if (!desktop.matches) return;
    const item = event.target.closest(selector);
    if (active && active !== item) close();
    if (item) {
      window.clearTimeout(timer);
      active = item;
      active.classList.remove('iw-mega-dismissed');
      active.classList.add('iw-mega-open');
      const link = active.querySelector(':scope > a');
      if (link) link.setAttribute('aria-expanded', 'true');
    }
  });
  document.addEventListener('click', (event) => {
    if (active && !active.contains(event.target)) close();
  });
  desktop.addEventListener('change', close);
  window.addEventListener('blur', close);
})();
