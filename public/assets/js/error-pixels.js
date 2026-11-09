/*
 * Page 404 qui s'effrite : des pixels accent tombent dans la carte et s'empilent en bas.
 * Un clic dans la carte en fait tomber d'autres à cet endroit.
 * Rien ne s'affiche si le système demande de réduire les animations.
 */
(() => {
  const card = document.querySelector('.error-page--404');
  if (!card || window.matchMedia('(prefers-reduced-motion: reduce)').matches) return;

  const SIZE = 8;        // taille d'un pixel (px)
  const MAX_STACK = 5;   // hauteur maximale d'une pile, en pixels : le bouton reste dégagé
  const FIRST_WAVE = 120;

  const canvas = document.createElement('canvas');
  canvas.className = 'error-page__pixels';
  canvas.setAttribute('aria-hidden', 'true');
  card.prepend(canvas);
  const ctx = canvas.getContext('2d');
  const accent = getComputedStyle(document.documentElement).getPropertyValue('--color-accent').trim() || '#C084FC';

  let width = 0, height = 0, cols = 0, piles = [], falling = [], frame = null;

  const resize = () => {
    const ratio = Math.min(window.devicePixelRatio || 1, 2);
    const rect = card.getBoundingClientRect();
    width = rect.width;
    height = rect.height;
    canvas.width = Math.round(width * ratio);
    canvas.height = Math.round(height * ratio);
    ctx.setTransform(ratio, 0, 0, ratio, 0, 0);
    const previous = piles;
    cols = Math.floor(width / SIZE);
    piles = Array.from({ length: cols }, (_, i) => previous[i] ?? 0);
    if (frame === null) draw();
  };

  const spawn = (count, x = null) => {
    for (let k = 0; k < count; k++) {
      const col = x === null
        ? Math.floor(Math.random() * cols)
        : Math.max(0, Math.min(cols - 1, Math.floor(x / SIZE) + Math.floor(Math.random() * 7) - 3));
      falling.push({ col, y: -Math.random() * height * 0.7, speed: 1 + Math.random() * 2 });
    }
    if (frame === null) frame = requestAnimationFrame(draw);
  };

  function draw() {
    ctx.clearRect(0, 0, width, height);
    ctx.fillStyle = accent;
    for (let c = 0; c < cols; c++) {
      for (let k = 0; k < piles[c]; k++) ctx.fillRect(c * SIZE + 1, height - (k + 1) * SIZE + 1, SIZE - 2, SIZE - 2);
    }
    falling = falling.filter((pixel) => {
      pixel.y += pixel.speed;
      pixel.speed += 0.08;
      const floor = height - (piles[pixel.col] + 1) * SIZE;
      if (pixel.y >= floor) {
        piles[pixel.col] = Math.min(piles[pixel.col] + 1, MAX_STACK);
        return false;
      }
      ctx.fillRect(pixel.col * SIZE + 1, Math.round(pixel.y / SIZE) * SIZE + 1, SIZE - 2, SIZE - 2);
      return true;
    });
    frame = falling.length > 0 ? requestAnimationFrame(draw) : null;
  }

  resize();
  new ResizeObserver(resize).observe(card);
  spawn(FIRST_WAVE);

  card.addEventListener('click', (event) => {
    if (event.target.closest('a, button')) return;
    spawn(30, event.clientX - card.getBoundingClientRect().left);
  });
})();
