import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import vue from "@vitejs/plugin-vue";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/sass/app.scss",
                "resources/js/app.js",
                // Common JS
                "resources/js/common/imagePreview.js",
                "resources/js/common/confirm.js",
                "resources/js/common/viewModal.js",
                // Admin CSS
                "resources/css/admin/dashboard.css",
                "resources/css/admin/forms.css",
                "resources/css/admin/tables.css",
                // Component CSS
                "resources/css/components/notifications.css",
                // Admin JS
                "resources/js/admin/dashboard.js",
                "resources/js/admin/student-form.js",
                "resources/js/admin/student-table.js",
                "resources/js/admin/notifications.js",
                // Component JS
                "resources/js/components/notifications.js",
            ],
            refresh: true,
        }),
        vue({
            template: {
                transformAssetUrls: {
                    base: null,
                    includeAbsolute: false,
                },
            },
        }),
    ],
    resolve: {
        alias: {
            vue: "vue/dist/vue.esm-bundler.js",
        },
    },
});
