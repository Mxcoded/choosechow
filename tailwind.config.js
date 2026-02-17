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
                // ===========================================
                // CHOOSECHOW FOOD PSYCHOLOGY COLOR PALETTE
                // ===========================================
                
                // Primary - Appetite Red (stimulates hunger, urgency)
                'chow-red': {
                    50: '#fef2f2',
                    100: '#fee2e2',
                    200: '#fecaca',
                    300: '#fca5a5',
                    400: '#f87171',
                    500: '#ef4444',
                    600: '#dc2626',  // Primary CTA
                    700: '#b91c1c',  // Hover state
                    800: '#991b1b',
                    900: '#7f1d1d',
                },
                
                // Secondary - Warm Orange (warmth, home cooking, friendly)
                'chow-orange': {
                    50: '#fff7ed',
                    100: '#ffedd5',
                    200: '#fed7aa',
                    300: '#fdba74',
                    400: '#fb923c',  // Highlights
                    500: '#f97316',  // Secondary CTA
                    600: '#ea580c',  // Hover state
                    700: '#c2410c',
                    800: '#9a3412',
                    900: '#7c2d12',
                },
                
                // Accent - Golden Yellow (happiness, ratings, premium)
                'chow-gold': {
                    50: '#fefce8',
                    100: '#fef9c3',
                    200: '#fef08a',
                    300: '#fde047',
                    400: '#facc15',  // Ratings, stars
                    500: '#eab308',  // Premium badges
                    600: '#ca8a04',
                    700: '#a16207',
                    800: '#854d0e',
                    900: '#713f12',
                },
                
                // Neutral - Warm Cream (homey, comforting backgrounds)
                'chow-cream': {
                    50: '#fffbf5',   // Lightest warm background
                    100: '#fef7ed',  // Section backgrounds
                    200: '#fdf2e3',
                    300: '#fce7d3',
                    400: '#f9d9bd',
                    500: '#f5c9a3',
                },
                
                // Earth - Brown tones (natural, authentic, trust)
                'chow-brown': {
                    50: '#fdf8f3',
                    100: '#f5ebe0',
                    200: '#e6d5c3',
                    300: '#d4b896',
                    400: '#c49a6c',
                    500: '#a67c52',
                    600: '#92400e',  // Earthy accents
                    700: '#7c3410',
                    800: '#78350f',  // Trust text
                    900: '#5c2a0e',
                },
                
                // Fresh - Green (healthy, fresh, success)
                'chow-fresh': {
                    50: '#f0fdf4',
                    100: '#dcfce7',
                    200: '#bbf7d0',
                    300: '#86efac',
                    400: '#4ade80',
                    500: '#22c55e',  // Healthy badges
                    600: '#16a34a',  // Verified, success
                    700: '#15803d',
                    800: '#166534',
                    900: '#14532d',
                },

                // Keep original red for backward compatibility
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
