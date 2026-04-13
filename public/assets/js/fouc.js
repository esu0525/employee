// Check localStorage early to avoid Flash of Unstyled Content (FOUC)
if (localStorage.getItem('sidebar_collapsed') === 'true' && window.innerWidth >= 1024) {
    const container = document.getElementById('app-container');
    if (container) {
        container.classList.add('collapsed-sidebar');
    }
}

// Apply saved theme immediately to <body> to avoid FOUC on dark/night mode
(function() {
    const savedTheme = localStorage.getItem('app-theme');
    if (savedTheme && savedTheme !== 'light') {
        document.documentElement.setAttribute('data-theme', savedTheme);
        // body may not exist yet at this point in sidebar fouc.js, so we use a listener
        document.addEventListener('DOMContentLoaded', function() {
            document.body.setAttribute('data-theme', savedTheme);
        });
    }
})();
