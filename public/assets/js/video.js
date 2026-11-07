/*
 * VideoPlayer : au clic, remplace la miniature par le lecteur YouTube sans cookie (youtube-nocookie.com).
 * Rien n'est chargé depuis YouTube (hors miniature) tant que le visiteur ne lance pas la vidéo.
 */
(() => {
  document.addEventListener('click', (event) => {
    const play = event.target.closest('.video-player__play');
    if (!play) return;

    const player = play.closest('[data-video]');
    const id = player.dataset.video;
    if (!/^[A-Za-z0-9_-]{11}$/.test(id)) return;

    event.preventDefault();
    const iframe = document.createElement('iframe');
    iframe.src = `https://www.youtube-nocookie.com/embed/${id}?autoplay=1&rel=0`;
    iframe.title = player.dataset.title;
    iframe.allow = 'accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture';
    iframe.allowFullscreen = true;
    iframe.referrerPolicy = 'strict-origin-when-cross-origin';
    player.replaceChildren(iframe);
    iframe.focus();
  });
})();
