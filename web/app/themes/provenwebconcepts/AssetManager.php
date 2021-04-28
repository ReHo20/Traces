<?php

    class AssetManager {

        public function pwcEnqueueStyle($handle, $uri) {
            wp_enqueue_style($handle, $uri);
        }

        public function pwcPrintToHead() {
            add_action('wp_head', 'pwcEnqueueStyle', 1);
        }
    }