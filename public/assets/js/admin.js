/*
 * Back-office : menu mobile de la barre latérale, confirmation des suppressions,
 * onglets de langue, ajout de technos, aperçu des images, réordonnancement des listes
 * et choix d'un logo avec recherche.
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
  // ── Choix d'un logo : liste avec recherche et aperçu ───
  // Remplace la liste déroulante (gardée pour l'envoi du formulaire) par un champ de recherche
  // et une liste de logos (rôles ARIA combobox / listbox, navigation au clavier).
  document.querySelectorAll('[data-logo-picker]').forEach((field, n) => {
    const select = field.querySelector('select');
    if (!select) return;
    const logoUrl = (key) => `/assets/img/tech/${key}.svg`;
    const options = [...select.options].map((option) => ({
      value: option.value,
      label: option.textContent.trim(),
      search: `${option.value} ${option.dataset.aliases ?? ''}`.toLowerCase(),
    }));

    const control = document.createElement('div');
    control.className = 'logo-picker';
    control.innerHTML = `
      <div class="logo-picker__control field__input">
        <span class="logo-picker__preview" aria-hidden="true"></span>
        <input type="text" class="logo-picker__input t-body" role="combobox" aria-autocomplete="list"
               aria-expanded="false" autocomplete="off" spellcheck="false">
      </div>
      <ul class="logo-picker__list" role="listbox" hidden></ul>
      <p class="logo-picker__empty t-small muted" hidden></p>`;
    select.hidden = true;
    select.after(control);

    const input = control.querySelector('input');
    const preview = control.querySelector('.logo-picker__preview');
    const list = control.querySelector('ul');
    const empty = control.querySelector('.logo-picker__empty');
    const listId = `${select.id}-list`;
    list.id = listId;
    input.id = `${select.id}-search`;
    input.setAttribute('aria-controls', listId);
    input.placeholder = field.dataset.searchLabel ?? '';
    empty.textContent = field.dataset.noResults ?? '';
    field.querySelector('label')?.setAttribute('for', input.id);

    list.innerHTML = options.map((option, i) => `
      <li class="logo-picker__option" role="option" id="${listId}-${i}" data-index="${i}">
        <span class="logo-picker__logo">${option.value ? `<img src="${logoUrl(option.value)}" alt="" loading="lazy" width="24" height="24">` : ''}</span>
        <span class="t-body">${option.label.replace(/[&<>"]/g, (c) => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;' }[c]))}</span>
      </li>`).join('');
    const items = [...list.children];
    let visible = items;
    let active = -1;

    const current = () => options[select.selectedIndex] ?? options[0];
    const showSelected = () => {
      const option = current();
      input.value = option.value ? option.label : '';
      preview.innerHTML = option.value ? `<img src="${logoUrl(option.value)}" alt="" width="24" height="24">` : '';
      items.forEach((item, i) => item.setAttribute('aria-selected', String(i === select.selectedIndex)));
    };
    const setActive = (index) => {
      visible[active]?.classList.remove('is-active');
      active = index;
      const item = visible[active];
      if (!item) {
        input.removeAttribute('aria-activedescendant');
        return;
      }
      item.classList.add('is-active');
      input.setAttribute('aria-activedescendant', item.id);
      item.scrollIntoView({ block: 'nearest' });
    };
    const filter = () => {
      const query = input.value.trim().toLowerCase();
      // Les noms qui commencent par la recherche d'abord, puis ceux qui la contiennent.
      const starts = [], contains = [];
      options.forEach((option, i) => {
        const match = query === '' ? 1 : option.search.split(' ').some((word) => word.startsWith(query)) ? 1 : option.search.includes(query) ? 2 : 0;
        items[i].hidden = match === 0;
        if (match === 1) starts.push(items[i]);
        if (match === 2) contains.push(items[i]);
      });
      visible = [...starts, ...contains];
      visible.forEach((item) => list.appendChild(item));
      if (query === '') items.forEach((item) => list.appendChild(item));
      empty.hidden = visible.length > 0;
      setActive(visible.length > 0 ? 0 : -1);
    };
    const open = () => {
      if (!list.hidden) return;
      list.hidden = false;
      input.setAttribute('aria-expanded', 'true');
      filter();
      const selected = items[select.selectedIndex];
      if (input.value === current().label && selected) {
        input.select();
        visible = items.filter((item) => !item.hidden);
        setActive(visible.indexOf(selected));
      }
    };
    const close = () => {
      list.hidden = true;
      empty.hidden = true;
      input.setAttribute('aria-expanded', 'false');
      setActive(-1);
      showSelected();
    };
    const choose = (item) => {
      if (!item) return;
      select.selectedIndex = Number(item.dataset.index);
      select.dispatchEvent(new Event('change', { bubbles: true }));
      close();
    };

    input.addEventListener('focus', open);
    input.addEventListener('click', open);
    input.addEventListener('input', () => {
      if (list.hidden) open();
      filter();
    });
    input.addEventListener('keydown', (event) => {
      if (event.key === 'ArrowDown' || event.key === 'ArrowUp') {
        event.preventDefault();
        if (list.hidden) return open();
        const step = event.key === 'ArrowDown' ? 1 : -1;
        setActive(Math.max(0, Math.min(visible.length - 1, active + step)));
      } else if (event.key === 'Enter') {
        if (!list.hidden) {
          event.preventDefault(); // ne pas envoyer le formulaire en choisissant un logo
          choose(visible[active]);
        }
      } else if (event.key === 'Escape') {
        if (!list.hidden) {
          event.preventDefault();
          close();
        }
      } else if (event.key === 'Tab') {
        close();
      }
    });
    list.addEventListener('mousedown', (event) => event.preventDefault()); // garde le focus dans le champ
    list.addEventListener('click', (event) => choose(event.target.closest('[role="option"]')));
    control.addEventListener('focusout', (event) => {
      if (!control.contains(event.relatedTarget)) close();
    });

    showSelected();
  });
})();
