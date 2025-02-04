import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
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
                mint: {
                    50: '#f0fdf9',
                    100: '#e6f7f3',
                    200: '#c5ebe1',
                    600: '#0f766e',
                    800: '#0f766e',
                    1000: '#0f766e',
                    700: '#115e59',
                    900: '#134e4a',
                },
            },
        },
        
    },

    plugins: [forms],
};
