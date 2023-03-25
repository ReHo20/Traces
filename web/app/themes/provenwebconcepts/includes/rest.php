<?php

    use Timber\Timber;

    add_action('rest_api_init', function() {
        //region Add to cart
        register_rest_route('traces', '/edit/', [
            'methods' => 'post',
            'args' => [],
            'callback' => function(WP_REST_Request $request) {
                return retrieve_edit_fields($request);
            },
        ]);
        register_rest_route('traces', '/save/', [
            'methods' => 'post',
            'args' => [],
            'callback' => function(WP_REST_Request $request) {
                return save_frontend_fields($request);
            },
        ]);
        register_rest_route('traces', '/revert/', [
            'methods' => 'post',
            'args' => [],
            'callback' => function(WP_REST_Request $request) {
                return revert_frontend_fields($request);
            },
        ]);
        register_rest_route('traces', '/cancel/', [
            'methods' => 'post',
            'args' => [],
            'callback' => function(WP_REST_Request $request) {
                return cancel_frontend_fields($request);
            },
        ]);
        register_rest_route('traces', '/filters/', [
            'methods' => 'post',
            'args' => [],
            'callback' => function(WP_REST_Request $request) {
                return get_filtered_traces($request);
            },
        ]);
    });

    function retrieve_edit_fields(WP_REST_Request $request): array {
        $formData = $request->get_params();


        $context = Timber::get_context();
        $context['trace_id'] = $formData['id'];
        $context['item'] = get_traces($formData['id'])[0];

        return [
            'status' => 'success',
            'html' => Timber::compile('partials/aside-form-item.twig', $context),
        ];
    }

    function save_frontend_fields(WP_REST_Request $request): array {
        $formData = $request->get_params();

        $fields = [];

        foreach ($formData['fields'] as $item) {
            $fields[$item['name']] = $item['value'];
        }

        update_field('details', $fields, $formData['id']);

        $context = Timber::get_context();
        $context['trace_id'] = $formData['id'];
        $context['item'] = get_traces($formData['id'])[0];
        $context['rest'] = true;

        return [
            'status' => 'success',
            'html' => Timber::compile('partials/aside-item.twig', $context),
        ];
    }

    function revert_frontend_fields(WP_REST_Request $request): array {
        $formData = $request->get_params();

        $context = Timber::get_context();
        $context['trace_id'] = $formData['id'];
        $context['item'] = get_traces($formData['id'])[0];
        $context['rest'] = true;

        return [
            'status' => 'success',
            'html' => Timber::compile('partials/aside-form-item.twig', $context),
        ];
    }

    function cancel_frontend_fields(WP_REST_Request $request): array {
        $formData = $request->get_params();

        $context = Timber::get_context();
        $context['trace_id'] = $formData['id'];
        $context['item'] = get_traces($formData['id'])[0];
        $context['rest'] = true;

        return [
            'status' => 'success',
            'html' => Timber::compile('partials/aside-item.twig', $context),
        ];
    }

    function get_filtered_traces(WP_REST_Request $request): array {
        $formData = $request->get_params();

        $args = [];

        $context = Timber::get_context();

        array_map(function($value, $key) use (&$args) {



            if (taxonomy_exists($key) && array_filter($value)) {

                if (!isset($args['tax_query'])) {
                    $args['tax_query'] = [
                        'relation' => 'OR',
                    ];
                }

                $args['tax_query'][] = [
                    'taxonomy' => $key,
                    'field' => 'slug',
                    'terms' => $value,
                ];

                return $args;

            } else {
                $args[$key] = $value;
            }
        }, $formData, array_keys($formData));

        $context['items'] = pwc_get_posts('trace', $args);

        return [
            'status' => 'success',
            'html' => Timber::compile('partials/traces.twig', $context),
        ];
    }