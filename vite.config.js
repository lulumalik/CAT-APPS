import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';
import vue from '@vitejs/plugin-vue';
import { VitePWA } from 'vite-plugin-pwa';

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
        VitePWA({
            registerType: 'autoUpdate',
            includeAssets: ['favicon.ico', 'apple-touch-icon.png'],
            manifest: {
                name: 'Pratistha Cendekia Prestasi',
                short_name: 'Pratistha',
                description: 'Platform pembinaan dan kursus persiapan AKPOL.',
                theme_color: '#1a1a2d',
                background_color: '#1a1a2d',
                display: 'standalone',
                scope: '/',
                start_url: '/',
                icons: [
                    {
                        src: '/icons/pwa-192x192.png',
                        sizes: '192x192',
                        type: 'image/png',
                    },
                    {
                        src: '/icons/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                    },
                    {
                        src: '/icons/pwa-512x512.png',
                        sizes: '512x512',
                        type: 'image/png',
                        purpose: 'any maskable',
                    },
                ],
            },
            workbox: {
                globPatterns: ['**/*.{js,css,html,ico,png,svg,woff2}'],
                // Keep build stable when large marketing images are emitted.
                maximumFileSizeToCacheInBytes: 8 * 1024 * 1024,
            },
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
        },
    },
});
