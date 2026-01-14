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
        'has_archive' => 'servicios',  // Archive específico sin placeholder
        'rewrite' => [
            'slug' => 'servicios/%mg_categoria%',  // Singles con categoría
            'with_front' => false,
        ],
        'supports' => ['title'],
        'show_in_rest' => true,
    ];

    register_post_type('mg_servicio', $args);
}

/**
 * Filtro para reemplazar %mg_categoria% en la URL del servicio
 * Convierte /servicios/%mg_categoria%/brandbook en /servicios/diseno/brandbook
 */
function mg_servicio_permalink_structure($post_link, $post) {
    // Solo aplicar a servicios
    if ($post->post_type !== 'mg_servicio') {
        return $post_link;
    }

    // Si contiene el placeholder %mg_categoria%
    if (strpos($post_link, '%mg_categoria%') !== false) {
        // Obtener la categoría del servicio
        $terms = get_the_terms($post->ID, 'mg_categoria');
        
        if ($terms && !is_wp_error($terms)) {
            // Usar la primera categoría
            $category_slug = $terms[0]->slug;
        } else {
            // Si no tiene categoría, usar 'sin-categoria'
            $category_slug = 'sin-categoria';
        }
        
        // Reemplazar el placeholder con el slug real
        $post_link = str_replace('%mg_categoria%', $category_slug, $post_link);
    }

    return $post_link;
}
add_filter('post_type_link', 'mg_servicio_permalink_structure', 10, 2);

add_action('init', 'mg_register_cpt_servicio');
