<?php
    $context = Timber::get_context();
    $context['post'] = Timber::get_post();
    $context['post']->fields = get_fields($context['post']->ID);
    $context['post']->breadcrumbs = pwc_get_breadcrumbs();

    try {
        Timber::render("assets/views/pages/99-blocks.twig", $context);
    } catch (Exception $e) {
        echo $e->getMessage();
    }