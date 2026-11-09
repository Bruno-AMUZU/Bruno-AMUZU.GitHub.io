/*
 * Navigation de section de l'accueil (components/section-nav.php).
 * - Marque la section en cours (aria-current="location") dans le rail et dans la liste de la pastille,
 *   et affiche son nom dans la pastille.
 * - Pastille : ouvre et ferme la liste (clic, Échap, clic à l'extérieur, choix d'une section).
 * - Efface la pastille quand le pied de page est visible.
 * Sans ce script, les liens d'ancre fonctionnent quand même.
 */
(() => {
  const nav = document.querySelector('[data-section-nav]');
  if (!nav) return;

  const links = [...nav.querySelectorAll('[data-section-link]')];
  const ids = [...new Set(links.map((link) => link.dataset.sectionLink))];
  const sections = ids.map((id) => document.getElementById(id)).filter(Boolean);
  const current = nav.querySelector('[data-section-current]');
  const toggle = nav.querySelector('[data-section-toggle]');
  const list = nav.querySelector('#section-pill-list');
  if (sections.length === 0) return;

  // Section en cours : la dernière dont le haut a dépassé le tiers supérieur de l'écran.
  let active = null;
  const update = () => {
    const line = window.innerHeight * 0.33;
    let id = sections[0].id;
    for (const section of sections) {
      if (section.getBoundingClientRect().top <= line) id = section.id;
    }
    // Tout en bas de la page, la dernière section est forcément la section en cours.
    if (window.innerHeight + window.scrollY >= document.documentElement.scrollHeight - 2) {
      id = sections[sections.length - 1].id;
    }
    if (id === active) return;
    active = id;
    for (const link of links) {
      if (link.dataset.sectionLink === id) link.setAttribute('aria-current', 'location');
      else link.removeAttribute('aria-current');
    }
    const label = links.find((link) => link.dataset.sectionLink === id)?.textContent.trim();
    if (current && label) current.textContent = label;
  };

  let ticking = false;
  const onScroll = () => {
    if (ticking) return;
    ticking = true;
    requestAnimationFrame(() => {
      update();
      ticking = false;
    });
  };
  window.addEventListener('scroll', onScroll, { passive: true });
  window.addEventListener('resize', onScroll);
  update();

  // Pastille : liste des sections.
  if (toggle && list) {
    const setOpen = (open) => {
      list.hidden = !open;
      toggle.setAttribute('aria-expanded', String(open));
    };
    toggle.addEventListener('click', () => setOpen(list.hidden));
    list.addEventListener('click', (event) => {
      if (event.target.closest('a')) setOpen(false);
    });
    document.addEventListener('click', (event) => {
      if (!list.hidden && !event.target.closest('.section-pill')) setOpen(false);
    });
    document.addEventListener('keydown', (event) => {
      if (event.key === 'Escape' && !list.hidden) {
        setOpen(false);
        toggle.focus();
      }
    });
  }

  // Pied de page visible : la pastille s'efface.
  const footer = document.querySelector('.site-footer, footer');
  if (footer && 'IntersectionObserver' in window) {
    new IntersectionObserver(([entry]) => {
      nav.classList.toggle('is-at-end', entry.isIntersecting);
    }).observe(footer);
  }
})();
