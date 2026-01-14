<?php
if (!defined('ABSPATH')) exit;

function mg_register_cpt_portafolio() {
    $labels = [
        'name' => __('Portafolio', 'maggiore'),
        'singular_name' => __('Proyecto', 'maggiore'),
        'add_new_item' => __('Agregar proyecto', 'maggiore'),
        'edit_item' => __('Editar proyecto', 'maggiore'),
        'view_item' => __('Ver proyecto', 'maggiore'),
        'menu_name' => __('Portafolio', 'maggiore'),
    ];

    $args = [
        'label' => __('Portafolio', 'maggiore'),
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-format-gallery',
        'has_archive' => true,
        'rewrite' => ['slug' => 'portafolio'],
        'supports' => ['title', 'thumbnail'],
        'show_in_rest' => true,
    ];

    register_post_type('mg_portafolio', $args);
}
add_action('init', 'mg_register_cpt_portafolio');
