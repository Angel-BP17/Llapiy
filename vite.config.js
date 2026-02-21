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
                "resources/js/users/index.js",
                "resources/js/roles/index.js",
                "resources/js/campos/index.js",
                "resources/js/document_types/index.js",
                "resources/js/blocks/index.js",
                "resources/js/documents/index.js",
                "resources/js/sections/index.js",
                "resources/js/andamios/index.js",
                "resources/js/boxes/index.js",
                "resources/js/home/charts-home.js",
            ],
            refresh: true,
        }),
    ],
});
