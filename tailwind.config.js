/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  // darkMode removed to enforce light mode only
  theme: {
    extend: {
      // Custom colors if needed, but dark mode colors removed
    },
  },
  plugins: [],
}