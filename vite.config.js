import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/assets/sass/app.scss',
                "node_modules/sortablejs/Sortable.min.js",
                "resources/assets/js/jquery-ui.min.js",
                "resources/assets/js/huebee.js",
                "resources/assets/js/app.js",
                "resources/assets/js/keyBindings.js",
                "resources/assets/js/itemExport.js",
                "resources/assets/js/itemImport.js",
                "resources/assets/js/liveStatRefresh.js",
            ],
            refresh: true,
        }),
    ],
});
