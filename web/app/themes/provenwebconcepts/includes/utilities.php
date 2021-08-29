<?php

    /**
     * Determine if the current visitor is PWC, for development purposes
     * @return bool
     */
    function isPWC(): bool {
        return (in_array($_SERVER['REMOTE_ADDR'], [
            '80.127.149.186' // PWC Kantoor
        ]));
    }

    function pwc_unregister_scripts(array $handles = []) {
        foreach ($handles as $handle) {
            wp_deregister_script($handle);
        }
    }

    function pwc_unregister_styles(array $handles = []) {
        foreach ($handles as $handle) {
            wp_deregister_style($handle);
        }
    }

    /**
     * Determine if a certain block has been added to a page to load in required styles/scripts for that block
     *
     * @param        $layout . The block you want to check for
     * @param string $flexible_content Optional
     * @param null   $type Optional
     *
     * @return bool
     */
    function page_has_block($layout, string $flexible_content = 'blocks', $type = null): bool {
        global $post;
        if (isset($post->ID) && function_exists('get_fields') && !empty(get_fields($post->ID)[$flexible_content])) {
            foreach (get_fields($post->ID)[$flexible_content] as $block) {
                if (isset($type)) {
                    if ($block['acf_fc_layout'] == $layout && $block['blockType'] == $type) {
                        return true;
                    }
                } else {
                    if ($block['acf_fc_layout'] == $layout) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * Return the internationalized version of a dutch phone number
     *
     * @param      $number
     * @param int  $countrycode
     * @param bool $addPlus
     *
     * @return string
     */
    function convertPhone($number, $countrycode = 31, bool $addPlus = true): string {
        $replace = ['(0)', '-', ' '];
        $number = str_replace($replace, '', $number);
        if ($number[0] == '0') {
            $number = ltrim($number, '0');
        }
        if ($number[0] !== '+') {
            if ($addPlus) {
                $number = '+' . $countrycode . $number;
            } else {
                $number = $countrycode . $number;
            }
        }

        return $number;
    }

    /**
     * Determine if a certain cookie has not yet been set
     *
     * @param $cookie
     *
     * @return bool
     */
    function cookie_not_set($cookie): bool {
        return isset($_COOKIE[$cookie]) ? false : true;
    }

    /**
     * Determine if the environment is matched
     *
     * @param $selectedEnvironment
     *
     * @return bool
     */
    function isEnv($selectedEnvironment): bool {
        return $selectedEnvironment === WP_ENV;
    }

    /**
     * Determine on which pages CF7 scripts/styles need to be shown
     * @return bool
     */
    function showCf7(): bool {
        return is_page('contact') || page_has_block('blocks', 'contact');
    }

    /**
     * Returns the contents of a file in at the url
     *
     * @param $url
     *
     * @return array|bool|string|string[]|null
     */
    function url_get_contents($url) {
        if (!function_exists('curl_init') || file_exists($url)) {
            return null;
        }
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $output = curl_exec($ch);
        curl_close($ch);

        return str_replace('../', get_stylesheet_directory_uri() . '/assets/', $output);
    }

    /**
     * Decide which content needs to be displayed for a block. The default or unique version.
     *
     * @param $block
     * @param $layout
     *
     * @return array
     */
    function get_source($block, $layout): array {
        if (!isset($block['source'])) {
            return $block;
        }

        $source = $block['source'] !== 'default' ? $block : [];

        foreach (get_fields('options')[$layout]['group'] as $group) {
            if ($block['select' . ucfirst($layout)] === $group['groupID'] && $block['source'] === 'default') {
                $source = $group;
            }
        }

        return $source;
    }

    /**
     * Generates the breadcrumbs
     *
     * @return array
     */
    function pwc_get_breadcrumbs(): array {

        global $post;

        $breadcrumbs[] = [
            'id' => 0,
            'title' => 'Home',
            'link' => get_home_url()
        ];

        if (!(is_404() || is_search()) and isset($post)) {
            if (is_archive()) {
                $bpost = Timber::get_post(get_post_type_object(get_post_type())->has_archive);
                if (!is_object($bpost)) {
                    $postTypeObject = get_post_type_object(get_post_type());
                    $bpost = [
                        'id' => 0,
                        'post_title' => $postTypeObject->label,
                        'link' => get_post_type_archive_link(get_post_type())
                    ];
                }
            } elseif (is_home()) {
                $bpost = Timber::get_post(get_option('page_for_posts', true));
            } else {
                $bpost = Timber::get_post($post->ID);
            }

            if (is_singular('post')) {
                $pageForPostsId = get_option('page_for_posts', true);
                $breadcrumbs[] = [
                    'id' => $pageForPostsId,
                    'title' => Timber::get_post($pageForPostsId)->title,
                    'link' => get_permalink($pageForPostsId)
                ];
            }

            $postTypeObject = get_post_type_object(get_post_type());

            $customPostTypes = get_post_types([
                'public' => true,
                '_builtin' => false
            ]);

            if (is_singular($customPostTypes) && $customPostTypes) {
                $breadcrumbs[] = [
                    'id' => 0,
                    'title' => ucfirst($postTypeObject->rewrite['slug']),
                    'link' => $postTypeObject->has_archive ? get_post_type_archive_link(get_post_type()) : get_home_url()
                ];
            }

            $bposts = $bpost ? [0 => $bpost] : [];
            $i = 0;

            while (isset($bpost->post_parent) && $bpost->post_parent !== 0) {
                $i ++;
                $bpost = Timber::get_post($bpost->post_parent);
                $bposts[$i] = $bpost;
            }

            if (count($bposts) > 0) {
                foreach (array_reverse($bposts) as $key => $bpost) {
                    $bpost = (array) $bpost;
                    $breadcrumbs[] = [
                        'id' => $bpost['id'],
                        'title' => $bpost['post_title'],
                        'link' => $bpost['link'] ?? get_permalink($bpost['id'])
                    ];
                }
            }
        } else if (is_404()) {
            $breadcrumbs[] = [
                'id' => 0,
                'title' => '404 foutmelding',
                'link' => '#'
            ];
        } else if (is_search()) {
            $breadcrumbs[] = [
                'id' => 0,
                'title' => 'Zoekresultaten voor ' . get_search_query(),
                'link' => '/search/' . get_search_query()
            ];
        }

        return $breadcrumbs;
    }

    /**
     * Include the post-object of a Contact Form 7 form.
     *
     * @param $form
     *
     * @return string
     */
    function pwc_get_form($form): string {
        if (empty($form)) {
            return '';
        }

        global $post;

        $postTitle = is_front_page() ? 'Homepage' : $post->post_title;

        $content = '[contact-form-7 id="' . $form->ID . '" title="' . $form->post_title .
            '" html_class="flex-form" post="' . $postTitle . '" website="' . get_bloginfo('name') . '"]';

        return apply_shortcodes($content);
    }

    /**
     * Check if the current file php-file or twig-template is being loaded in as a flexible block or as a fixed block on the current page
     *
     * @param array $block
     *
     * @return bool
     */
    function is_block(array $block): bool {
        return isset($block['acf_fc_layout']) || isset($block['groupID']);
    }

    /**
     * Get an array of posts of any post_type that is specified
     *
     * @param       $post_type
     * @param array $args
     * @param int   $posts_per_page
     *
     * @return Timber\PostQuery
     */
    function pwc_get_posts($post_type, array $args = [], int $posts_per_page = - 1) {

        $lastArgs = [
            'post_type' => $post_type
        ];

        if (!isset($args['posts_per_page'])) {
            $lastArgs += [
                'posts_per_page' => $posts_per_page
            ];
        }

        if (is_archive() || is_home()) {
            global $paged;
            if (!isset($paged) || !$paged) {
                $paged = 1;
            }
            $lastArgs += [
                'paged' => $paged
            ];
        }

        $args = is_array($args) ? array_merge_recursive($args, $lastArgs) : $lastArgs;

        $posts = new Timber\PostQuery($args);

        foreach ($posts->get_posts() as &$post) {
            $post = get_custom_fields($post);
        }


        return $posts;
    }

    /**
     * Get the custom fields for the current post
     *
     * @param $post
     *
     * @return mixed
     */
    function get_custom_fields($post) {

        $fields = get_fields($post) ? : [];

        foreach ($fields as $key => &$field) {
            if (is_object($field)) {
                $field = get_custom_fields(Timber::get_post($field->ID));
            }
            $post->$key = $field;
        }

        return $post;
    }