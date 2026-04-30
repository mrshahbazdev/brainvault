import Alpine from 'alpinejs';

window.Alpine = Alpine;

// Dark mode toggle
Alpine.store('darkMode', {
    on: localStorage.getItem('darkMode') === 'true' ||
        (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    toggle() {
        this.on = !this.on;
        localStorage.setItem('darkMode', this.on);
        document.documentElement.classList.toggle('dark', this.on);
    },
    init() {
        document.documentElement.classList.toggle('dark', this.on);
    }
});

Alpine.start();
