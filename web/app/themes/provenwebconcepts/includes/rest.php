<?php
    add_action('rest_api_init', function() {
        //region Add to cart
        register_rest_route('traces', '/edit/', array(
            'methods' => 'post',
            'args' => array(),
            'callback' => function(WP_REST_Request $request) {
                return retrieve_edit_fields($request);
            }
        ));
        register_rest_route('traces', '/save/', array(
            'methods' => 'post',
            'args' => array(),
            'callback' => function(WP_REST_Request $request) {
                return save_frontend_fields($request);
            }
        ));
        register_rest_route('traces', '/revert/', array(
            'methods' => 'post',
            'args' => array(),
            'callback' => function(WP_REST_Request $request) {
                return revert_frontend_fields($request);
            }
        ));
        register_rest_route('traces', '/cancel/', array(
            'methods' => 'post',
            'args' => array(),
            'callback' => function(WP_REST_Request $request) {
                return cancel_frontend_fields($request);
            }
        ));
    });

    function retrieve_edit_fields(WP_REST_Request $request){
        $formData = $request->get_params();


        $context = Timber::get_context();
        $context['trace_id'] = $formData['id'];

        return [
            'status' => 'success',
            'html' => Timber::compile('partials/aside-form-item.twig', $context)
        ];
    }

    function save_frontend_fields(WP_REST_Request $request) {
        $formData = $request->get_params();

        $fields = [];

        foreach($formData['fields'] as $item){
            $fields[$item['name']] = $item['value'];
        }

        update_field('details', $fields, $formData['id']);

        $context = Timber::get_context();
        $context['trace_id'] = $formData['id'];
        $context['item'] = get_traces($formData['id'])[0];
        $context['rest'] = true;



        return [
            'status' => 'success',
            'html' => Timber::compile('partials/aside-item.twig', $context)
        ];
    }

    function revert_frontend_fields(WP_REST_Request $request) {
        $formData = $request->get_params();

        $context = Timber::get_context();
        $context['trace_id'] = $formData['id'];
        $context['item'] = get_traces($formData['id'])[0];
        $context['rest'] = true;

        return [
            'status' => 'success',
            'html' => Timber::compile('partials/aside-form-item.twig', $context)
        ];
    }

    function cancel_frontend_fields(WP_REST_Request $request) {
        $formData = $request->get_params();

        $context = Timber::get_context();
        $context['trace_id'] = $formData['id'];
        $context['item'] = get_traces($formData['id'])[0];
        $context['rest'] = true;

        return [
            'status' => 'success',
            'html' => Timber::compile('partials/aside-item.twig', $context)
        ];
    }