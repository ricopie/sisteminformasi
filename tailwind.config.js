/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.jsx',
        './resources/**/*.js',
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './node_modules/flowbite-react/**/*.{js,jsx}',
    ],
    darkMode: 'class',
    theme: {
        extend: {},
    },
    plugins: [require('flowbite/plugin')],
};
