import './bootstrap';
import './preload.js';
import './calendar_function.js';
import './carousel_animation.js';
import './gsap_animation.js';

// Animate.CSS
import 'animate.css';

// AOS
import AOS from 'aos';
import 'aos/dist/aos.css'; 
// ..
AOS.init();

// Splitting JS
import "splitting/dist/splitting.css";
import "splitting/dist/splitting-cells.css";
import Splitting from "splitting";

Splitting();

// Swiper. JS
import Swiper from 'swiper/bundle';
import 'swiper/css/bundle';
const swiper = new Swiper(".mySwiper",{
        slidesPerView: 3,
        spaceBetween: 30,
        loop: true,
        centeredSlides: true,
        watchSlidesProgress: true,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        navigation: {
            nextEl: ".swiper-button-next",
            prevEl: ".swiper-button-prev",
        },
        autoplay: {
            delay: 3000,
            disableOnInteraction: false,
        },
        on: {
            init: function () {
                // Pindahkan pagination ke luar wrapper
                const swiperEl = document.querySelector(".mySwiper");
                const paginationEl = swiperEl.querySelector(".swiper-pagination");
                swiperEl.parentNode.appendChild(paginationEl);
            },
            progress: function () {
                for (let i = 0; i < this.slides.length; i++) {
                    const slide = this.slides[i];
                    const progress = slide.progress;

                    // Reset default
                    slide.style.opacity = 0.3;
                    slide.style.transform = "scale(0.85)";
                    slide.style.filter = "blur(4px)";

                    if (Math.abs(progress) < 0.5) {
                        slide.style.opacity = 1;
                        slide.style.transform = "scale(1)";
                        slide.style.filter = "blur(0)";
                    } else if (Math.abs(progress) < 1.5) {
                        slide.style.opacity = 0.7;
                        slide.style.transform = "scale(0.9)";
                        slide.style.filter = "blur(2px)";
                    }
                }
            },
            setTransition: function (duration) {
                for (let i = 0; i < this.slides.length; i++) {
                    this.slides[i].style.transition = duration + "ms";
                }
            }
        }
    });



