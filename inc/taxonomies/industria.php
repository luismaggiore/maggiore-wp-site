<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {
    $labels = [
        'name'              => __('Industrias', 'maggiore'),
        'singular_name'     => __('Industria', 'maggiore'),
        'search_items'      => __('Buscar industrias', 'maggiore'),
        'all_items'         => __('Todas las industrias', 'maggiore'),
        'parent_item'       => __('Industria superior', 'maggiore'),
        'parent_item_colon' => __('Industria superior:', 'maggiore'),
        'edit_item'         => __('Editar industria', 'maggiore'),
        'update_item'       => __('Actualizar industria', 'maggiore'),
        'add_new_item'      => __('Añadir nueva industria', 'maggiore'),
        'new_item_name'     => __('Nombre de nueva industria', 'maggiore'),
        'menu_name'         => __('Industrias', 'maggiore'),
    ];

    register_taxonomy('mg_industria', ['mg_cliente', 'mg_caso_exito'], [
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'industria'],
    ]);
});
