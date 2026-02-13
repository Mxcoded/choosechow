import './bootstrap';

// 1. Import jQuery and make it available globally
import jQuery from 'jquery';
window.$ = window.jQuery = jQuery;

// 2. Import Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

// 3. Import FontAwesome CSS
import '@fortawesome/fontawesome-free/css/all.css';

// Theme toggle: persist theme in localStorage and toggle `dark` class on <html>
const applyTheme = (theme) => {
	if (theme === 'dark') {
		document.documentElement.classList.add('dark');
	} else {
		document.documentElement.classList.remove('dark');
	}
};

// Initialize theme from localStorage or system preference - do this ASAP
try {
	const stored = localStorage.getItem('theme');
	if (stored) {
		applyTheme(stored);
	} else if (window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches) {
		applyTheme('dark');
	}
} catch (e) {
	// ignore localStorage errors
}

const toggleTheme = () => {
	const isDark = document.documentElement.classList.toggle('dark');
	try { localStorage.setItem('theme', isDark ? 'dark' : 'light'); } catch (e) {}
};

// Set up event listeners when DOM is ready
document.addEventListener('DOMContentLoaded', () => {
	const btn = document.getElementById('theme-toggle');
	const mobileBtn = document.getElementById('mobile-theme-toggle');
	if (btn) btn.addEventListener('click', toggleTheme);
	if (mobileBtn) mobileBtn.addEventListener('click', toggleTheme);
});