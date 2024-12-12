import defaultTheme from "tailwindcss/defaultTheme";

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    darkMode: "media",
    theme: {
        extend: {
            colors: {
                "apple-blue": "#007AFF",
                "apple-green": "#34C759",
                "apple-yellow": "#FFCC00",
                "apple-red": "#FF3B30",
            },
        },
    },
    plugins: [],
};
