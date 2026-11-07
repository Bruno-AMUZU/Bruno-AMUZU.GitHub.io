/*
 * Back-office : menu mobile de la barre latérale, confirmation des suppressions,
 * onglets de langue, ajout de technos, aperçu des images et réordonnancement des listes.
 * Chaque fonction ne s'active que si les éléments concernés sont présents dans la page.
 */
(() => {
  document.documentElement.classList.add('js');

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

  // ── Onglets FR / EN ─────────────────────────────────────
  const tabs = [...document.querySelectorAll('[data-tab]')];
  const panels = [...document.querySelectorAll('[data-panel]')];
  const select = (lang) => {
    tabs.forEach((tab) => tab.setAttribute('aria-selected', String(tab.dataset.tab === lang)));
    panels.forEach((panel) => { panel.hidden = panel.dataset.panel !== lang; });
  };
  if (tabs.length) {
    // Ouvre la langue qui contient une erreur, sinon le français.
    const withError = panels.find((panel) => panel.querySelector('.field--error'));
    select(withError ? withError.dataset.panel : tabs[0].dataset.tab);
    tabs.forEach((tab) => tab.addEventListener('click', () => select(tab.dataset.tab)));
    document.querySelector('[role="tablist"]')?.addEventListener('keydown', (event) => {
      if (event.key !== 'ArrowRight' && event.key !== 'ArrowLeft') return;
      const index = tabs.indexOf(document.activeElement);
      const next = tabs[(index + (event.key === 'ArrowRight' ? 1 : tabs.length - 1)) % tabs.length];
      next.focus();
      select(next.dataset.tab);
    });
  }

  // ── Technos : Entrée ou virgule ajoute une puce ─────────
  document.querySelectorAll('[data-tags]').forEach((box) => {
    const name = box.dataset.tags;
    const list = box.querySelector('.admin-tags__list');
    const input = box.querySelector(`input[name="${name}_new"]`);

    const add = (value) => {
      value = value.trim();
      if (!value || [...list.querySelectorAll('input')].some((i) => i.value.toLowerCase() === value.toLowerCase())) return;
      const item = document.createElement('li');
      item.className = 'tag t-caption admin-tags__item';
      const hidden = Object.assign(document.createElement('input'), { type: 'hidden', name: `${name}[]`, value });
      const remove = Object.assign(document.createElement('button'), { type: 'button', className: 'admin-tags__remove', textContent: '×' });
      remove.setAttribute('aria-label', `Retirer ${value}`);
      item.append(hidden, document.createTextNode(value + ' '), remove);
      list.append(item);
    };

    input?.addEventListener('keydown', (event) => {
      if (event.key === 'Enter' || event.key === ',') {
        event.preventDefault();
        input.value.split(',').forEach(add);
        input.value = '';
      }
    });
    list.addEventListener('click', (event) => {
      if (event.target.closest('.admin-tags__remove')) event.target.closest('li').remove();
    });
  });

  // ── Aperçu de l'image choisie ───────────────────────────
  document.querySelectorAll('.dropzone input[type=file]').forEach((input) => {
    const zone = input.closest('.dropzone');
    const preview = zone.querySelector('[data-preview]');
    input.addEventListener('change', () => {
      const file = input.files[0];
      if (preview && file && file.type.startsWith('image/')) {
        preview.src = URL.createObjectURL(file);
        preview.hidden = false;
      }
      zone.querySelector('.muted').textContent = [...input.files].map((f) => f.name).join(', ') || zone.querySelector('.muted').textContent;
    });
    ['dragenter', 'dragover'].forEach((type) => zone.addEventListener(type, () => zone.classList.add('is-over')));
    ['dragleave', 'drop'].forEach((type) => zone.addEventListener(type, () => zone.classList.remove('is-over')));
  });

  // ── Réordonnancement par glisser-déposer ────────────────
  const table = document.querySelector('[data-reorder]');
  if (table) {
    let dragged = null;
    const token = document.querySelector('input[name="_csrf"]')?.value ?? '';

    table.addEventListener('dragstart', (event) => {
      dragged = event.target.closest('.data-row');
      dragged?.classList.add('is-dragging');
    });
    table.addEventListener('dragover', (event) => {
      event.preventDefault();
      const target = event.target.closest('.data-row');
      if (!dragged || !target || target === dragged) return;
      const after = event.clientY > target.getBoundingClientRect().top + target.offsetHeight / 2;
      target.parentNode.insertBefore(dragged, after ? target.nextSibling : target);
    });
    table.addEventListener('dragend', () => {
      dragged?.classList.remove('is-dragging');
      dragged = null;
      const body = new URLSearchParams();
      table.querySelectorAll('.data-row').forEach((row) => body.append('ids[]', row.dataset.id));
      fetch(table.dataset.reorder, { method: 'POST', headers: { 'X-CSRF-Token': token }, body });
    });
  }
})();
