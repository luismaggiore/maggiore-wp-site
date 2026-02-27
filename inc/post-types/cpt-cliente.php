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
        'has_archive' => 'clientes',  // Archive específico
        'rewrite' => [
            'slug' => 'clientes/%mg_industria%',  // Singles con industria
            'with_front' => false,
        ],
        'supports' => ['title', 'thumbnail', 'page-attributes'],
        'show_in_rest' => true,
    ];

    register_post_type('mg_cliente', $args);
}

/**
 * Filtro para reemplazar %mg_industria% en la URL del cliente
 * Convierte /clientes/%mg_industria%/apple en /clientes/tecnologia/apple
 */
function mg_cliente_permalink_structure($post_link, $post) {
    // Solo aplicar a clientes
    if ($post->post_type !== 'mg_cliente') {
        return $post_link;
    }

    // Si contiene el placeholder %mg_industria%
    if (strpos($post_link, '%mg_industria%') !== false) {
        // Obtener la industria del cliente
        $terms = get_the_terms($post->ID, 'mg_industria');
        
        if ($terms && !is_wp_error($terms)) {
            // Usar la primera industria
            $industry_slug = $terms[0]->slug;
        } else {
            // Si no tiene industria, usar 'sin-industria'
            $industry_slug = 'sin-industria';
        }
        
        // Reemplazar el placeholder con el slug real
        $post_link = str_replace('%mg_industria%', $industry_slug, $post_link);
    }

    return $post_link;
}
add_filter('post_type_link', 'mg_cliente_permalink_structure', 10, 2);

add_action('init', 'mg_register_cpt_cliente');
