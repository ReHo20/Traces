<?php
    require_once(__DIR__ . '/PWCLogger.php');
    require_once(__DIR__ . '/includes/pwc-acf-config.php');
    require_once(__DIR__ . '/includes/utilities.php');
    require_once(__DIR__ . '/includes/rest.php');
    add_action('wp_enqueue_scripts', 'scripts');
    add_action('wp_enqueue_scripts', 'styles');
    add_action('admin_head', 'styles_and_scripts_in_wpadmin');
    add_action('init', 'menus');
    add_action('wp_before_admin_bar_render', 'admin_bar_remove', 0);
    add_filter('timber/context', 'add_to_context');
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_filter('timber/twig', function($twig) {
        $functions = [
            'isPWC',
            'do_shortcode',
            'convertPhone',
            'cookie_not_set',
            'isEnv',
            'is_home',
            'is_archive',
            'is_front_page',
            'get_source',
            'is_block',
            'pwc_get_form',
            'pwc_get_posts',
            'get_current_blog_id',
            'add_leading_zeros',
            'is_user_logged_in',
            'current_user_can',
            'get_traces',
            'is_singular',
            'wp_logout_url',
            'get_home_url',
            'wp_get_current_user',
            'field',
            'get_contractors',
            'wp_login_url',
            'get_login_redirect_url',
            'wp_lostpassword_url',
        ];
        foreach ($functions as $function) {
            $twig->addFunction(new Timber\Twig_Function($function, $function));
        }

        return $twig;
    });
    add_filter('acf/settings/save_json', 'my_acf_json_save_point');
    add_filter('acf/settings/load_json', 'my_acf_json_load_point');
    add_theme_support('post-thumbnails');
    add_theme_support('title-tag');
    add_filter('shortcode_atts_wpcf7', 'custom_shortcode_atts_wpcf7_filter', 10, 3);
    add_filter('post_mime_types', 'custom_mime_types');

    if (isPWC() && is_user_logged_in()) {
        show_admin_bar(true);
    }

    function admin_bar_remove() {
        global $wp_admin_bar;

        /* Remove their stuff */
        $wp_admin_bar->remove_menu('search');
        $wp_admin_bar->remove_menu('customize');
        $wp_admin_bar->remove_menu('wp-logo');
        $wp_admin_bar->remove_menu('comments');
    }


    // Menu's
    function menus() {
        $menus = [
            'mainMenu' => __('Hoofdmenu'),
            'disclaimers' => __('Disclaimer')
        ];
        foreach (range(1, 5) as $index) {
            $menus += [
                'footer' . $index => __('Footer Locatie ' . $index)
            ];
        }
        register_nav_menus($menus);
    }

    function add_to_context($data) {
        $data['mainMenu'] = new \Timber\Menu("mainMenu");
        $data['disclaimers'] = new \Timber\Menu("disclaimers");
        $data['footer'] = [];
        foreach (range(1, 5) as $index) {
            if (has_nav_menu('footer' . $index)) {
                $data['footer'] += [
                    $index => new \Timber\Menu("footer" . $index)
                ];
            }
        }
        if (function_exists('get_fields')) {
            $data['options'] = get_fields('options');
        }


        return $data;
    }

    function styles() {
        wp_enqueue_style('fonts', 'https://fonts.googleapis.com/css2?family=Open+Sans:ital,wght@0,300;0,400;0,600;0,700;0,800;1,300;1,400;1,600;1,700;1,800&display=swap', '', '');
        wp_add_inline_style('fonts', url_get_contents(get_template_directory_uri() . '/assets/css/critical.css'));
        wp_enqueue_style('default', get_template_directory_uri() . '/assets/css/default.css', [], '1.0.2');
        wp_add_inline_style('default', get_extra_traces_styling());
        if (showCf7() && function_exists('wpcf7_enqueue_styles')) {
            wpcf7_enqueue_styles();
        }
        if (is_front_page() || is_singular('trace')) {
            wp_enqueue_style('leaflet-css', get_template_directory_uri() .
                '/assets/js/leaflet/leaflet.css', [], '1.0.0');
        }
    }

    function scripts() {
        if (!is_admin()) {
            wp_deregister_script('jquery');
            wp_enqueue_script('jquery', 'https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.6.0.min.js', [], '3.3.1', true);
        }
        //wp_enqueue_script('jquery.slicknav', get_template_directory_uri() .
        // '/assets/lib/slicknav/jquery.slicknav.min.js', [], '1.0.10', true);
        //wp_enqueue_script('wow.min', get_template_directory_uri() . '/assets/lib/wow/wow.min.js', [], '3.5.2', true);
        //wp_enqueue_script('menu-config', get_template_directory_uri() .
        //  '/assets/js/menu-config.min.js', [], '1.0.0', true);
        //wp_enqueue_script('lazy', get_template_directory_uri() . '/assets/js/lazy.min.js', [], '1.0.0', true);

        if (showCf7() && function_exists('wpcf7_enqueue_scripts')) {
            wpcf7_enqueue_scripts();
        }
        if (isEnv('production') && cookie_not_set('cookie-acceptance')) {
            wp_enqueue_script('cookie-js', get_template_directory_uri() .
                '/assets/js/cookie.min.js', [], '1.0.0', true);
        }
        if (is_front_page() || is_singular('trace')) {
            wp_enqueue_script('leaflet-js', get_template_directory_uri() .
                '/assets/js/leaflet/leaflet.js', [], '1.0.0', true);
            wp_enqueue_script('leaflet-providers', get_template_directory_uri() .
                '/assets/lib/leaflet-providers.js', [], '1.0.0', true);
            wp_enqueue_script('leaflet-config', get_template_directory_uri() .
                '/assets/js/leaflet-config.js', [], '1.0.1', true);
            wp_enqueue_script('trace-management-front', get_template_directory_uri() .
                '/assets/js/trace-management-front.js', [], '1.0.0', true);
        }

        wp_localize_script('leaflet-js', 'latlngs', get_coordinates());

    }

    function styles_and_scripts_in_wpadmin() {
        wp_enqueue_script('wp-admin.js', get_template_directory_uri() . '/assets/js/wp-admin.js', [], '1.0.0', true);
    }

    // Optimize site side, with removing not needed stuff
    if (!is_admin()) {
        add_action("wp_enqueue_scripts", function() {
            if (!is_admin_bar_showing()) {
                pwc_unregister_styles(['dashicons', 'wp-block-library']);
            }
        }, 11);

        remove_action('wp_head', 'print_emoji_detection_script', 7);
        remove_action('admin_print_scripts', 'print_emoji_detection_script');
        remove_action('wp_print_styles', 'print_emoji_styles');
        remove_action('admin_print_styles', 'print_emoji_styles');
        remove_action('wp_head', 'wp_oembed_add_host_js');

        add_filter('tiny_mce_plugins', 'disable_embeds_tiny_mce_plugin');
    }

    $blocks = [];

    foreach ($blocks as $block) {
        add_filter('acf/load_field/name=select' . ucfirst($block), function($field) use ($block) {

            // reset choices
            $field['choices'] = [];

            // if has rows
            if (have_rows($block, 'options')) {
                while (have_rows($block, 'options')) {
                    the_row();

                    if (get_sub_field('group')) {
                        foreach (get_sub_field('group') as $key => &$item) {
                            $value = $item['groupID'];
                            $label = $item['name'];
                            $field['choices'][$value] = $label;
                        }
                    }
                }
            }

            return $field;
        });
    }

    function custom_shortcode_atts_wpcf7_filter($out, $pairs, $atts) {
        $my_attributes = ['website', 'post'];

        foreach ($my_attributes as $value) {
            if (isset($atts[$value])) {
                $out[$value] = $atts[$value];
            }
        }

        return $out;
    }

    function import($url) {
        $json = file_get_contents($url);

        return json_decode($json, true);
    }

    function geojson_import() {
        $geojson = '{
            "type": "FeatureCollection",
            "name": "110KV-kabels",
            "crs": { "type": "name", "properties": { "name": "urn:ogc:def:crs:OGC:1.3:CRS84" } },
            "features": [
            { "type": "Feature", "properties": { "fid": 33, "kV": "110" }, "geometry": { "type": "LineString", "coordinates": [ [ 6.029081691424082, 53.212576605841122 ], [ 6.028564282209855, 53.2137803672667 ], [ 6.027261792929616, 53.216301309405935 ], [ 6.026057365520296, 53.218501083535507 ], [ 6.024797574896799, 53.220749500738094 ], [ 6.023487238432289, 53.223171919045143 ], [ 6.022088152434641, 53.225658378436911 ], [ 6.020719199948947, 53.22817511049778 ], [ 6.019325265855809, 53.230627472068235 ], [ 6.017957647737379, 53.233052808346912 ], [ 6.016623314680248, 53.235519149858192 ], [ 6.015252926488257, 53.237980288994088 ], [ 6.0131180714383, 53.239621442511506 ], [ 6.010595628497033, 53.241934482054674 ], [ 6.008049054529702, 53.244249381846025 ], [ 6.005554802554093, 53.246483351126201 ], [ 6.003099981748693, 53.248777964577705 ], [ 6.00058810894593, 53.251069234254317 ], [ 5.998123652668265, 53.253353042585623 ], [ 5.995604317957974, 53.255576175053541 ], [ 5.993209742993155, 53.25776817209313 ], [ 5.990962670354839, 53.259844740836201 ], [ 5.988481970310401, 53.262106928333246 ], [ 5.986006637287431, 53.264343958512285 ], [ 5.983534084935157, 53.266584503654208 ], [ 5.981022486896638, 53.268828770768266 ], [ 5.978829719051612, 53.27080989928649 ], [ 5.976332374844795, 53.273039663722564 ], [ 5.97385904240155, 53.275296124106376 ], [ 5.971372553108573, 53.277482738896474 ], [ 5.968874872918617, 53.2797445834535 ], [ 5.966374086938709, 53.282017135491024 ], [ 5.963925971707498, 53.284224897613839 ], [ 5.961439106922771, 53.28645965556079 ], [ 5.961696015524797, 53.289141609082527 ], [ 5.961953216888262, 53.291843256547132 ], [ 5.962160285437567, 53.294145710299965 ], [ 5.96242929002751, 53.296831175342518 ], [ 5.96267749635298, 53.299523901723425 ], [ 5.962943410659665, 53.302196837557204 ], [ 5.963200866901914, 53.304905634275634 ], [ 5.963423258143434, 53.307225914180179 ], [ 5.96368364645146, 53.309925735542564 ], [ 5.963824185222635, 53.311712638254519 ], [ 5.966396606195357, 53.313966077914472 ], [ 5.968924365485047, 53.316214313743821 ], [ 5.971512498285811, 53.318483691282893 ], [ 5.974028456841501, 53.320705003511229 ], [ 5.976193101779344, 53.322593038631389 ], [ 5.978848600392418, 53.324919242660663 ], [ 5.981217439686334, 53.327047986295923 ], [ 5.9814768884157, 53.32748492087903 ] ] } },
            { "type": "Feature", "properties": { "fid": 34, "kV": "110" }, "geometry": { "type": "LineString", "coordinates": [ [ 5.9814768884157, 53.32748492087903 ], [ 5.981217442086048, 53.32704799095125 ], [ 5.978848600392418, 53.324919242660663 ], [ 5.976193101779344, 53.322593038631389 ], [ 5.974028456841501, 53.320705003511229 ], [ 5.971512498285811, 53.318483691282893 ], [ 5.96892436787248, 53.316214318394159 ], [ 5.96639660858026, 53.313966082563752 ], [ 5.963824187604968, 53.311712642902776 ], [ 5.963683648833607, 53.309925740189918 ], [ 5.963423258143434, 53.307225914180179 ], [ 5.963200869283457, 53.304905638920509 ], [ 5.962943410659665, 53.302196837557204 ], [ 5.962677498733871, 53.29952390636565 ], [ 5.96242929002751, 53.296831175342518 ], [ 5.962160287817811, 53.294145714939496 ], [ 5.961953216888262, 53.291843256547132 ], [ 5.961696015524797, 53.289141609082527 ], [ 5.961439109302106, 53.286459660196527 ], [ 5.963925974089202, 53.284224902248397 ], [ 5.966374089322748, 53.282017140124388 ], [ 5.968874872918617, 53.2797445834535 ], [ 5.971372553108573, 53.277482738896474 ], [ 5.97385904240155, 53.275296124106376 ], [ 5.976332374844795, 53.273039663722564 ], [ 5.978829719051612, 53.27080989928649 ], [ 5.981022486896638, 53.268828770768266 ], [ 5.983534087335562, 53.266584508279315 ], [ 5.986006637287431, 53.264343958512285 ], [ 5.988481972715525, 53.262106932955959 ], [ 5.990962672762328, 53.259844745457691 ], [ 5.993209745402788, 53.257768176713526 ], [ 5.995604320369887, 53.255576179672772 ], [ 5.998123655082588, 53.253353047203653 ], [ 6.000588111362603, 53.251069238871104 ], [ 6.003099984167762, 53.248777969193256 ], [ 6.005554804975503, 53.246483355740509 ], [ 6.008049054529702, 53.244249381846025 ], [ 6.010595628497033, 53.241934482054674 ], [ 6.0131180714383, 53.239621442511506 ], [ 6.015252926488257, 53.237980288994088 ], [ 6.016623314680248, 53.235519149858192 ], [ 6.017957650170572, 53.233052812954057 ], [ 6.019325268290279, 53.23062747667413 ], [ 6.020719202384717, 53.228175115102381 ], [ 6.022088152434641, 53.225658378436911 ], [ 6.02348724087064, 53.223171923647101 ], [ 6.024797577336369, 53.220749505338823 ], [ 6.026057365520296, 53.218501083535507 ], [ 6.027261792929616, 53.216301309405935 ], [ 6.028564284652934, 53.213780371863741 ], [ 6.029081691424082, 53.212576605841122 ] ] } }
            ]
            }';


        return json_decode($geojson, true)['features'][0]['geometry']['coordinates'];
    }

    function cc_mime_types($mimes) {
        $mimes['json'] = 'application/json';
        $mimes['geojson'] = 'application/geo+json';

        return $mimes;
    }

    add_filter('upload_mimes', 'cc_mime_types');


    function add_leading_zeros($string, $length = 12): string {
        return str_pad($string, $length, '0', STR_PAD_LEFT);
    }

    function get_traces($id = '') {

        $args = [
            'orderby' => 'post_title',
            'order' => 'ASC'
        ];

        if ($id) {
            $args += [
                'p' => $id
            ];
        } elseif (!is_front_page()) {
            global $post;
            $args += [
                'post__in' => [$post->ID]
            ];
        }

        $traces = pwc_get_posts('trace', $args);

        //        if($traces) {
        foreach ($traces as $key => $item) {
            $item->state = get_states($item)[0] ?? [];
            if (empty($item->state)) {
                continue;
            }
            if (!(is_user_logged_in() || defined('REST_REQUEST')) &&
                !get_field('visibility', 'term_' . $item->state->term_id)) {
                unset($traces[$key]);
            }
        }

        //        }

        return $traces->get_posts();
    }

    function get_coordinates(): array {
        $traces = get_traces();

        $coordinates = [];

        foreach ($traces as $key => $item) {
            if (empty($item->trace['url'])) {
                continue;
            }
            $json = import($item->trace['url']);
            $coordinates[$key]['color'] = get_color($item);
            $coordinates[$key]['id'] = $item->ID;
            foreach ($json['features'] as $sec => $feature) {

                $coordinates[$key]['coordinates'][$sec] = $feature['geometry']['coordinates'];

                foreach ($coordinates[$key]['coordinates'][$sec] as &$points) {
                    $points = array_reverse($points);
                    foreach ($points as &$abc) {
                        $abc = is_array($abc) ? array_reverse($abc) : $abc;
                    }
                }
            }
        }

        return $coordinates;
    }

    function get_color($project) {
        if (!is_user_logged_in()) {

            $state = !empty(get_the_terms($project, 'state')) ? get_the_terms($project, 'state')[0] : [];

            return !empty($state) ? get_field('color', 'term_' . $state->term_id) : '';
        } else {
            return get_field('color', $project->details['contractor']);
        }
    }

    $traceCapabilities = [
        'edit_trace' => true,
        'read_trace' => true,
        'delete_trace' => true,
        'edit_traces' => true,
        'edit_others_traces' => true,
        'delete_traces' => true,
        'publish_traces' => true,
        'read_private_traces' => true,
        'delete_private_traces' => true,
        'delete_published_traces' => true,
        'delete_others_traces' => true,
        'edit_private_traces' => true,
        'edit_published_traces' => true,
    ];

    add_role('employee', 'Werknemer', ['read' => true]);
    add_role('project-manager', 'Projectleider', ['read' => true, 'list_users' => true, 'create_users' => true, 'create_posts' => false]);

    remove_role('subscriber');
    remove_role('contributor');
    remove_role('editor');
    remove_role('author');

    $administrator = get_role('administrator');
    $projectManager = get_role('project-manager');

    foreach($traceCapabilities as $capability => $grand){
        $administrator->add_cap($capability);
        $projectManager->add_cap($capability);
    }

    function get_contractors(): array {
        $args = [
            'orderby' => 'post_title',
            'order' => 'ASC'
        ];

        $contractors = pwc_get_posts('contractor', $args);

        return $contractors->get_posts();
    }

    function get_extra_traces_styling(): string {
        $states = get_states();
        $contractors = get_contractors();

        $css = '';

        if ($states) {
            foreach ($states as $state) {
                $css .= '
                .' . $state->slug . ':before{
                    background-color:' . $state->color . ';
                }
                
            ';
            }
        }

        if ($contractors) {
            foreach ($contractors as $state) {
                $css .= '
                .' . $state->post_name . ':before{
                    background-color:' . $state->color . ';
                }
                
            ';
            }
        }

        return $css;
    }

    function get_states($trace = 0) {
        $states = $trace !== 0 ? get_the_terms($trace->id, 'state') : get_terms([
            'taxonomy' => 'state',
            'hide_empty' => false
        ]);

        if ($states) {
            foreach ($states as &$state) {
                $state = get_custom_fields($state);
            }
        }

        return $states;
    }

    function get_login_redirect_url(): string {
        return !empty($_REQUEST['redirect_to']) ? $_REQUEST['redirect_to'] : admin_url();
    }

    function custom_mime_types($post_mime_types){
        $post_mime_types['application/octet-stream'] = [
            'GeoJSON',
            'GeoJSON beheren',
            [
                'GeoJSON (%s)',
                'GeoJSON (%s)',
                'singular' => 'GeoJSON (%s)',
                'plural' => 'GeoJSON (%s)',
                'context' => '',
                'domain' => '',
            ]
        ];

        return $post_mime_types;
    }

