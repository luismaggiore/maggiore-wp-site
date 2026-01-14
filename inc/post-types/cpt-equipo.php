<?php
if (!defined('ABSPATH')) exit;

function mg_register_cpt_equipo() {
    $labels = [
        'name' => __('Equipo', 'maggiore'),
        'singular_name' => __('Miembro', 'maggiore'),
        'add_new_item' => __('Agregar miembro', 'maggiore'),
        'edit_item' => __('Editar miembro', 'maggiore'),
        'view_item' => __('Ver miembro', 'maggiore'),
        'menu_name' => __('Equipo', 'maggiore'),
    ];

    $args = [
        'label' => __('Equipo', 'maggiore'),
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-groups',
        'has_archive' => true,
        'rewrite' => ['slug' => 'equipo'],
        'supports' => ['title', 'thumbnail'],
        'show_in_rest' => true,
    ];

    register_post_type('mg_equipo', $args);
}
add_action('init', 'mg_register_cpt_equipo');
