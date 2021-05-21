<?php
    $context = Timber::get_context();
    $context['post'] = Timber::get_post();
    $context['post']->fields = get_fields($context['post']->ID);

    try {
        Timber::render("assets/views/pages/01-home.twig", $context);
    } catch (Exception $e) {
        echo $e->getMessage();
    }