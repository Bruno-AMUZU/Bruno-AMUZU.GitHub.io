/*
 * Apparition au défilement (toutes les pages du site public).
 * Les cartes des grilles (.grid > *) encore sous l'écran au chargement sont préparées (.reveal),
 * puis montent et apparaissent en cascade quand elles entrent à l'écran (.is-in).
 * Ce qui est déjà visible au chargement n'est jamais masqué ; sans ce script, tout reste visible.
 * Rien ne bouge si le système demande de réduire les animations.
 */
(() => {
  if (!('IntersectionObserver' in window)) return;
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const items = [...document.querySelectorAll('.page-main .grid > *')];
  const below = items.filter((item) => item.getBoundingClientRect().top > window.innerHeight);
  if (below.length === 0) return;

  // Décalage en cascade selon la position de la carte dans sa grille (4 crans au maximum).
  for (const item of below) {
    const index = [...item.parentElement.children].indexOf(item);
    item.style.setProperty('--reveal-delay', `${Math.min(index, 3) * 80}ms`);
    item.classList.add('reveal');
  }

  const observer = new IntersectionObserver((entries) => {
    for (const entry of entries) {
      if (!entry.isIntersecting) continue;
      const item = entry.target;
      item.classList.add('is-in');
      observer.unobserve(item);
      // Animation terminée : on retire les classes, pour que le délai de cascade ne retarde pas le survol.
      const delay = parseInt(item.style.getPropertyValue('--reveal-delay'), 10) || 0;
      setTimeout(() => {
        item.classList.remove('reveal', 'is-in');
        item.style.removeProperty('--reveal-delay');
      }, delay + 800);
    }
  }, { rootMargin: '0px 0px -10% 0px' });

  below.forEach((item) => observer.observe(item));
})();
