// =========================================
// 1. MARQUEE INFINITE SCROLL LOGIC
// =========================================
document.addEventListener("DOMContentLoaded", () => {
    const track = document.querySelector('.marquee-track');

    if (!track) return;
    const originalContent = track.innerHTML;
    track.innerHTML = originalContent + originalContent;

    let position = 0;
    const speed = 1.5; 

    function animateMarquee() {
        position -= speed;
        const resetPoint = track.scrollWidth / 2;
        if (Math.abs(position) >= resetPoint) {
            position = 0;
        }
        track.style.transform = `translate3d(${position}px, 0, 0)`;
        requestAnimationFrame(animateMarquee);
    }
    requestAnimationFrame(animateMarquee);
});

// =========================================
// 2. SIDE MENU TOGGLE & ANIMATIONS
// =========================================
document.addEventListener('DOMContentLoaded', () => {
    const menuBtn = document.getElementById('menu-btn') || document.getElementById('open-menu-btn');
    const closeBtn = document.getElementById('close-btn') || document.getElementById('close-menu-btn');
    const pageWrapper = document.getElementById('page-wrapper');
    const menuToggle = document.getElementById('menu-toggle');
    const body = document.body;
    
    // Select all menu links across the template
    const menuLinks = document.querySelectorAll('.side-menu-links a, .login-box a, .side-menu a');

    function openMenu() {
        body.classList.add('menu-open');
        if (menuToggle) menuToggle.checked = true;
        
        // Stagger link entrance
        menuLinks.forEach((link, index) => {
            link.style.opacity = '0';
            link.style.transform = 'translate3d(30px, 0, 0)';
            
            setTimeout(() => {
                link.style.transition = 'opacity 0.4s cubic-bezier(0.16, 1, 0.3, 1), transform 0.6s cubic-bezier(0.16, 1, 0.3, 1)';
                link.style.opacity = '1';
                link.style.transform = 'translate3d(0, 0, 0)';
            }, 100 + (index * 60));
        });
    }

    function closeMenu() {
        body.classList.remove('menu-open');
        if (menuToggle) menuToggle.checked = false;

        // Rapidly hide links matching the X button exit animation
        menuLinks.forEach(link => {
            link.style.transition = 'opacity 0.2s ease, transform 0.2s ease';
            link.style.opacity = '0';
            link.style.transform = 'translate3d(30px, 0, 0)';
        });
    }

    // Event Listeners for Open and Close
    if (menuBtn) menuBtn.addEventListener('click', openMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    
    // Close menu when clicking the shrunken wrapper
    if (pageWrapper) {
        pageWrapper.addEventListener('click', (e) => {
            if (body.classList.contains('menu-open') || (menuToggle && menuToggle.checked)) {
                e.preventDefault(); 
                closeMenu();
            }
        });
    }

    // Same-page link click handler (reuses closeMenu animation without reload)
    menuLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const linkUrl = new URL(link.href, window.location.origin);
            const currentUrl = new URL(window.location.href);

            if (linkUrl.pathname === currentUrl.pathname) {
                e.preventDefault(); // Stop page reload
                closeMenu();        // Uses exact same slide-down exit as X button
            }
        });
    });
});

// =========================================
// 3. TESTIMONIAL CAROUSEL
// =========================================
document.addEventListener('DOMContentLoaded', () => {
    const carousel = document.getElementById('testimonial-carousel');
    if (!carousel) return;

    const track = carousel.querySelector('#testimonial-track');
    let slides = Array.from(track.children);
    const prevBtn = carousel.querySelector('.prev-btn');
    const nextBtn = carousel.querySelector('.next-btn');

    let currentIndex = 0;
    const totalOriginalSlides = slides.length;

    // Clone the first and last slides for seamless infinite scrolling
    const firstClone = slides[0].cloneNode(true);
    const lastClone = slides[slides.length - 1].cloneNode(true);

    firstClone.id = 'first-clone';
    lastClone.id = 'last-clone';

    track.appendChild(firstClone);
    track.insertBefore(lastClone, slides[0]);

    // Re-select all slides including clones
    slides = Array.from(track.children);
    
    function moveToSlide(index, withTransition = true) {
        track.style.transition = withTransition ? 'transform 0.5s cubic-bezier(0.16, 1, 0.3, 1)' : 'none';
        
        const slideWidth = slides[0].offsetWidth;
        const gap = parseInt(window.getComputedStyle(track).gap) || 0;
        const moveAmount = (slideWidth + gap) * index;
        
        track.style.transform = `translateX(-${moveAmount}px)`;
        currentIndex = index;
    }

    setTimeout(() => {
        moveToSlide(1, false);
    }, 0);

    window.addEventListener('resize', () => {
        moveToSlide(currentIndex, false);
    });

    if (nextBtn) {
        nextBtn.addEventListener('click', () => {
            if (currentIndex >= slides.length - 1) return;
            currentIndex++;
            moveToSlide(currentIndex, true);

            if (slides[currentIndex].id === 'first-clone') {
                setTimeout(() => {
                    moveToSlide(1, false);
                }, 500);
            }
        });
    }

    if (prevBtn) {
        prevBtn.addEventListener('click', () => {
            if (currentIndex <= 0) return;
            currentIndex--;
            moveToSlide(currentIndex, true);

            if (slides[currentIndex].id === 'last-clone') {
                setTimeout(() => {
                    moveToSlide(totalOriginalSlides, false);
                }, 500);
            }
        });
    }
});

// =========================================
// 4. DYNAMIC HEADER/FOOTER ICON RANDOMIZER
// =========================================
document.addEventListener('DOMContentLoaded', () => {
    const iconContainer = document.getElementById('dynamic-svg-icon');
    
    if (iconContainer) {
        const icons = iconContainer.querySelectorAll('svg');
        
        if (icons.length > 0) {
            icons.forEach(icon => icon.style.display = 'none');
            const randomIndex = Math.floor(Math.random() * icons.length);
            icons[randomIndex].style.display = 'block';
        }
    }
});