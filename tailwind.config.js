/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
    "./node_modules/flowbite/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        net: {
          blue: {
            50: '#2159D4',
            100: '#0C25CB',
          },
          orange: {
            50: '#FFAC00',
            100: '#FD8C03',
            150: '#FE5A1D',
          },
          red: {
            50: '#CE1027',
            100: '#B70026',
          },
        },
      },
      keyframes: {
        'float': {
          '0%, 100%': { transform: 'translateY(0)', },
          '50%': { transform: 'translateY(-20%)', },
        },
        'float-x': {
          '0%, 100%': { transform: 'translateX(0)', },
          '50%': { transform: 'translateX(-20%)', },
      },
      'bounce-x': {
        '0%, 100%': { transform: 'translateX(0)', },
        '50%': { transform: 'translateX(20%)', },
      },
      },
      animation: {
        'float-1': 'float 3s ease-in-out infinite alternate',
        'float-2': 'float 4s ease-in-out infinite',
        'float-3': 'float 5s ease-in-out infinite',
        'bounce-x': 'bounce-x 1s ease-in-out infinite',
      },
    },
  },
  plugins: [
    require('flowbite/plugin'),
  ],
}

