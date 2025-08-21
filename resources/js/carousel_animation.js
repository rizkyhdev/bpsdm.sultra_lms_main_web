document.addEventListener('DOMContentLoaded', function () {

  if (typeof Splitting === 'function') {
    Splitting({ target: '#heroCarousel [data-splitting]', by: 'chars' });
  }

  const carouselEl = document.getElementById('heroCarousel');

  const playFor = (slide) => {
    if (!slide) return;
 
    carouselEl.querySelectorAll('.carousel-item')
      .forEach(s => s.classList.remove('animate-in'));

  
    void slide.offsetWidth;

    slide.classList.add('animate-in');
  };


  const first = carouselEl.querySelector('.carousel-item.active');
  requestAnimationFrame(() => {
    requestAnimationFrame(() => playFor(first));
  });

  // Bersihkan saat mulai slide
  carouselEl.addEventListener('slide.bs.carousel', () => {
    carouselEl.querySelectorAll('.carousel-item')
      .forEach(s => s.classList.remove('animate-in'));
  });

  // Mainkan animasi setelah slide berpindah
  carouselEl.addEventListener('slid.bs.carousel', (e) => {
    playFor(e.relatedTarget);
  });
});