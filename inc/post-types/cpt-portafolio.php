<?php
if (!defined('ABSPATH')) exit;

function mg_register_cpt_portafolio() {

    $labels = [
        'name'               => __('Portafolio', 'maggiore'),
        'singular_name'      => __('Proyecto', 'maggiore'),
        'add_new_item'       => __('Agregar proyecto', 'maggiore'),
        'edit_item'          => __('Editar proyecto', 'maggiore'),
        'view_item'          => __('Ver proyecto', 'maggiore'),
        'menu_name'          => __('Portafolio', 'maggiore'),
    ];

    $args = [
        'label'             => __('Portafolio', 'maggiore'),
        'labels'            => $labels,
        'public'            => true,
        'publicly_queryable'=> true,
        'show_ui'           => true,
        'show_in_menu'      => true,
        'menu_icon'         => 'dashicons-format-gallery',

        // Archive del CPT: /portafolio
        'has_archive'       => 'portafolio',

        // Single del CPT: /portafolio/proyecto/nombre-del-proyecto
        'rewrite'           => [
            'slug'       => 'portafolio/proyecto',
            'with_front' => false,
        ],

        // (opcional) para que aparezca el metabox de la taxonomía
        'taxonomies'        => ['mg_categoria_portafolio'],

        'supports'          => ['title', 'thumbnail'],
        'show_in_rest'      => true,
    ];

    register_post_type('mg_portafolio', $args);
}

// prioridad baja para registrar antes que la taxonomía
add_action('init', 'mg_register_cpt_portafolio', 5);
