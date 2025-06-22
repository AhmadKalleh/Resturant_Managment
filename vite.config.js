import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import react from '@vitejs/plugin-react';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.jsx'],
            refresh: true,
        }),
        react({
            include: "**/*.{jsx,tsx}",
            babel: {
                presets: ['@babel/preset-react'],
                plugins: ['@babel/plugin-transform-react-jsx']
            }
        }),
    ],
    resolve: {
        extensions: ['.js', '.jsx', '.json']
    },
    optimizeDeps: {
        include: ['react', 'react-dom', '@inertiajs/react']
    },
    build: {
        commonjsOptions: {
            include: [/node_modules/],
            transformMixedEsModules: true
        }
    }
});
