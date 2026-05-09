import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';

const usePolling =
    process.env.CHOKIDAR_USEPOLLING === 'true' ||
    process.env.CHOKIDAR_USEPOLLING === '1';

export default defineConfig({
    server: {
        host: process.env.VITE_HOST || '0.0.0.0',
        hmr: {
            host: process.env.VITE_HMR_HOST || 'localhost',
            clientPort: 5173,
        },
        watch: usePolling
            ? {
                usePolling: true,
                interval: Number(process.env.CHOKIDAR_INTERVAL || 100),
            }
            : undefined,
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
