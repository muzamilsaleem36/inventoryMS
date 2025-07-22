import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/js/app.js',
            ],
            refresh: true,
        }),
    ],
    resolve: {
        alias: {
            '@': '/resources/js',
            '~': '/resources/sass',
        },
    },
    build: {
        outDir: 'public/build',
        rollupOptions: {
            output: {
                manualChunks: {
                    vendor: ['bootstrap', 'jquery'],
                    pos: ['./resources/js/pos/pos-system.js'],
                    dashboard: ['./resources/js/pos/dashboard.js'],
                },
            },
        },
    },
    server: {
        host: '0.0.0.0',
        port: 3000,
        cors: true,
    },
    css: {
        preprocessorOptions: {
            scss: {
                additionalData: `@import "resources/sass/variables";`
            }
        }
    }
}); 