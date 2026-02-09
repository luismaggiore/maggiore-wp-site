<?php
/**
 * Funciones adicionales para el Footer
 * 
 * @package Maggiore
 * @version 2.0
 * 
 * NOTA: El registro del menú footer se movió a functions.php
 * para centralizar todos los menús y asegurar compatibilidad con Polylang
 */

if (!defined('ABSPATH')) exit;

/* ========================================
 * REGISTRAR MENÚ DEL FOOTER
 * ======================================== */
/**
 * ❌ ESTA FUNCIÓN SE ELIMINÓ
 * El menú footer ahora se registra en functions.php junto con el menú principal
 * Ver: maggiore_setup() en functions.php
 */

/* ========================================
 * ENQUEUE FOOTER CSS
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
        }
        
        // Título actual
        echo '<li class="breadcrumb-item active" aria-current="page">';
        echo esc_html(get_the_title());
        echo '</li>';
    } elseif (is_archive()) {
        echo '<li class="breadcrumb-item active" aria-current="page">';
        
        if (is_category()) {
            echo esc_html(single_cat_title('', false));
        } elseif (is_tag()) {
            echo esc_html(single_tag_title('', false));
        } elseif (is_author()) {
            echo esc_html(get_the_author());
        } elseif (is_post_type_archive()) {
            echo esc_html(post_type_archive_title('', false));
        } elseif (is_tax()) {
            echo esc_html(single_term_title('', false));
        } else {
            echo __('Archivo', 'maggiore');
        }
        
        echo '</li>';
    } elseif (is_search()) {
        echo '<li class="breadcrumb-item active" aria-current="page">';
        echo __('Resultados de búsqueda', 'maggiore');
        echo '</li>';
    } elseif (is_404()) {
        echo '<li class="breadcrumb-item active" aria-current="page">';
        echo __('Página no encontrada', 'maggiore');
        echo '</li>';
    }
    
    echo '</ol>';
    echo '</div>';
    echo '</nav>';
}

/* ========================================
 * WIDGET AREAS FOOTER (OPCIONAL)
 * Si deseas usar widgets en el footer
 * ======================================== */
function maggiore_footer_widgets() {
    register_sidebar([
        'name'          => __('Footer Widget Area 1', 'maggiore'),
        'id'            => 'footer-1',
        'description'   => __('Primera columna del footer', 'maggiore'),
        'before_widget' => '<div class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="footer-widget-title">',
        'after_title'   => '</h5>',
    ]);
    
    register_sidebar([
        'name'          => __('Footer Widget Area 2', 'maggiore'),
        'id'            => 'footer-2',
        'description'   => __('Segunda columna del footer', 'maggiore'),
        'before_widget' => '<div class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="footer-widget-title">',
        'after_title'   => '</h5>',
    ]);
    
    register_sidebar([
        'name'          => __('Footer Widget Area 3', 'maggiore'),
        'id'            => 'footer-3',
        'description'   => __('Tercera columna del footer', 'maggiore'),
        'before_widget' => '<div class="footer-widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5 class="footer-widget-title">',
        'after_title'   => '</h5>',
    ]);
}
// Descomentar si quieres usar widgets en el footer
// add_action('widgets_init', 'maggiore_footer_widgets');

/* ========================================
 * HELPER: Obtener enlaces de redes sociales
 * ======================================== */
function maggiore_get_social_links() {
    return [
        'facebook'  => get_option('mg_facebook_url', ''),
        'instagram' => get_option('mg_instagram_url', ''),
        'linkedin'  => get_option('mg_linkedin_url', ''),
        'twitter'   => get_option('mg_twitter_url', ''),
        'youtube'   => get_option('mg_youtube_url', ''),
    ];
}

/* ========================================
 * HELPER: Obtener información de contacto
 * ======================================== */
function maggiore_get_contact_info() {
    return [
        'phone'   => get_option('mg_phone', ''),
        'email'   => get_option('mg_email', ''),
        'address' => get_option('mg_address', ''),
    ];
}