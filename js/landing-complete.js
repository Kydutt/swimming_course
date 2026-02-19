// ============= Navigation =============
const navbar = document.getElementById('navbar');
const navLinks = document.querySelectorAll('.nav-link');
const mobileToggle = document.getElementById('mobileToggle');
const navMenu = document.getElementById('navMenu');

// Sticky navbar on scroll
window.addEventListener('scroll', () => {
    navbar.classList.toggle('scrolled', window.scrollY > 50);
    perbaruiNavAktif();
}, { passive: true });

// Mobile menu toggle
if (mobileToggle) {
    mobileToggle.addEventListener('click', () => {
        navMenu.classList.toggle('active');
        mobileToggle.classList.toggle('active');
    });
}

// Close mobile menu when clicking outside
document.addEventListener('click', (e) => {
    if (navMenu && navMenu.classList.contains('active')) {
        if (!navMenu.contains(e.target) && !mobileToggle.contains(e.target)) {
            navMenu.classList.remove('active');
            mobileToggle.classList.remove('active');
        }
    }
});

// ============= Smooth Scroll =============
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', (e) => {
        const href = anchor.getAttribute('href');
        if (!href || href === '#') return;
        const target = document.querySelector(href);
        if (target) {
            e.preventDefault();
            const offset = target.getBoundingClientRect().top + window.scrollY - (navbar ? navbar.offsetHeight : 0) - 8;
            window.scrollTo({ top: offset, behavior: 'smooth' });
            navMenu.classList.remove('active');
            mobileToggle.classList.remove('active');
        }
    });
});

// ============= Active Nav Link =============
function perbaruiNavAktif() {
    const sections = document.querySelectorAll('section[id]');
    const scrollPos = window.scrollY + (navbar ? navbar.offsetHeight : 0) + 60;

    sections.forEach(section => {
        if (scrollPos >= section.offsetTop && scrollPos < section.offsetTop + section.offsetHeight) {
            navLinks.forEach(link => {
                link.classList.remove('active-nav');
                if (link.getAttribute('href') === `#${section.id}`) {
                    link.classList.add('active-nav');
                }
            });
        }
    });
}
perbaruiNavAktif();

// ============= Scroll Animations =============
const animatedEls = document.querySelectorAll('.feature-card, .program-card, .section-header, .info-card');
animatedEls.forEach(el => el.classList.add('fade-in'));

if ('IntersectionObserver' in window) {
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });

    animatedEls.forEach(el => observer.observe(el));
} else {
    animatedEls.forEach(el => el.classList.add('visible'));
}

// Stagger animation for cards
document.querySelectorAll('.features-grid .feature-card').forEach((el, i) => {
    el.style.transitionDelay = (i * 0.1) + 's';
});
document.querySelectorAll('.programs-grid .program-card').forEach((el, i) => {
    el.style.transitionDelay = (i * 0.1) + 's';
});

// ============= Program Card 3D Tilt =============
document.querySelectorAll('.program-card').forEach(card => {
    card.addEventListener('mousemove', (e) => {
        const rect = card.getBoundingClientRect();
        const cx = rect.left + rect.width / 2;
        const cy = rect.top + rect.height / 2;
        const dx = (e.clientX - cx) / (rect.width / 2);  // -1 to 1
        const dy = (e.clientY - cy) / (rect.height / 2);  // -1 to 1
        const rotX = -dy * 8;   // max 8deg vertical tilt
        const rotY = dx * 8;   // max 8deg horizontal tilt

        card.style.transform = `translateY(-12px) scale(1.02) rotateX(${rotX}deg) rotateY(${rotY}deg)`;
        card.style.transition = 'box-shadow 0.15s ease, border-color 0.3s ease';
        card.style.perspective = '1000px';
    });

    card.addEventListener('mouseleave', () => {
        card.style.transform = '';
        card.style.transition = 'transform 0.4s cubic-bezier(0.34, 1.56, 0.64, 1), box-shadow 0.4s ease, border-color 0.3s ease';
    });
});

// ============= Ripple Effect on Daftar Button =============
document.querySelectorAll('.btn-program').forEach(btn => {
    btn.addEventListener('click', function (e) {
        const rect = btn.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;

        const ripple = document.createElement('span');
        ripple.classList.add('ripple');
        ripple.style.cssText = `width:${size}px; height:${size}px; left:${x}px; top:${y}px;`;
        btn.appendChild(ripple);

        ripple.addEventListener('animationend', () => ripple.remove());
    });
});

