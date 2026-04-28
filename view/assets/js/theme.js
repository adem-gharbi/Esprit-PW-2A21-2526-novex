document.addEventListener('DOMContentLoaded', () => {
    // Check initial theme from localStorage
    const savedTheme = localStorage.getItem('theme') || 'light';
    if (savedTheme === 'dark') {
        document.documentElement.setAttribute('data-theme', 'dark');
    }

    // Attempt to locate toggler if on settings page
    const themeToggleBtn = document.getElementById('themeToggleBtn');
    if (themeToggleBtn) {
        // Init visual state of button based on current theme
        if (savedTheme === 'dark') {
            themeToggleBtn.textContent = "Passer en mode Clair ☀️";
            themeToggleBtn.classList.remove('btn-outline');
            themeToggleBtn.classList.add('btn-accent');
        }

        themeToggleBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const currentTheme = document.documentElement.getAttribute('data-theme');
            let newTheme = 'light';
            
            if (currentTheme !== 'dark') {
                newTheme = 'dark';
            }
            
            document.documentElement.setAttribute('data-theme', newTheme);
            localStorage.setItem('theme', newTheme);
            
            // Update button visual
            if (newTheme === 'dark') {
                themeToggleBtn.textContent = "Passer en mode Clair ☀️";
                themeToggleBtn.classList.remove('btn-outline');
                themeToggleBtn.classList.add('btn-accent');
            } else {
                themeToggleBtn.textContent = "Passer en mode Sombre 🌙";
                themeToggleBtn.classList.remove('btn-accent');
                themeToggleBtn.classList.add('btn-outline');
            }
        });
    }
});
