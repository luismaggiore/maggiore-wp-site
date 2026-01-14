<?php
if (!defined('ABSPATH')) exit;

add_action('init', function () {
    $labels = [
        'name'              => __('Equipos', 'maggiore'),
        'singular_name'     => __('Equipo', 'maggiore'),
        'search_items'      => __('Buscar Equiposs', 'maggiore'),
        'all_items'         => __('Todas los Equiposs', 'maggiore'),
        'parent_item'       => __('Equipo superior', 'maggiore'),
        'parent_item_colon' => __('Equipo superior:', 'maggiore'),
        'edit_item'         => __('Editar Equipo', 'maggiore'),
        'update_item'       => __('Actualizar Equipo', 'maggiore'),
        'add_new_item'      => __('Añadir nuevo Equipo', 'maggiore'),
        'new_item_name'     => __('Nombre de nuevo Equipo', 'maggiore'),
        'menu_name'         => __('Equipos', 'maggiore'),
    ];

    register_taxonomy('mg_equipos', ['mg_equipo'], [
        'labels' => $labels,
        'hierarchical' => true,
        'public' => true,
        'show_ui' => true,
        'show_in_rest' => true,
        'show_admin_column' => true,
        'rewrite' => ['slug' => 'equipo'],
    ]);
});
