import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

export default defineConfig({
    server: {
        host: process.env.VITE_HOST || '0.0.0.0',
        hmr: {
            host: process.env.VITE_HMR_HOST || 'localhost',
        },
        watch: {
            usePolling: process.env.CHOKIDAR_USEPOLLING === 'true',
            interval: 10,       // Cek setiap 100ms (default biasanya lebih lambat)
            binaryInterval: 30, // Cek binary setiap 300ms
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
