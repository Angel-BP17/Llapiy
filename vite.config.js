import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import react from "@vitejs/plugin-react";
import tailwindcss from "@tailwindcss/vite";
import { wayfinder } from "@laravel/vite-plugin-wayfinder";
import path from "path";

export default defineConfig({
    base: "",
    server: {
        host: "localhost",
        watch: {
            ignored: ["**/storage/framework/views/**"],
        },
    },
    plugins: [
        laravel({
            input: ["resources/css/app.css", "resources/js/app.tsx"],
            refresh: true,
        }),
        react(),
        tailwindcss(),
        wayfinder(),
    ],
    resolve: {
        alias: {
            "@": path.resolve(__dirname, "./resources/js"),
        },
    },
    test: {
        globals: true,
        environment: "jsdom",
        setupFiles: "./resources/js/tests/setup.ts",
    },
});
