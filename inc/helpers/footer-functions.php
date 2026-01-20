<?php
/**
 * Funciones adicionales para el Footer
 * Agregar estas funciones a functions.php
 * 
 * @package Maggiore
 */

if (!defined('ABSPATH')) exit;

/* ========================================
 * REGISTRAR MENÚ DEL FOOTER
 * ======================================== */
function maggiore_register_footer_menu() {
    register_nav_menus([
        'footer-menu' => __('Menú Footer', 'maggiore')
    ]);
}
add_action('init', 'maggiore_register_footer_menu');

/* ========================================
 * ENQUEUE FOOTER CSS
 * Agregar esto a la función maggiore_scripts() existente
 * ======================================== */
function maggiore_footer_styles() {
    wp_enqueue_style(
        'footer-styles',
        get_template_directory_uri() . '/assets/css/footer-styles.css',
        [],
        '1.0.0'
    );
    
    // Bootstrap Icons (si no está cargado ya)
    if (!wp_style_is('bootstrap-icons', 'enqueued')) {
        wp_enqueue_style(
            'bootstrap-icons',
            'https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css',
            [],
            '1.11.1'
        );
    }
}
add_action('wp_enqueue_scripts', 'maggiore_footer_styles');

/* ========================================
 * BREADCRUMBS MEJORADOS (OPCIONAL)
 * Función auxiliar para breadcrumbs más avanzados
 * ======================================== */
function maggiore_breadcrumbs() {
    // No mostrar en home
    if (is_front_page() || is_home()) {
        return;
    }
    
    $separator = '<span class="breadcrumb-separator">›</span>';
    $home_title = __('Inicio', 'maggiore');
    
    echo '<nav aria-label="breadcrumb" class="breadcrumb-container">';
    echo '<div class="container">';
    echo '<ol class="breadcrumb">';
    
    // Home
    echo '<li class="breadcrumb-item">';
    echo '<a href="' . esc_url(home_url('/')) . '"><i class="bi bi-house-door"></i> ' . $home_title . '</a>';
    echo '</li>';
    
    if (is_singular()) {
        $post_type = get_post_type();
        $post_type_object = get_post_type_object($post_type);
        
        // Enlace al archivo si es CPT
        if ($post_type !== 'post' && $post_type !== 'page') {
            $archive_link = get_post_type_archive_link($post_type);
            if ($archive_link) {
                echo '<li class="breadcrumb-item">';
                echo '<a href="' . esc_url($archive_link) . '">' . esc_html($post_type_object->labels->name) . '</a>';
                echo '</li>';
            }
        }
        
        // Categoría/Taxonomía si existe
        if ($post_type === 'post') {
            $categories = get_the_category();
            if (!empty($categories)) {
                $category = $categories[0];
                echo '<li class="breadcrumb-item">';
                echo '<a href="' . esc_url(get_category_link($category->term_id)) . '">' . esc_html($category->name) . '</a>';
                echo '</li>';
            }
        } else {
            // Para CPTs, obtener la primera taxonomía asociada
            $taxonomies = get_object_taxonomies($post_type);
            if (!empty($taxonomies)) {
                $terms = get_the_terms(get_the_ID(), $taxonomies[0]);
                if (!empty($terms) && !is_wp_error($terms)) {
                    $term = $terms[0];
                    echo '<li class="breadcrumb-item">';
                    echo '<a href="' . esc_url(get_term_link($term)) . '">' . esc_html($term->name) . '</a>';
                    echo '</li>';
                }
            }
        }
        
        // Título actual
        echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_title() . '</li>';
        
    } elseif (is_archive()) {
        if (is_category()) {
            $category = get_queried_object();
            if ($category->parent) {
                $parent = get_category($category->parent);
                echo '<li class="breadcrumb-item">';
                echo '<a href="' . esc_url(get_category_link($parent->term_id)) . '">' . esc_html($parent->name) . '</a>';
                echo '</li>';
            }
            echo '<li class="breadcrumb-item active" aria-current="page">' . single_cat_title('', false) . '</li>';
        } elseif (is_tax()) {
            $term = get_queried_object();
            $taxonomy = get_taxonomy($term->taxonomy);
            echo '<li class="breadcrumb-item active" aria-current="page">' . esc_html($term->name) . '</li>';
        } elseif (is_post_type_archive()) {
            echo '<li class="breadcrumb-item active" aria-current="page">' . post_type_archive_title('', false) . '</li>';
        } else {
            echo '<li class="breadcrumb-item active" aria-current="page">' . get_the_archive_title() . '</li>';
        }
    } elseif (is_search()) {
        echo '<li class="breadcrumb-item active" aria-current="page">' . __('Resultados de búsqueda', 'maggiore') . '</li>';
    } elseif (is_404()) {
        echo '<li class="breadcrumb-item active" aria-current="page">' . __('Página no encontrada', 'maggiore') . '</li>';
    }
    
    echo '</ol>';
    echo '</div>';
    echo '</nav>';
}

/* ========================================
 * ACTUALIZAR REWRITE RULES
 * Ejecutar una sola vez después de instalar
 * ======================================== */
function maggiore_flush_rewrite_rules_on_activation() {
    // Registrar los CPTs
    maggiore_register_footer_menu();
    
    // Flush rewrite rules
    flush_rewrite_rules();
}
// Descomentar esta línea UNA VEZ después de agregar el código, luego comentarla de nuevo
// register_activation_hook(__FILE__, 'maggiore_flush_rewrite_rules_on_activation');
