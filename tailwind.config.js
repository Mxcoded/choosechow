import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    // FORCE Light Mode by using 'class' strategy (and never adding the class)
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Ensure primary red is consistent
                red: {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    600: '#dc2626',
                    700: '#b91c1c',
                    900: '#7f1d1d',
                }
            }
        },
    },

    plugins: [forms],
};