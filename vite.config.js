import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        host: process.env.VITE_HOST || '0.0.0.0',
        hmr: {
            host: process.env.VITE_HMR_HOST || 'localhost',
            clientPort: 5173,
        },
        watch: {
            usePolling: true,
            interval: 100,
        },
    },
    plugins: [
        vue(),
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
        tailwindcss(),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
