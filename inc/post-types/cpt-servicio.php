<?php
if (!defined('ABSPATH')) exit;

function mg_register_cpt_servicio() {
    $labels = [
        'name' => __('Servicios', 'maggiore'),
        'singular_name' => __('Servicio', 'maggiore'),
        'add_new' => __('Agregar nuevo', 'maggiore'),
        'add_new_item' => __('Agregar nuevo servicio', 'maggiore'),
        'edit_item' => __('Editar servicio', 'maggiore'),
        'new_item' => __('Nuevo servicio', 'maggiore'),
        'view_item' => __('Ver servicio', 'maggiore'),
        'search_items' => __('Buscar servicios', 'maggiore'),
        'not_found' => __('No se encontraron servicios', 'maggiore'),
        'menu_name' => __('Servicios', 'maggiore'),
    ];

    $args = [
        'label' => __('Servicios', 'maggiore'),
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-portfolio',
        'has_archive' => true,
        'rewrite' => ['slug' => 'servicios'],
        'supports' => ['title'],
        'show_in_rest' => true,
    ];

    register_post_type('mg_servicio', $args);
}
add_action('init', 'mg_register_cpt_servicio');
