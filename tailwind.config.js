/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  darkMode: 'class', // <--- This enables the toggle button
  theme: {
    extend: {
      colors: {
        dark: {
          bg: '#111827',
          card: '#1f2937',
          border: '#374151',
          text: '#f3f4f6',
        }
      }
    },
  },
  plugins: [],
}