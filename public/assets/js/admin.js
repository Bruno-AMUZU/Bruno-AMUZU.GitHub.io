/*
 * Back-office : menu mobile de la barre latérale, confirmation des suppressions,
 * onglets de langue, ajout de technos, aperçu des images et réordonnancement des listes.
 * Chaque fonction ne s'active que si les éléments concernés sont présents dans la page.
 */
(() => {
  // ── Menu mobile ─────────────────────────────────────────
  const menuButton = document.querySelector('[data-admin-menu]');
  const sidebar = document.querySelector('.admin-sidebar');
  if (menuButton && sidebar) {
    menuButton.addEventListener('click', () => {
      const open = sidebar.classList.toggle('is-open');
      menuButton.setAttribute('aria-expanded', String(open));
    });
  }

  // ── Confirmation avant une action irréversible ──────────
  document.addEventListener('submit', (event) => {
    const message = event.target.dataset.confirm;
    if (message && !window.confirm(message)) {
      event.preventDefault();
    }
  });
})();
