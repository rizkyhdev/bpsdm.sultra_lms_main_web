import { gsap } from "gsap";
document.addEventListener("DOMContentLoaded", () => {
    const preloader = document.getElementById("preloader");

    
    document.querySelectorAll("[data-animate]").forEach(el => {
        const classes = el.getAttribute("data-animate").split(" ");
        el.classList.remove(...classes);
    });

    if (preloader) {
        preloader.classList.remove("fade-out");
        preloader.style.display = "flex";
    }

    window.addEventListener("load", () => {
        setTimeout(() => {
            preloader.classList.add("fade-out");

            setTimeout(() => {
                document.querySelectorAll("[data-animate]").forEach(el => {
                    const classes = el.getAttribute("data-animate").split(" ");
                    el.classList.add(...classes);
                });

        
                animateNavbar();
                ScrollTrigger.refresh();
            }, 500);

        }, 300);
    });


    document.querySelectorAll("a").forEach(link => {
        link.addEventListener("click", function (e) {
            const url = this.getAttribute("href");
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



function animateNavbar() {
    gsap.from(".logo-navbar", {
        duration: 1.5,
        x: -200,
        opacity: 0,
        rotation: -15,
        ease: "bounce.out",
        stagger: 0.2
    });

    // Hover effect untuk logo-navbar
    document.querySelectorAll(".logo-navbar").forEach(logo => {
        logo.addEventListener("mouseenter", () => {
            gsap.to(logo, { scale: 1.1, rotation: 5, duration: 0.3, ease: "power1.out" });
        });
        logo.addEventListener("mouseleave", () => {
            gsap.to(logo, { scale: 1, rotation: 0, duration: 0.3, ease: "power1.out" });
        });
    });
}



function animatePelatihanCards() {
    gsap.killTweensOf(".cardCalendar");
    gsap.set(".cardCalendar", { clearProps: "all" });
    gsap.from(".cardCalendar", {
        opacity: 0,
        y: 80,
        rotate: 3,
        scale: 0.95,
        duration: 0.8,
        stagger: 0.15,
        ease: "power2.out"
    });

    ScrollTrigger.refresh();
}




document.addEventListener("click", function (e) {
    if (e.target.classList.contains("calendar-day") && e.target.dataset.date) {
        const selectedDate = e.target.dataset.date;
        renderPelatihan(selectedDate);

        if (typeof animatePelatihanCards === "function") {
            animatePelatihanCards();
        }
    }
});
