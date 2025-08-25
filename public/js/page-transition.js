document.addEventListener("DOMContentLoaded", function () {
  const body = document.body;

  // Masukkan efek masuk (fade + scale in)
  body.style.opacity = '0';
  body.style.transform = 'scale(0.98)';
  body.style.transition = 'opacity 0.4s ease, transform 0.4s ease';

  requestAnimationFrame(() => {
    body.style.opacity = '1';
    body.style.transform = 'scale(1)';
  });

  // Tangani efek keluar saat klik link
  document.querySelectorAll('a[href]').forEach(link => {
    link.addEventListener('click', function (e) {
      const href = this.getAttribute('href');

      const isSameOrigin = this.hostname === window.location.hostname;
      const isNotBlank = this.getAttribute('target') !== '_blank';
      const isNotAnchor = !href.startsWith('#');
      const isNotJsLink = !href.startsWith('javascript:');

      if (isSameOrigin && isNotBlank && isNotAnchor && isNotJsLink) {
        e.preventDefault();

        // Efek keluar (fade + scale out)
        body.style.opacity = '0';
        body.style.transform = 'scale(0.98)';

        setTimeout(() => {
          window.location.href = href;
        }, 200); // waktu sinkron dengan transition di atas
      }
    });
  });
});
