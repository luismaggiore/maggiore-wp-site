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
        'has_archive' => 'equipo',  // Archive específico
        'rewrite' => [
            'slug' => 'equipo/%mg_equipos%',  // Singles con equipo
            'with_front' => false,
        ],
        'supports' => ['title', 'thumbnail', 'page-attributes'],
        'show_in_rest' => true,
    ];

    register_post_type('mg_equipo', $args);
}

/**
 * Filtro para reemplazar %mg_equipos% en la URL del miembro
 * Convierte /equipo/%mg_equipos%/juan-perez en /equipo/diseno/juan-perez
 */
function mg_equipo_permalink_structure($post_link, $post) {
    // Solo aplicar a equipo
    if ($post->post_type !== 'mg_equipo') {
        return $post_link;
    }

    // Si contiene el placeholder %mg_equipos%
    if (strpos($post_link, '%mg_equipos%') !== false) {
        // Obtener el equipo del miembro
        $terms = get_the_terms($post->ID, 'mg_equipos');
        
        if ($terms && !is_wp_error($terms)) {
            // Usar el primer equipo
            $team_slug = $terms[0]->slug;
        } else {
            // Si no tiene equipo, usar 'sin-equipo'
            $team_slug = 'sin-equipo';
        }
        
        // Reemplazar el placeholder con el slug real
        $post_link = str_replace('%mg_equipos%', $team_slug, $post_link);
    }

    return $post_link;
}
add_filter('post_type_link', 'mg_equipo_permalink_structure', 10, 2);

add_action('init', 'mg_register_cpt_equipo');
