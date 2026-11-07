/*
 * Bascule de thème clair / sombre.
 *
 * - Sans choix du visiteur, le thème suit le réglage du système (prefers-color-scheme, dans tokens.css).
 * - Un clic sur un bouton [data-theme-toggle] passe à l'autre thème et le mémorise (localStorage).
 * - Le thème mémorisé est appliqué dans <head> avant l'affichage (voir le gabarit) : pas de flash.
 * - L'icône montre l'action : lune = passer en sombre, soleil = passer en clair.
 */
(() => {
  const root = document.documentElement;
  const system = window.matchMedia('(prefers-color-scheme: dark)');

  const current = () => root.dataset.theme || (system.matches ? 'dark' : 'light');

  const sync = () => {
    const dark = current() === 'dark';
    document.querySelectorAll('[data-theme-toggle]').forEach((button) => {
      button.hidden = false; // sans JavaScript, le bouton reste masqué (il ne servirait à rien)
      button.dataset.state = dark ? 'dark' : 'light';
      button.setAttribute('aria-label', dark ? button.dataset.labelToLight : button.dataset.labelToDark);
    });
  };

  document.addEventListener('click', (event) => {
    const button = event.target.closest('[data-theme-toggle]');
    if (!button) return;

    const next = current() === 'dark' ? 'light' : 'dark';
    root.dataset.theme = next;
    try {
      localStorage.setItem('theme', next);
    } catch {
      /* stockage indisponible (navigation privée) : le choix vaut pour la page en cours */
    }
    sync();
  });

  system.addEventListener('change', sync);
  sync();
})();
