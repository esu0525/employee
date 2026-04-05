/**
 * Landing Page JavaScript
 * Handles Lucide icons, background particles, and navigation scroll effects.
 */

document.addEventListener('DOMContentLoaded', function () {
    // Initialize Lucide icons
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }

    // Background Particles
    const colors = ['#ffffff', '#cbd5e1', '#94a3b8', '#e2e8f0', '#6366f1', '#8b5cf6'];
    const pc = document.getElementById('particleContainer');
    if (pc) {
        for (let i = 0; i < 30; i++) {
            const el = document.createElement('div');
            el.className = 'particle';
            const size = Math.random() * 6 + 2;
            el.style.cssText = `
                width: ${size}px; height: ${size}px;
                background: ${colors[Math.floor(Math.random() * colors.length)]};
                left: ${Math.random() * 100}%;
                top: ${Math.random() * 100}%;
                animation-duration: ${8 + Math.random() * 12}s;
                animation-delay: ${-Math.random() * 15}s;
            `;
            pc.appendChild(el);
        }
    }

    // Navbar scroll effect
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            if (window.scrollY > 50) {
                navbar.style.padding = '1rem 5%';
                navbar.style.background = 'rgba(15, 23, 42, 0.95)';
            } else {
                navbar.style.padding = '1.5rem 5%';
                navbar.style.background = 'rgba(15, 23, 42, 0.4)';
            }
        });
    }
});
