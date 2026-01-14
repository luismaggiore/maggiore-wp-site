<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {
    $labels = [
        'name'              => __('Categorías', 'maggiore'),
        'singular_name'     => __('Categoría', 'maggiore'),
        'search_items'      => __('Buscar Categorías', 'maggiore'),
        'all_items'         => __('Todas las categorías', 'maggiore'),
        'parent_item'       => __('Categoría superior', 'maggiore'),
        'parent_item_colon' => __('Categoría superior:', 'maggiore'),
        'edit_item'         => __('Editar categoría', 'maggiore'),
        'update_item'       => __('Actualizar categoría', 'maggiore'),
        'add_new_item'      => __('Añadir nueva categoría', 'maggiore'),
        'new_item_name'     => __('Nombre de nueva categoría', 'maggiore'),
        'menu_name'         => __('Categorías', 'maggiore'),
    ];

   register_taxonomy('mg_categoria', ['mg_servicio'], [
    'labels' => $labels,
    'hierarchical' => true,
    'public' => true,
    'show_ui' => true,
    'show_in_rest' => true,
    'show_admin_column' => true,
    'rewrite' => [
        'slug' => 'servicios',
        'with_front' => false,
        'hierarchical' => true
    ],
]);
});
