'use strict';

// Mobile nav toggle
const navbar = document.querySelector("[data-navbar]");
const navToggler = document.querySelector("[data-nav-toggler]");

navToggler.addEventListener("click", function () {
    navbar.classList.toggle("active");
});

// Header active
const header = document.querySelector("[data-header]");

window.addEventListener("scroll", function () {
    header.classList[window.scrollY > 50 ? "add" : "remove"]("active");
});

// Product carousel functionality
document.addEventListener('DOMContentLoaded', () => {
    // Select the necessary elements
    const list = document.querySelector('.product-list.has-scrollbar');
    const items = document.querySelectorAll('.product-list .scrollbar-item');
    const prevBtn = document.querySelector('.prev-btn');
    const nextBtn = document.querySelector('.next-btn');

    // Configuration
    const itemsPerPage = 3; 

    // Calculated values
    const totalItems = items.length;
    const totalSlides = totalItems - itemsPerPage + 1; 
    const slideDistance = 100 / itemsPerPage; 
    let currentSlide = 0; 

    function updateCarousel() {
        const translateValue = -currentSlide * slideDistance;
        list.style.transform = `translateX(${translateValue}%)`;

        if (prevBtn) {
             prevBtn.disabled = currentSlide === 0;
        }
        if (nextBtn) {
            nextBtn.disabled = currentSlide >= totalSlides - 1;
        }
    }

    // --- Event Listeners for Buttons ---

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentSlide < totalSlides - 1) {
                currentSlide++;
                updateCarousel();
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentSlide > 0) {
                currentSlide--;
                updateCarousel();
            }
        });
    }

    // Initialize the carousel on load
    if (list && items.length > itemsPerPage) {
        updateCarousel();
    } else if (prevBtn) {
         prevBtn.style.display = 'none';
         if (nextBtn) nextBtn.style.display = 'none';
    }
});