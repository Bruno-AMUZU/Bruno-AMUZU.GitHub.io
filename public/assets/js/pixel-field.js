/*
 * Fond de pixels du site public (toutes les pages sauf la 404, qui a ses propres pixels).
 * Un canvas fixé derrière tout le contenu : quelques pixels accent s'allument au hasard et s'éteignent
 * doucement ; à la souris, ils s'allument aussi autour du curseur. Les cartes, opaques, glissent par-dessus :
 * les pixels n'apparaissent que dans le fond, entre elles. Pas de souris (mobile) : scintillement seul.
 * Mis en pause quand l'onglet est caché. Rien ne s'affiche si le système demande de réduire les animations.
 */
(() => {
  if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;
  if (document.querySelector('.error-page--404')) return;

  const CELL = 16;          // taille d'une case (px)
  const GAP = 4;            // espace entre deux pixels
  const DENSITY = 0.22;     // pixels allumés par image pour 300 000 px² d'écran (peu de pixels)
  const FADE = 0.975;       // vitesse d'extinction
  const MAX_ALPHA = 0.5;    // opacité maximale
  const RADIUS = 3;         // rayon autour du curseur, en cases

  const canvas = document.createElement('canvas');
  canvas.className = 'pixel-field';
  canvas.setAttribute('aria-hidden', 'true');
  document.body.prepend(canvas);
  const ctx = canvas.getContext('2d');

  // Couleur accent lue dans les tokens (#C084FC → [192, 132, 252]).
  const hex = getComputedStyle(document.documentElement).getPropertyValue('--color-accent').trim().replace('#', '');
  const rgb = hex.length === 6 ? [0, 2, 4].map((i) => parseInt(hex.slice(i, i + 2), 16)) : [192, 132, 252];

  let width = 0, height = 0, cols = 0, rows = 0, cells = new Float32Array(0), spawn = 0;
  let mouse = null, frame = null;

  const resize = () => {
    const ratio = Math.min(window.devicePixelRatio || 1, 2);
    width = window.innerWidth;
    height = window.innerHeight;
    canvas.width = Math.round(width * ratio);
    canvas.height = Math.round(height * ratio);
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    cols = Math.ceil(width / CELL);
    rows = Math.ceil(height / CELL);
    cells = new Float32Array(cols * rows);
    spawn = DENSITY * Math.max(0.5, (width * height) / 300000);
  };

  const draw = () => {
    for (let n = spawn; n > 0; n -= 1) {
      if (Math.random() < n) cells[(Math.random() * cells.length) | 0] = 1;
    }

    if (mouse) {
      const cx = mouse.x / CELL, cy = mouse.y / CELL;
      for (let y = Math.max(0, Math.floor(cy - RADIUS)); y < Math.min(rows, Math.ceil(cy + RADIUS)); y++) {
        for (let x = Math.max(0, Math.floor(cx - RADIUS)); x < Math.min(cols, Math.ceil(cx + RADIUS)); x++) {
          const distance = Math.hypot(x + 0.5 - cx, y + 0.5 - cy);
          if (distance < RADIUS && Math.random() < 0.08) {
            const i = y * cols + x;
            cells[i] = Math.max(cells[i], 1 - distance / RADIUS);
          }
        }
      }
    }

    ctx.clearRect(0, 0, width, height);
    for (let i = 0; i < cells.length; i++) {
      const value = cells[i];
      if (value < 0.02) {
        cells[i] = 0;
        continue;
      }
      ctx.fillStyle = `rgba(${rgb[0]}, ${rgb[1]}, ${rgb[2]}, ${(value * MAX_ALPHA).toFixed(3)})`;
      ctx.fillRect((i % cols) * CELL + GAP / 2, Math.floor(i / cols) * CELL + GAP / 2, CELL - GAP, CELL - GAP);
      cells[i] = value * FADE;
    }
    frame = requestAnimationFrame(draw);
  };

  const start = () => {
    if (frame === null && !document.hidden) frame = requestAnimationFrame(draw);
  };
  const stop = () => {
    if (frame !== null) cancelAnimationFrame(frame);
    frame = null;
  };

  resize();
  window.addEventListener('resize', resize);
  document.addEventListener('visibilitychange', () => (document.hidden ? stop() : start()));
  window.addEventListener('pointermove', (event) => {
    mouse = event.pointerType === 'mouse' ? { x: event.clientX, y: event.clientY } : null;
  }, { passive: true });
  document.documentElement.addEventListener('pointerleave', () => {
    mouse = null;
  });
  start();
})();
