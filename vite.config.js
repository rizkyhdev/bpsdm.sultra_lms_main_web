import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/sass/app.scss',
                'resources/css/app.css',
                'resources/js/app.js',
                'resources/js/student.js',
            ],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
          output: {
            manualChunks: undefined,
          },
        },
      },
      base: process.env.NODE_ENV === 'production' ? process.env.APP_URL + '/' : '/',
});
