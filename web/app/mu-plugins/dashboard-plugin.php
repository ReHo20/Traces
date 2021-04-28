<?php

    /**
     * Plugin Name:  PWC Dashboard
     * Plugin URI:   https://provenwebconcepts.nl/
     * Description:  Customizations to the WordPress Dashboard
     * Version:      1.0.0
     * Author:       Proven Web Concepts
     * Author URI:   https://provenwebconcepts.nl/
     */

    remove_action('welcome_panel', 'wp_welcome_panel');

    function pwc_welcome_panel() { ?>
        <div class="welcome-panel-content">
            <h2>Welkom!</h2>
            <p class="about-description">Wat wil je doen?</p>
            <div class="welcome-panel-column-container">
                <div class="welcome-panel-column">
                    <h3><?php _e('Next Steps'); ?></h3>
                    <ul>
                        <?php if ('page' == get_option('show_on_front') && !get_option('page_for_posts')) : ?>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-edit-page">' .
                                    __('Edit your front page') .
                                    '</a>', get_edit_post_link(get_option('page_on_front'))); ?></li>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-add-page">' .
                                    __('Add additional pages') .
                                    '</a>', admin_url('post-new.php?post_type=page')); ?></li>
                        <?php elseif ('page' == get_option('show_on_front')) : ?>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-edit-page">' .
                                    __('Edit your front page') .
                                    '</a>', get_edit_post_link(get_option('page_on_front'))); ?></li>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-add-page">' .
                                    __('Add additional pages') .
                                    '</a>', admin_url('post-new.php?post_type=page')); ?></li>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-write-blog">' .
                                    __('Add a blog post') . '</a>', admin_url('post-new.php')); ?></li>
                        <?php else : ?>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-write-blog">' .
                                    __('Write your first blog post') . '</a>', admin_url('post-new.php')); ?></li>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-add-page">' .
                                    __('Add an About page') . '</a>', admin_url('post-new.php?post_type=page')); ?></li>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-setup-home">' .
                                    __('Set up your homepage') .
                                    '</a>', current_user_can('customize') ? add_query_arg('autofocus[section]', 'static_front_page', admin_url('customize.php')) : admin_url('options-reading.php')); ?></li>
                        <?php endif; ?>
                        <li><?php printf('<a href="%s" class="welcome-icon welcome-view-site">' . __('View your site') .
                                '</a>', home_url('/')); ?></li>
                    </ul>
                </div>
                <div class="welcome-panel-column ">
                    <h3><?php _e('More Actions'); ?></h3>
                    <ul>
                        <?php if (current_theme_supports('widgets')) : ?>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-widgets">' .
                                    __('Manage widgets') . '</a>', admin_url('widgets.php')); ?></li>
                        <?php endif; ?>
                        <?php if (current_theme_supports('menus')) : ?>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-menus">' . __('Manage menus') .
                                    '</a>', admin_url('nav-menus.php')); ?></li>
                        <?php endif; ?>
                        <?php if (current_user_can('manage_options')) : ?>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-comments">' .
                                    __('Turn comments on or off') .
                                    '</a>', admin_url('options-discussion.php')); ?></li>
                        <?php endif; ?>
                        <li><?php printf('<a href="%s" class="welcome-icon welcome-learn-more">' .
                                __('Learn more about getting started') .
                                '</a>', __('https://wordpress.org/support/article/first-steps-with-wordpress-b/')); ?></li>
                    </ul>
                </div>
                <div class="welcome-panel-column welcome-panel-last">
                    <h3>Assistentie</h3>
                    <ul>
                        <?php if ('page' == get_option('show_on_front') && !get_option('page_for_posts')) : ?>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-edit-page">' .
                                    __('Edit your front page') .
                                    '</a>', get_edit_post_link(get_option('page_on_front'))); ?></li>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-add-page">' .
                                    __('Add additional pages') .
                                    '</a>', admin_url('post-new.php?post_type=page')); ?></li>
                        <?php elseif ('page' == get_option('show_on_front')) : ?>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-edit-page">' .
                                    __('Edit your front page') .
                                    '</a>', get_edit_post_link(get_option('page_on_front'))); ?></li>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-add-page">' .
                                    __('Add additional pages') .
                                    '</a>', admin_url('post-new.php?post_type=page')); ?></li>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-write-blog">' .
                                    __('Add a blog post') . '</a>', admin_url('post-new.php')); ?></li>
                        <?php else : ?>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-write-blog">' .
                                    __('Write your first blog post') . '</a>', admin_url('post-new.php')); ?></li>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-add-page">' .
                                    __('Add an About page') . '</a>', admin_url('post-new.php?post_type=page')); ?></li>
                            <li><?php printf('<a href="%s" class="welcome-icon welcome-setup-home">' .
                                    __('Set up your homepage') .
                                    '</a>', current_user_can('customize') ? add_query_arg('autofocus[section]', 'static_front_page', admin_url('customize.php')) : admin_url('options-reading.php')); ?></li>
                        <?php endif; ?>
                        <li><?php printf('<a href="%s" class="welcome-icon welcome-view-site">' . __('View your site') .
                                '</a>', home_url('/')); ?></li>
                    </ul>
                </div>
            </div>
        </div>
    <?php }

//    add_action( 'welcome_panel', 'pwc_welcome_panel' );
