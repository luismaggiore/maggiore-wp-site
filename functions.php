<?php
/**
 * Maggiore Theme Functions
 * Versión optimizada con sistema de animaciones mejorado
 * 
 * @package Maggiore
 * @version 2.0.0
 */

if (!defined('ABSPATH')) exit;

/* -------------------------------------------------------
 * Theme setup
 * ----------------------------------------------------- */
function maggiore_setup() {
    load_theme_textdomain('maggiore', get_template_directory() . '/languages');

    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');

    register_nav_menus([
        'primary' => __('Menú Principal', 'maggiore')
    ]);
}
add_action('after_setup_theme', 'maggiore_setup');

/* -------------------------------------------------------
 * Enqueue scripts & styles
 * Sistema optimizado con orden correcto de dependencias
 * ----------------------------------------------------- */
function maggiore_scripts() {
  // Versión para cache busting (incrementar al hacer cambios)
  $theme_version = '2.0.0';
  
  // =========================================================================
  // CSS STYLES
  // =========================================================================
  
  wp_enqueue_style('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css');
  wp_enqueue_style('maggiore-style', get_stylesheet_uri()); // style.css
  wp_enqueue_style('maggiore-main', get_template_directory_uri() . '/assets/css/main.css', [], $theme_version);
  wp_enqueue_style('comments-styles', get_template_directory_uri() . '/assets/css/comments-styles.css', [], $theme_version);
  wp_enqueue_style('blog-styles', get_template_directory_uri() . '/assets/css/blog-styles.css', [], $theme_version);
  wp_enqueue_style('bs-override', get_template_directory_uri() . '/assets/css/override.css', [], $theme_version);
  
  // International telephone input CSS
  wp_enqueue_style('intl-tel-input-css', 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/css/intlTelInput.css');

  // =========================================================================
  // EXTERNAL SCRIPTS
  // =========================================================================
  
  // Bootstrap
  wp_enqueue_script('bootstrap', 'https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js', [], null, true);
  
  // GSAP + Plugins
  wp_enqueue_script('gsap', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/gsap.min.js', [], null, true);
  wp_enqueue_script('gsap-scroll', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollTrigger.min.js', ['gsap'], null, true);
  wp_enqueue_script('gsap-smoother', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollSmoother.min.js', ['gsap-scroll'], null, true);
  wp_enqueue_script('gsap-scrollto', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/ScrollToPlugin.min.js', ['gsap'], null, true);
  wp_enqueue_script('gsap-splittext', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/SplitText.min.js', ['gsap'], null, true);
  wp_enqueue_script('gsap-textplugin', 'https://cdn.jsdelivr.net/npm/gsap@3.13.0/dist/TextPlugin.min.js', ['gsap'], null, true);

  // International telephone input
  wp_enqueue_script('intl-tel-input', 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/intlTelInput.min.js', [], null, true);
  wp_enqueue_script('intl-tel-utils', 'https://cdn.jsdelivr.net/npm/intl-tel-input@23.8.0/build/js/utils.js', ['intl-tel-input'], null, true);

  // =========================================================================
  // SISTEMA DE ANIMACIONES - ORDEN CRÍTICO ⚠️
  // Cargar en este orden específico para evitar errores
  // =========================================================================
  function permitir_webp_upload($mimes) {
  $mimes['webp'] = 'image/webp';
  return $mimes;
}
add_filter('mime_types', 'permitir_webp_upload');


  // 1️⃣ CONFIGURACIÓN (primero, sin dependencias)
  wp_enqueue_script(
    'visual-config',
    get_template_directory_uri() . '/assets/js/visual-config.js',
    [], // Sin dependencias
    $theme_version,
    true // En footer
  );

  // 2️⃣ CONTROLLER (depende de config)
  wp_enqueue_script(
    'animation-controller',
    get_template_directory_uri() . '/assets/js/animation-controller.js',
    ['visual-config'], // ✅ Depende de config
    $theme_version,
    true
  );

  // 3️⃣ EFECTOS VISUALES (dependen del controller)
  
  // Aurora (ES6 Module - se marca como module más abajo)
  wp_enqueue_script(
    'aurora',
    get_template_directory_uri() . '/assets/js/aurora.js',
    ['animation-controller'], // ✅ Depende del controller
    $theme_version,
    true
  );

  // Constelación
  wp_enqueue_script(
    'constelacion',
    get_template_directory_uri() . '/assets/js/constelacion.js',
    ['animation-controller'], // ✅ Depende del controller
    $theme_version,
    true
  );

  // 4️⃣ MAIN (depende de GSAP + efectos visuales)
  wp_enqueue_script(
    'maggiore-main',
    get_template_directory_uri() . '/assets/js/main.js',
    ['gsap', 'gsap-scroll', 'gsap-splittext', 'constelacion'], // ✅ Todas las dependencias
    $theme_version,
    true
  );

  // =========================================================================
  // SCRIPTS ADICIONALES
  // =========================================================================
  
  // Admin media (para portafolio)
  wp_enqueue_script(
    'mg-admin-media-public',
    get_template_directory_uri() . '/assets/js/admin-media.js',
    ['jquery'],
    $theme_version,
    true
  );

  // Teléfono
  wp_enqueue_script(
    'telefono',
    get_template_directory_uri() . '/assets/js/telefono.js',
    ['intl-tel-input'],
    $theme_version,
    true
  );

  // =========================================================================
  // LOCALIZAR DATOS PHP → JS
  // =========================================================================
  
  wp_localize_script('maggiore-main', 'maggioreData', [
    'ajaxUrl' => admin_url('admin-ajax.php'),
    'nonce' => wp_create_nonce('maggiore_nonce'),
    'homeUrl' => home_url('/'),
    'themeUrl' => get_template_directory_uri(),
    'currentLang' => function_exists('pll_current_language') ? pll_current_language() : 'es',
  ]);
}
add_action('wp_enqueue_scripts', 'maggiore_scripts');

/* -------------------------------------------------------
 * Agregar type="module" a aurora.js
 * Necesario para que funcione el import de Three.js
 * ----------------------------------------------------- */
function maggiore_add_module_type($tag, $handle, $src) {
    // Aurora necesita ser ES6 module
    if ($handle === 'aurora') {
        return '<script type="module" src="' . esc_url($src) . '"></script>';
    }
    return $tag;
}
add_filter('script_loader_tag', 'maggiore_add_module_type', 10, 3);

/* -------------------------------------------------------
 * Defer non-critical scripts para mejor performance
 * ----------------------------------------------------- */
function maggiore_defer_scripts($tag, $handle, $src) {
    // Scripts que pueden ser defer (no críticos)
    $defer_scripts = [
        'bootstrap',
        'intl-tel-input',
        'intl-tel-utils',
        'mg-admin-media-public'
    ];
    
    if (in_array($handle, $defer_scripts)) {
        return str_replace(' src', ' defer src', $tag);
    }
    
    return $tag;
}
add_filter('script_loader_tag', 'maggiore_defer_scripts', 10, 3);

/* -------------------------------------------------------
 * Widgets
 * ----------------------------------------------------- */
function maggiore_widgets_init() {
    register_sidebar([
        'name' => __('Sidebar Principal', 'maggiore'),
        'id' => 'sidebar-1',
        'before_widget' => '<div class="widget %2$s">',
        'after_widget'  => '</div>',
        'before_title'  => '<h5>',
        'after_title'   => '</h5>',
    ]);

    register_sidebar([
        'name' => __('Footer', 'maggiore'),
        'id' => 'footer-sidebar',
    ]);
}
add_action('widgets_init', 'maggiore_widgets_init');

/* -------------------------------------------------------
 * Customizer (solo textos)
 * ----------------------------------------------------- */
function maggiore_customize_register($wp_customize) {
    $wp_customize->add_section('maggiore_texts', [
        'title' => __('Textos del tema', 'maggiore'),
        'priority' => 30,
    ]);

    $wp_customize->add_setting('footer_text', [
        'default' => __('© 2025 Maggiore Agencia', 'maggiore'),
        'sanitize_callback' => 'sanitize_text_field',
    ]);

    $wp_customize->add_control('footer_text', [
        'label' => __('Texto del footer', 'maggiore'),
        'section' => 'maggiore_texts',
        'type' => 'text',
    ]);
}
add_action('customize_register', 'maggiore_customize_register');

/* -------------------------------------------------------
 * Performance Optimizations
 * ----------------------------------------------------- */

// Remover emoji scripts (mejora performance)
remove_action('wp_head', 'print_emoji_detection_script', 7);
remove_action('wp_print_styles', 'print_emoji_styles');

// Deshabilitar Gutenberg CSS en frontend si no lo usas
function maggiore_remove_gutenberg_css() {
    wp_dequeue_style('wp-block-library');
    wp_dequeue_style('wp-block-library-theme');
    wp_dequeue_style('wc-blocks-style'); // WooCommerce
}
add_action('wp_enqueue_scripts', 'maggiore_remove_gutenberg_css', 100);

/* -------------------------------------------------------
 * Select2 para Admin
 * ----------------------------------------------------- */
function mg_enqueue_select2_admin() {
    wp_enqueue_style('select2-css', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css');
    wp_enqueue_script('select2-js', 'https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js', ['jquery'], null, true);

    wp_add_inline_script('select2-js', "
        jQuery(document).ready(function($) {
            $('.select2').select2({
                width: '100%'
            });
        });
    ");
}
add_action('admin_enqueue_scripts', 'mg_enqueue_select2_admin');

/* -------------------------------------------------------
 * Admin Media Scripts
 * ----------------------------------------------------- */
add_action('admin_enqueue_scripts', function () {
    wp_enqueue_media();
    wp_enqueue_script(
        'mg-admin-media',
        get_template_directory_uri() . '/assets/js/admin-media.js',
        ['jquery'],
        '2.0.0',
        true
    );
});

/* -------------------------------------------------------
 * Navwalker Bootstrap
 * ----------------------------------------------------- */
require_once get_template_directory() . '/inc/navwalker.php';

/* -------------------------------------------------------
 * Custom Post Types
 * ----------------------------------------------------- */
require_once get_template_directory() . '/inc/post-types/cpt-servicio.php';
require_once get_template_directory() . '/inc/post-types/cpt-cliente.php';
require_once get_template_directory() . '/inc/post-types/cpt-caso-exito.php';
require_once get_template_directory() . '/inc/post-types/cpt-portafolio.php';
require_once get_template_directory() . '/inc/post-types/cpt-equipo.php';
require_once get_template_directory() . '/inc/post-types/cpt-area.php';

/* -------------------------------------------------------
 * Metaboxes
 * ----------------------------------------------------- */
foreach (glob(get_template_directory() . '/inc/metaboxes/*.php') as $file) {
    require_once $file;
}

/* -------------------------------------------------------
 * Taxonomías
 * ----------------------------------------------------- */
require_once get_template_directory() . '/inc/taxonomies/industria.php';
require_once get_template_directory() . '/inc/taxonomies/equipos.php';
require_once get_template_directory() . '/inc/taxonomies/categoria-servicio.php';
require_once get_template_directory() . '/inc/taxonomies/categoria-portafolio.php';

/* -------------------------------------------------------
 * Helpers & Systems
 * ----------------------------------------------------- */
require_once get_template_directory() . '/inc/helpers/cpt-relations.php';
require_once get_template_directory() . '/inc/helpers/auto-relations.php';
require_once get_template_directory() . '/inc/helpers/multilang.php';
require_once get_template_directory() . '/inc/helpers/translation.php';
require_once get_template_directory() . '/inc/helpers/seo.php';
require_once get_template_directory() . '/inc/helpers/hreflang.php';
require_once get_template_directory() . '/inc/helpers/hierarchy-helpers.php';
require_once get_template_directory() . '/inc/helpers/portafolio-video-system.php';
require_once get_template_directory() . '/inc/helpers/taxonomy-color-fields.php';
require_once get_template_directory() . '/inc/helpers/taxonomy-css-generator.php';


/* -------------------------------------------------------
 * SEO Enhanced
 * ----------------------------------------------------- */
require_once get_template_directory() . '/inc/seo-enhanced.php';
require_once get_template_directory() . '/inc/seo-metabox-enhanced.php';
