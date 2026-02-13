/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
        "./vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php",
    ],
    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#e8f4fd',
                    100: '#d1e9fb',
                    200: '#a3d3f7',
                    300: '#75bdf3',
                    400: '#47a7ef',
                    500: '#1991eb',
                    600: '#1474bc',
                    700: '#0f578d',
                    800: '#0a3a5e',
                    900: '#051d2f',
                },
            },
            fontFamily: {
                sans: ['Poppins', 'sans-serif'],
            },
        },
    },
    plugins: [],
}
