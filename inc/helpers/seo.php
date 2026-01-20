<?php
/**
 * ACTUALIZACIÓN PARA inc/helpers/seo.php
 * 
 * Esta es la versión actualizada de la función mg_output_seo_meta()
 * que soporta el nuevo sistema de keywords como array
 * 
 * CÓMO USAR:
 * 1. Abre tu archivo inc/helpers/seo.php
 * 2. Busca la función mg_output_seo_meta()
 * 3. Reemplázala completamente con esta versión
 */

if (!defined('ABSPATH')) exit;

/**
 * Output SEO, Open Graph, Twitter y Schema - VERSIÓN ACTUALIZADA
 */
function mg_output_seo_meta() {

    if (!is_singular()) return;

    global $post;

    // =========================
    // TITLE & DESCRIPTION
    // =========================
    $title = get_post_meta($post->ID, 'mg_seo_title', true);
    if (!$title) {
        $title = get_the_title($post->ID);
    }

    $desc = get_post_meta($post->ID, 'mg_seo_description', true);
    if (!$desc) {
        $desc = wp_trim_words(strip_tags(get_post_field('post_content', $post->ID)), 30);
    }

    // =========================
    // KEYWORDS (NUEVO - SOPORTE ARRAY)
    // =========================
    $keywords = get_post_meta($post->ID, 'mg_seo_keywords', true);
    $keywords_string = '';
    
    if (!empty($keywords)) {
        if (is_array($keywords)) {
            // Nuevo formato: array de keywords
            $keywords_string = implode(', ', $keywords);
        } elseif (is_string($keywords)) {
            // Formato antiguo: string separado por comas (compatibilidad)
            $keywords_string = $keywords;
        }
    }

    // =========================
    // OG IMAGE (3 niveles)
    // =========================
    $og_image = '';

    // 1️⃣ OG Image custom
    $custom_og = get_post_meta($post->ID, 'mg_og_image', true);
    if (is_numeric($custom_og)) {
        $og_image = wp_get_attachment_url($custom_og);
    } elseif (filter_var($custom_og, FILTER_VALIDATE_URL)) {
        $og_image = $custom_og;
    }

    // 2️⃣ Fallback a featured image
    if (!$og_image && has_post_thumbnail($post->ID)) {
        $og_image = get_the_post_thumbnail_url($post->ID, 'large');
    }

    // 3️⃣ Fallback global
    if (!$og_image) {
        $og_image = get_template_directory_uri() . '/assets/img/default-og.jpg';
    }

    // =========================
    // ROBOTS
    // =========================
    $noindex = get_post_meta($post->ID, 'mg_seo_noindex', true);

    // =========================
    // SCHEMA
    // =========================
    $schema = get_post_meta($post->ID, 'mg_schema_json', true);

    // =========================
    // OUTPUT
    // =========================

    echo "<title>" . esc_html($title) . "</title>\n";
    echo "<meta name='description' content='" . esc_attr($desc) . "'>\n";

    // Keywords (NUEVO)
    if (!empty($keywords_string)) {
        echo "<meta name='keywords' content='" . esc_attr($keywords_string) . "'>\n";
    }

    // Robots
    if ($noindex) {
        echo "<meta name='robots' content='noindex, nofollow'>\n";
    }

    // Open Graph
    echo "<meta property='og:title' content='" . esc_attr($title) . "'>\n";
    echo "<meta property='og:description' content='" . esc_attr($desc) . "'>\n";
    echo "<meta property='og:url' content='" . esc_url(get_permalink($post->ID)) . "'>\n";
    echo "<meta property='og:type' content='article'>\n";
    echo "<meta property='og:image' content='" . esc_url($og_image) . "'>\n";

    // Twitter
    echo "<meta name='twitter:card' content='summary_large_image'>\n";
    echo "<meta name='twitter:title' content='" . esc_attr($title) . "'>\n";
    echo "<meta name='twitter:description' content='" . esc_attr($desc) . "'>\n";
    echo "<meta name='twitter:image' content='" . esc_url($og_image) . "'>\n";

    // Schema JSON-LD (manual)
    if (!empty($schema)) {
        echo "<script type='application/ld+json'>\n";
        echo wp_kses_post($schema);
        echo "\n</script>\n";
    }
}

/**
 * NOTAS DE IMPLEMENTACIÓN:
 * 
 * 1. Esta función ahora soporta keywords en formato array (nuevo sistema de tags)
 * 2. Mantiene compatibilidad con el formato antiguo (string separado por comas)
 * 3. El output de OG Image tiene mejor fallback
 * 4. Mejor validación de URLs
 * 
 * CAMBIOS ESPECÍFICOS:
 * - Líneas 30-42: Nueva lógica para keywords (array o string)
 * - Línea 68: Output condicional de keywords
 * - Líneas 50-65: Mejor lógica para OG image
 */
