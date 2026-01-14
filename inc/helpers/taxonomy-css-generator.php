<?php
/**
 * Generador Automático de Clases CSS para Taxonomías con Colores
 * 
 * Genera automáticamente clases CSS basadas en los slugs de términos
 * y sus colores personalizados. Se inyecta en el <head> del sitio.
 */

if (!defined('ABSPATH')) exit;

/**
 * Genera CSS dinámico para todos los términos con colores
 */
function mg_generate_taxonomy_color_css() {
    // Configuración: taxonomías que queremos procesar
    $taxonomies = ['mg_categoria', 'mg_industria', 'mg_equipos'];
    
    // Array para almacenar el CSS
    $css = '';
    
    foreach ($taxonomies as $taxonomy) {
        $terms = get_terms([
            'taxonomy' => $taxonomy,
            'hide_empty' => false,
        ]);
        
        if (empty($terms) || is_wp_error($terms)) {
            continue;
        }
        
        foreach ($terms as $term) {
            $color = get_term_meta($term->term_id, 'term_color', true);
            
            // Si no hay color personalizado, saltar
            if (empty($color)) {
                continue;
            }
            
            $slug = $term->slug;
            
            // Generar variaciones de color para hover/active
            $color_hover = mg_adjust_brightness($color, -10); // 10% más oscuro
            $color_light = mg_adjust_brightness($color, 90); // 90% más claro (para fondos)
            $color_dark = mg_adjust_brightness($color, -30); // 30% más oscuro
            
            // ===== BOTONES =====
            $css .= "
/* Botón: {$term->name} */
.btn-{$slug},
.button-{$slug} {
    border-color: {$color} !important;
    color: #ffffff !important;
}

.btn-{$slug}:hover,
.button-{$slug}:hover {
    background-color: {$color_dark} !important;
    border-color: {$color} !important;
    color: #ffffff !important;
}

.btn-{$slug}.btn-solid,
.button-{$slug}.solid {
    background-color: {$color} !important;
    border-color: {$color} !important;
    color: #ffffff !important;
}

.btn-{$slug}.btn-solid:hover,
.button-{$slug}.solid:hover {
    background-color: {$color_hover} !important;
    border-color: {$color_hover} !important;
}

/* ===== BADGES / TAGS ===== */
.badge-{$slug},
.tag-{$slug} {
    background-color: {$color_light} !important;
    color: {$color_dark} !important;
    border-color: {$color} !important;
}

.badge-{$slug}:hover,
.tag-{$slug}:hover {
    background-color: {$color} !important;
    color: #ffffff !important;
}

/* ===== BORDES / BORDERS ===== */
.border-{$slug} {
    border-color: {$color} !important;
}

.border-left-{$slug} {
    border-left-color: {$color} !important;
}

.border-top-{$slug} {
    border-top-color: {$color} !important;
}

/* ===== FONDOS / BACKGROUNDS ===== */
.bg-{$slug} {
    background-color: {$color} !important;
}

.bg-{$slug}-light {
    background-color: {$color_light} !important;
}

.bg-{$slug}-dark {
    background-color: {$color_dark} !important;
}

/* ===== TEXTOS / TEXT ===== */
.text-{$slug} {
    color: {$color} !important;
}

.text-{$slug}-hover:hover {
    color: {$color} !important;
}

/* ===== LINKS ===== */
a.link-{$slug} {
    color: {$color} !important;
}

a.link-{$slug}:hover {
    color: {$color_hover} !important;
}

/* ===== CARDS ===== */
.card-{$slug} {
    border-left: 4px solid {$color} !important;
}

.card-{$slug}:hover {
    border-left-color: {$color_hover} !important;
    box-shadow: 0 4px 12px rgba(" . mg_hex_to_rgb($color) . ", 0.2) !important;
}

/* ===== UNDERLINE / DECORACIONES ===== */
.underline-{$slug} {
    text-decoration-color: {$color} !important;
}

.underline-{$slug}::after {
    background-color: {$color} !important;
}
";
        }
    }
    
    return $css;
}

/**
 * Ajustar brillo de un color hex
 * 
 * @param string $hex Color hexadecimal (#RRGGBB)
 * @param int $percent Porcentaje de ajuste (-100 a 100)
 * @return string Color ajustado
 */
function mg_adjust_brightness($hex, $percent) {
    // Remover el # si existe
    $hex = str_replace('#', '', $hex);
    
    // Convertir a RGB
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    // Ajustar cada componente
    $r = max(0, min(255, $r + ($r * $percent / 100)));
    $g = max(0, min(255, $g + ($g * $percent / 100)));
    $b = max(0, min(255, $b + ($b * $percent / 100)));
    
    // Convertir de vuelta a hex
    return '#' . str_pad(dechex($r), 2, '0', STR_PAD_LEFT) 
                . str_pad(dechex($g), 2, '0', STR_PAD_LEFT) 
                . str_pad(dechex($b), 2, '0', STR_PAD_LEFT);
}

/**
 * Convertir hex a RGB (para box-shadow con opacidad)
 * 
 * @param string $hex Color hexadecimal
 * @return string RGB sin paréntesis (ej: "255, 0, 0")
 */
function mg_hex_to_rgb($hex) {
    $hex = str_replace('#', '', $hex);
    
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    
    return "$r, $g, $b";
}

/**
 * Inyectar CSS en el <head>
 */
function mg_inject_taxonomy_color_css() {
    // Obtener CSS cacheado o generarlo
    $css = get_transient('mg_taxonomy_color_css');
    
    if (false === $css) {
        $css = mg_generate_taxonomy_color_css();
        // Cachear por 1 hora
        set_transient('mg_taxonomy_color_css', $css, HOUR_IN_SECONDS);
    }
    
    if (!empty($css)) {
        echo "\n<style id='mg-taxonomy-colors'>\n";
        echo "/* Clases CSS Generadas Automáticamente desde Taxonomías */\n";
        echo $css;
        echo "</style>\n";
    }
}
add_action('wp_head', 'mg_inject_taxonomy_color_css');

/** 
 * Limpiar caché cuando se actualiza un término
 */
function mg_clear_taxonomy_color_cache($term_id) {
    delete_transient('mg_taxonomy_color_css');
}

$taxonomies_with_color = ['mg_categoria', 'mg_industria', 'mg_equipos'];
foreach ($taxonomies_with_color as $taxonomy) {
    add_action("edited_{$taxonomy}", 'mg_clear_taxonomy_color_cache');
    add_action("create_{$taxonomy}", 'mg_clear_taxonomy_color_cache');
    add_action("delete_{$taxonomy}", 'mg_clear_taxonomy_color_cache');
}

/**
 * Función helper para obtener la clase de un término
 * 
 * @param int|object $term ID del término o objeto término
 * @param string $type Tipo de clase (btn, badge, bg, text, border, card)
 * @param string $variant Variante (solid, light, dark, etc.)
 * @return string Clase CSS
 */
function mg_get_term_class($term, $type = 'btn', $variant = '') {
    if (is_numeric($term)) {
        $term = get_term($term);
    }
    
    if (!$term || is_wp_error($term)) {
        return '';
    }
    
    $slug = $term->slug;
    $class = "{$type}-{$slug}";
    
    if (!empty($variant)) {
        $class .= " {$variant}";
    }
    
    return esc_attr($class);
}

/**
 * Función helper para obtener clases múltiples de un término
 * 
 * @param int|object $term ID del término o objeto término
 * @param array $types Array de tipos (ej: ['btn', 'badge'])
 * @return string Clases CSS separadas por espacio
 */
function mg_get_term_classes($term, $types = ['btn']) {
    $classes = [];
    
    foreach ($types as $type) {
        $classes[] = mg_get_term_class($term, $type);
    }
    
    return implode(' ', array_filter($classes));
}
