<?php
/**
 * Sistema de Herencia de Categorías
 * Crea automáticamente categorías en portafolio basadas en servicios asignados
 */

/**
 * Heredar categorías de servicios al guardar portafolio
 */
function mg_heredar_categorias_portafolio($post_id) {
    // Verificar que es el CPT correcto
    if (get_post_type($post_id) !== 'mg_portafolio') {
        return;
    }

    // Evitar loops infinitos
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    // Obtener servicios asignados al portafolio
    $servicios = get_post_meta($post_id, '_mg_portafolio_servicios', true);
    
    if (empty($servicios) || !is_array($servicios)) {
        return;
    }

    $categorias_a_asignar = [];

    // Por cada servicio asignado
    foreach ($servicios as $servicio_id) {
        // Obtener la categoría del servicio
        $categorias_servicio = wp_get_post_terms($servicio_id, 'mg_categoria', ['fields' => 'all']);
        
        if (empty($categorias_servicio) || is_wp_error($categorias_servicio)) {
            continue;
        }

        // Por cada categoría del servicio
        foreach ($categorias_servicio as $cat_servicio) {
            // Buscar si ya existe en portafolio (por slug para evitar duplicados)
            $cat_portafolio = get_term_by('slug', $cat_servicio->slug, 'mg_categoria_portafolio');

            // Si no existe, crearla
            if (!$cat_portafolio) {
                $nuevo_termino = wp_insert_term(
                    $cat_servicio->name,
                    'mg_categoria_portafolio',
                    [
                        'slug'        => $cat_servicio->slug,
                        'description' => $cat_servicio->description,
                    ]
                );

                if (!is_wp_error($nuevo_termino)) {
                    $categorias_a_asignar[] = $nuevo_termino['term_id'];
                }
            } else {
                // Si ya existe, usarla
                $categorias_a_asignar[] = $cat_portafolio->term_id;
            }
        }
    }

    // Asignar todas las categorías heredadas al portafolio
    if (!empty($categorias_a_asignar)) {
        wp_set_object_terms($post_id, $categorias_a_asignar, 'mg_categoria_portafolio');
    }
}
add_action('save_post_mg_portafolio', 'mg_heredar_categorias_portafolio', 20);

/**
 * También heredar cuando se actualizan los servicios asignados
 */
function mg_heredar_categorias_al_cambiar_servicios($meta_id, $post_id, $meta_key, $meta_value) {
    if ($meta_key === '_mg_portafolio_servicios' && get_post_type($post_id) === 'mg_portafolio') {
        mg_heredar_categorias_portafolio($post_id);
    }
}
add_action('updated_post_meta', 'mg_heredar_categorias_al_cambiar_servicios', 10, 4);