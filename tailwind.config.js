import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import flowbite from 'flowbite/plugin';

/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
    './app/DataTables/*.php', // for custom DataTable classes
    './resources/views/**/*.blade.php',
    './node_modules/flowbite/**/*.js',
  ],

  theme: {
    extend: {
      colors: {
        net: {
          blue: {
            50: '#501BCC',
            100: '#501BCC',
          },
          orange: {
            50: '#dd03ff',
            100: '#dd03ff',
            150: '#dd03ff',
          },
          red: {
            50: '#08a3fc',
            100: '#08a3fc',
          },
        },
        orange: {
          600: '#08a3fc',
        },
      },
      keyframes: {
        float: {
          '0%, 100%': { transform: 'translateY(0)' },
          '50%': { transform: 'translateY(-20%)' },
        },
        'float-x': {
          '0%, 100%': { transform: 'translateX(0)' },
          '50%': { transform: 'translateX(-20%)' },
        },
        'bounce-x': {
          '0%, 100%': { transform: 'translateX(0)' },
          '50%': { transform: 'translateX(20%)' },
        },
      },
      animation: {
        'float-1': 'float 3s ease-in-out infinite alternate',
        'float-2': 'float 4s ease-in-out infinite',
        'float-3': 'float 5s ease-in-out infinite',
        'bounce-x': 'bounce-x 1s ease-in-out infinite',
      },
      transitionProperty: {
        width: 'width',
      },
      textDecoration: ['active'],
      minWidth: {
        kanban: '28rem',
      },
      fontFamily: {
        sans: ['Figtree', ...defaultTheme.fontFamily.sans],
      },
    },
  },

  plugins: [
    forms,
    flowbite({
      chart: true,
    }),
  ],
};
