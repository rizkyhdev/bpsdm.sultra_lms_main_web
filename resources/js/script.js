window.addEventListener('scroll', function() {
    var header = document.querySelector('.header');
    var mainContent = document.querySelector('.main-content');
    
    // Menambahkan kelas 'scrolled' jika halaman sudah di-scroll lebih dari 50px
    if (window.scrollY > 50) {
        header.classList.add('scrolled');
        mainContent.classList.add('scrolled');
    } else {
        header.classList.remove('scrolled');
        mainContent.classList.remove('scrolled');
    }
});