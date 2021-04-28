<?php
    if (function_exists('acf_add_options_page')) {
        acf_add_options_page([
            'page_title' => 'Theme General Settings',
            'menu_title' => 'Theme Settings',
            'menu_slug' => 'acf-options',
            'redirect' => true,
            'icon_url' => 'dashicons-admin-settings'
        ]);

        acf_add_options_sub_page([
            'page_title' => 'Algemene instellingen',
            'menu_title' => 'Algemeen',
            'parent_slug' => 'acf-options',
            'menu_slug' => 'common',
        ]);

        acf_add_options_sub_page([
            'page_title' => 'Bedrijfsgegevens',
            'menu_title' => 'Bedrijfsgegevens',
            'parent_slug' => 'acf-options',
            'menu_slug' => 'company-details',
        ]);

        acf_add_options_sub_page([
            'page_title' => 'Standaard-instellingen voor blokken',
            'menu_title' => 'Blokken',
            'parent_slug' => 'acf-options',
            'menu_slug' => 'default-blocks',
            'update_button' => __('Update blokken', 'acf'),
            'updated_message' => __("Standaardblokken bijgewerkt", 'acf')
        ]);

        acf_add_options_sub_page([
            'page_title' => 'Instellingen voor downloads',
            'menu_title' => 'Downloads',
            'parent_slug' => 'acf-options',
            'menu_slug' => 'downloads',
            'post_id' => 'downloads',
            'update_button' => __('Update downloads', 'acf'),
            'updated_message' => __("Downloads bijgewerkt", 'acf')
        ]);
    }

    function my_acf_json_save_point($path) {
        $path = get_stylesheet_directory() . '/assets/json';

        return $path;
    }

    function my_acf_json_load_point($paths) {
        unset($paths[0]);
        $paths[] = get_stylesheet_directory() . '/assets/json';

        return $paths;
    }


