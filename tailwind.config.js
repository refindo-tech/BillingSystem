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
          },
          red: {
            50: '#CE1027',
            100: '#B70026',
          },
        },
      },
    },
  },
  plugins: [
    require('flowbite/plugin'),
  ],
}

