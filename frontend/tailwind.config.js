/** @type {import('tailwindcss').Config} */
export default {
    content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", "sans-serif"],
            },
            animation: {
                gradient: "gradient 4s ease infinite",
                "pulse-once": "pulseOnce 3s ease-out forwards",
            },
            keyframes: {
                gradient: {
                    "0%, 100%": { backgroundPosition: "0% 50%" },
                    "50%": { backgroundPosition: "100% 50%" },
                },
                pulseOnce: {
                    "0%": { backgroundColor: "rgba(126, 217, 87, 0.15)" },
                    "100%": { backgroundColor: "transparent" },
                },
            },
        },
    },
    plugins: [],
};
