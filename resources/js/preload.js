document.addEventListener("DOMContentLoaded", () => {
    const preloader = document.getElementById("preloader");

    // Matikan animasi Animate.css dulu
    document.querySelectorAll("[data-animate]").forEach(el => {
        const classes = el.getAttribute("data-animate").split(" ");
        el.classList.remove(...classes);
    });

    // Reset preloader di awal halaman load
    if (preloader) {
        preloader.classList.remove("fade-out");
        preloader.style.display = "flex";
    }

    // Jalankan fade-out saat halaman selesai load
    window.addEventListener("load", () => {
        setTimeout(() => {
            preloader.classList.add("fade-out");

            // Setelah preloader hilang, jalankan animasi
            setTimeout(() => {
                document.querySelectorAll("[data-animate]").forEach(el => {
                    const classes = el.getAttribute("data-animate").split(" ");
                    el.classList.add(...classes);
                });
            }, 500);

        }, 300);
    });

    // Saat klik link, tampilkan preloader lagi
    document.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", function (e) {
            const url = this.getAttribute("href");

            // Abaikan link kosong atau anchor
            if (!url || url.startsWith("#") || this.target === "_blank") return;

            e.preventDefault();
            preloader.classList.remove("fade-out");
            preloader.style.display = "flex";

            setTimeout(() => {
                window.location.href = url;
            }, 400);
        });
    });
});
