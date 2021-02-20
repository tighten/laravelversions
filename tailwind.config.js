module.exports = {
  purge: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
    './resources/**/*.vue',
  ],
  darkMode: false, // or 'media' or 'class'
  theme: {
    colors: {
      'gray-50': '#FAFAFA',
      'gray-100': '#F3F4F6',
      'gray-700': '#2E3036',
      'red-300': '#DE8888',
      'yellow-300': '#F2D987',
      'green-300': '#5AC984',
      'blue-300': '#75B5E2',
      'white': '#FFFFFF',
    },
    fontSize: {
      '5xl': '2.5rem',
      'xl': '1.3rem',
    },
    extend: {},
  },
  variants: {
    extend: {
      fontWeight: ['hover', 'focus'],
    },
  },
  plugins: [],
}
