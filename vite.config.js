import {defineConfig} from 'vite';
import laravel, {refreshPaths} from 'laravel-vite-plugin';
import { tscPlugin } from "vite-plugin-tsc-watch";

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.ts'],
            refresh: [
                ...refreshPaths,
                'app/Http/Livewire/**',
                'app/Http/**/**.php',
                "resources/js/**/*.ts",
            ],
        }),
        tscPlugin()
    ],
});
