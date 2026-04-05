// Check localStorage early to avoid Flash of Unstyled Content (FOUC)
if (localStorage.getItem('sidebar_collapsed') === 'true' && window.innerWidth >= 1024) {
    const container = document.getElementById('app-container');
    if (container) {
        container.classList.add('collapsed-sidebar');
    }
}
