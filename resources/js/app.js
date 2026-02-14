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