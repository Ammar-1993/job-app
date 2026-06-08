import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            fontSize: {
                'fluid-xs': 'clamp(0.7rem, 0.1vw + 0.68rem, 0.75rem)',
                'fluid-sm': 'clamp(0.8rem, 0.17vw + 0.76rem, 0.89rem)',
                'fluid-base': 'clamp(1rem, 0.34vw + 0.91rem, 1.19rem)',
                'fluid-lg': 'clamp(1.25rem, 0.61vw + 1.1rem, 1.58rem)',
                'fluid-xl': 'clamp(1.56rem, 1vw + 1.31rem, 2.11rem)',
                'fluid-2xl': 'clamp(1.95rem, 1.56vw + 1.56rem, 2.81rem)',
                'fluid-3xl': 'clamp(2.44rem, 2.38vw + 1.85rem, 3.75rem)',
                'fluid-4xl': 'clamp(3.05rem, 3.54vw + 2.17rem, 5rem)',
            },
            spacing: {
                'fluid-1': 'clamp(0.25rem, 0.1vw + 0.23rem, 0.35rem)',
                'fluid-2': 'clamp(0.5rem, 0.2vw + 0.45rem, 0.7rem)',
                'fluid-4': 'clamp(1rem, 0.4vw + 0.9rem, 1.4rem)',
                'fluid-8': 'clamp(2rem, 0.8vw + 1.8rem, 2.8rem)',
                'fluid-12': 'clamp(3rem, 1.2vw + 2.7rem, 4.2rem)',
                'fluid-16': 'clamp(4rem, 1.6vw + 3.6rem, 5.6rem)',
            },
            colors: {
                brand: {
                    50: '#f5f7ff',
                    100: '#ebf0fe',
                    200: '#ced9fd',
                    300: '#b1c2fb',
                    400: '#7695f8',
                    500: '#3b68f5',
                    600: '#355ddd',
                    700: '#2c4ea1',
                    800: '#233e81',
                    900: '#1d3369',
                    950: '#111e3e',
                },
                accent: {
                    50: '#fff1f2',
                    100: '#ffe4e6',
                    200: '#fecdd3',
                    300: '#fda4af',
                    400: '#fb7185',
                    500: '#f43f5e',
                    600: '#e11d48',
                    700: '#be123c',
                    800: '#9f1239',
                    900: '#881337',
                    950: '#4c0519',
                },
            },
        },
    },

    plugins: [forms],
};
