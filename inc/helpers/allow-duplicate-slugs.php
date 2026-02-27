<?php
/**
 * Permite slugs duplicados para páginas y CPTs en diferentes idiomas con Polylang
 * 
 * Esto permite tener URLs como:
 * - /servicios (español)
 * - /en/services (inglés) 
 * - /pt/servicos (portugués)
 * 
 * Pero también:
 * - /nosotros (español)
 * - /en/nosotros (inglés con mismo slug)
 * - /pt/nosotros (portugués con mismo slug)
 * 
 * @since 1.0.0
 */

if (!defined('ABSPATH')) exit;

/**
 * Permitir slugs duplicados en posts de diferentes idiomas
 */
add_filter('wp_unique_post_slug', 'mg_allow_duplicate_slugs_polylang', 10, 6);

function mg_allow_duplicate_slugs_polylang($slug, $post_ID, $post_status, $post_type, $post_parent, $original_slug) {
    
    // Solo aplicar si Polylang está activo
    if (!function_exists('pll_get_post_language')) {
        return $slug;
    }
    
    // Excluir tipos de post que nunca deben compartir slugs
    $excluded_types = array('revision', 'nav_menu_item', 'attachment');
    if (in_array($post_type, $excluded_types)) {
        return $slug;
    }
    
    // Tipos de post permitidos (páginas, posts y todos los CPTs de Maggiore)
    $allowed_types = array(
        'page',
        'post',
        'mg_servicio',
        'mg_cliente',
        'mg_caso_exito',
        'mg_portafolio',
        'mg_equipo',
        'mg_area',
        'mg_contacto'
    );
    
    if (!in_array($post_type, $allowed_types)) {
        return $slug;
    }
    
    // Obtener el idioma del post actual
    $current_lang = pll_get_post_language($post_ID);
    
    // Si no tiene idioma asignado aún, usar el idioma actual
    if (!$current_lang) {
        $current_lang = pll_current_language();
    }
    
    // Si aún no hay idioma, dejar que WordPress maneje
    if (!$current_lang) {
        return $slug;
    }
    
    // Buscar si existe otro post con el mismo slug
    global $wpdb;
    $check_sql = "SELECT post_name FROM $wpdb->posts WHERE post_name = %s AND post_type = %s AND ID != %d LIMIT 1";
    $post_name_check = $wpdb->get_var($wpdb->prepare($check_sql, $original_slug, $post_type, $post_ID));
    
    // Si existe otro post con ese slug
    if ($post_name_check) {
        // Verificar si es de un idioma diferente
        $existing_post = get_page_by_path($original_slug, OBJECT, $post_type);
        
        if ($existing_post) {
            $existing_lang = pll_get_post_language($existing_post->ID);
            
            // Si es de un idioma diferente, permitir el slug duplicado
            if ($existing_lang && $existing_lang !== $current_lang) {
                return $original_slug;
            }
        }
    }
    
    // En otros casos, usar el comportamiento por defecto de WordPress
    return $slug;
}

/**
 * Permitir slugs duplicados en términos de taxonomías de diferentes idiomas
 */
add_filter('wp_unique_term_slug', 'mg_allow_duplicate_term_slugs_polylang', 10, 3);

function mg_allow_duplicate_term_slugs_polylang($slug, $term, $original_slug) {
    
    // Solo aplicar si Polylang está activo
    if (!function_exists('pll_get_term_language')) {
        return $slug;
    }
    
    // Taxonomías permitidas (todas las de Maggiore)
    $allowed_taxonomies = array(
        'mg_industria',
        'mg_categoria',
        'mg_categoria_portafolio',
        'category',
        'post_tag'
    );
    
    if (!in_array($term->taxonomy, $allowed_taxonomies)) {
        return $slug;
    }
    
    // Obtener el idioma del término actual
    $current_lang = pll_get_term_language($term->term_id);
    
    // Si no tiene idioma asignado aún, usar el idioma actual
    if (!$current_lang) {
        $current_lang = pll_current_language();
    }
    
    // Si aún no hay idioma, dejar que WordPress maneje
    if (!$current_lang) {
        return $slug;
    }
    
    // Buscar términos con el mismo slug en la misma taxonomía
    $existing_term = get_term_by('slug', $original_slug, $term->taxonomy);
    
    if ($existing_term && $existing_term->term_id != $term->term_id) {
        $existing_lang = pll_get_term_language($existing_term->term_id);
        
        // Si es de un idioma diferente, permitir el slug duplicado
        if ($existing_lang && $existing_lang !== $current_lang) {
            return $original_slug;
        }
    }
    
    // En otros casos, usar el comportamiento por defecto de WordPress
    return $slug;
}
