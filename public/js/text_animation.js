document.addEventListener('DOMContentLoaded', function () {
    const headerElement = document.getElementById('typed-header');

    function startAnimation() {
        // Kosongkan isi h2
        headerElement.textContent = '';

        // Ketik teks pada h2
        new Typed('#typed-header', {
            strings: ["Selamat Datang di Sistem Informasi BANGKOM AURA"],
            typeSpeed: 80,
            showCursor: false,
            onComplete: function () {
                // Tunggu 10 detik, lalu fade out
                setTimeout(() => {
                    headerElement.style.transition = "opacity 2s";
                    headerElement.style.opacity = 0;

                    setTimeout(() => {
                        headerElement.style.opacity = 1;
                        startAnimation(); // Ulangi animasi
                    }, 2000); // Durasi fade out
                }, 10000); // Durasi tampil sebelum fade
            }
        });
    }

    startAnimation();
});
