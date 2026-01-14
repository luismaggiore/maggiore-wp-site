<?php
if (!defined('ABSPATH')) exit;

function mg_register_cpt_cliente() {
    $labels = [
        'name' => __('Clientes', 'maggiore'),
        'singular_name' => __('Cliente', 'maggiore'),
        'add_new' => __('Agregar nuevo', 'maggiore'),
        'add_new_item' => __('Agregar nuevo cliente', 'maggiore'),
        'edit_item' => __('Editar cliente', 'maggiore'),
        'new_item' => __('Nuevo cliente', 'maggiore'),
        'view_item' => __('Ver cliente', 'maggiore'),
        'search_items' => __('Buscar clientes', 'maggiore'),
        'not_found' => __('No se encontraron clientes', 'maggiore'),
        'menu_name' => __('Clientes', 'maggiore'),
    ];

    $args = [
        'label' => __('Clientes', 'maggiore'),
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-businessman',
        'has_archive' => true,
        'rewrite' => ['slug' => 'clientes'],
        'supports' => ['title', 'thumbnail'],
        'show_in_rest' => true,
    ];

    register_post_type('mg_cliente', $args);
}


add_action('init', 'mg_register_cpt_cliente');
