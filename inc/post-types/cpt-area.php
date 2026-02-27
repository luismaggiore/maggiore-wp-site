<?php
if (!defined('ABSPATH')) exit;

function mg_register_cpt_area() {
    $labels = [
        'name' => __('Áreas', 'maggiore'),
        'singular_name' => __('Área', 'maggiore'),
        'add_new_item' => __('Agregar nueva área', 'maggiore'),
        'edit_item' => __('Editar área', 'maggiore'),
        'view_item' => __('Ver área', 'maggiore'),
        'menu_name' => __('Áreas', 'maggiore'),
    ];

    $args = [
        'label' => __('Áreas', 'maggiore'),
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-networking',
        'has_archive' => true,
        'rewrite' => ['slug' => 'area'],
        'supports' => ['title', 'page-attributes'],
        'show_in_rest' => true,
    ];

    register_post_type('mg_area', $args);
}
add_action('init', 'mg_register_cpt_area');
