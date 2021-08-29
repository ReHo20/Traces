<?php

    use Timber\Timber;

    $context = Timber::get_context();
    $context['post'] = [
        'title' => '404 foutmelding',
        'thumbnail' => [
            'src' => $context['options']['error']['thumbnail']['url']
        ],
        'fields' => get_fields('options')['error'],
        'breadcrumbs' => pwc_get_breadcrumbs()
    ];

    try {
        Timber::render("assets/views/pages/404.twig", $context);
    } catch (Exception $e) {
        echo $e->getMessage();
    }