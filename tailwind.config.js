import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans:  ['Lato', ...defaultTheme.fontFamily.sans],
                serif: ['Lato', 'serif'],
                head:  ['Lato', 'sans-serif'],
            },
            colors: {
                funaab: {
                    50:  '#edf9f2',
                    100: '#d4f0de',
                    200: '#a8dfc0',
                    300: '#72c89a',
                    400: '#3daa71',
                    500: '#289950',
                    600: '#1f7a3c',
                    700: '#1a6432',
                    800: '#145228',
                    900: '#0d3320',
                },
                gold: {
                    50:  '#fdf5dc',
                    100: '#f8e9b0',
                    200: '#f0d080',
                    300: '#e6b84a',
                    400: '#d4a030',
                    500: '#c8992a',
                    600: '#a37820',
                    700: '#7a5818',
                    800: '#523a10',
                    900: '#2a1d08',
                },
            },
            width: {
                '3/5': '60%',
                '2/5': '40%',
                '1/2': '50%',
                '1/4': '25%',
                '3/4': '65%',
            },
            spacing: {
                '12': '3rem',
            },
            transitionProperty: {
                'width': 'width',
            },
        },
    },

    plugins: [forms],
};
