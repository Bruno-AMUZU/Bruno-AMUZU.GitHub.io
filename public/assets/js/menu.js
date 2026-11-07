/*
 * Menu mobile plein écran (Header).
 * Ouverture par [data-menu-open], fermeture par [data-menu-close] ou Échap ; le focus va dans le menu
 * à l'ouverture, revient au bouton à la fermeture, et reste piégé dans le menu tant qu'il est ouvert.
 */
(() => {
  const panel = document.getElementById('menu-mobile');
  const opener = document.querySelector('[data-menu-open]');
  if (!panel || !opener) return;

  const focusables = () => [...panel.querySelectorAll('a[href], button:not([disabled])')].filter((el) => el.offsetParent !== null);

  const open = () => {
    panel.hidden = false;
    opener.setAttribute('aria-expanded', 'true');
    document.documentElement.classList.add('is-menu-open');
    focusables()[0]?.focus();
  };

  const close = () => {
    if (panel.hidden) return;
    panel.hidden = true;
    opener.setAttribute('aria-expanded', 'false');
    document.documentElement.classList.remove('is-menu-open');
    opener.focus();
  };

  opener.addEventListener('click', open);
  panel.addEventListener('click', (event) => {
    if (event.target.closest('[data-menu-close]')) close();
  });

  document.addEventListener('keydown', (event) => {
    if (panel.hidden) return;
    if (event.key === 'Escape') {
      close();
      return;
    }
    if (event.key === 'Tab') {
      const items = focusables();
      const first = items[0];
      const last = items[items.length - 1];
      if (event.shiftKey && document.activeElement === first) {
        event.preventDefault();
        last.focus();
      } else if (!event.shiftKey && document.activeElement === last) {
        event.preventDefault();
        first.focus();
      }
    }
  });

  // Passage en desktop avec le menu ouvert : on le ferme.
  window.matchMedia('(min-width: 768px)').addEventListener('change', (event) => {
    if (event.matches) close();
  });
})();
