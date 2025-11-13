import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";

export default defineConfig({
    plugins: [
        laravel({
            input: [
                "resources/css/app.css",
                "resources/js/app.js",
                "resources/js/area_group_subgroup_selector.js",
                "resources/js/documentTypes/document_type_selector.js",
                "resources/js/documentTypes/campo_selector.js",
                "resources/js/users/area_groupType_group_subgroup_selector.js",
            ],
            refresh: true,
        }),
    ],
});
