<?php
if (!defined('ABSPATH')) exit;

function mg_register_cpt_caso_exito() {
    $labels = [
        'name' => __('Casos de Éxito', 'maggiore'),
        'singular_name' => __('Caso de Éxito', 'maggiore'),
        'add_new_item' => __('Agregar caso de éxito', 'maggiore'),
        'edit_item' => __('Editar caso de éxito', 'maggiore'),
        'view_item' => __('Ver caso de éxito', 'maggiore'),
        'menu_name' => __('Casos de Éxito', 'maggiore'),
    ];

    $args = [
        'label' => __('Casos de Éxito', 'maggiore'),
        'labels' => $labels,
        'public' => true,
        'menu_icon' => 'dashicons-star-filled',
        'has_archive' => 'casos-de-exito',  // Archive específico
        'rewrite' => [
            'slug' => 'casos-de-exito/%mg_industria%',  // Singles con industria
            'with_front' => false,
        ],
        'supports' => ['title','thumbnail'],
        'show_in_rest' => true,
    ];

    register_post_type('mg_caso_exito', $args);
}

/**
 * Filtro para reemplazar %mg_industria% en la URL del caso de éxito
 * Convierte /casos-de-exito/%mg_industria%/downy en /casos-de-exito/limpieza/downy
 * 
 * NOTA: La industria es del PROYECTO, NO del cliente
 * Ejemplo: Downy (P&G) puede ser "Limpieza" aunque P&G sea "Consumo Masivo"
 */
function mg_caso_exito_permalink_structure($post_link, $post) {
    // Solo aplicar a casos de éxito
    if ($post->post_type !== 'mg_caso_exito') {
        return $post_link;
    }

    // Si contiene el placeholder %mg_industria%
    if (strpos($post_link, '%mg_industria%') !== false) {
        // Obtener la industria del caso (NO del cliente)
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
add_filter('post_type_link', 'mg_caso_exito_permalink_structure', 10, 2);

add_action('init', 'mg_register_cpt_caso_exito');
