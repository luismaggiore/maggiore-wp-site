<?php
/**
 * SEO Output - inc/helpers/seo.php
 * 
 * Sistema unificado: esta función es la única fuente de verdad para
 * meta tags, Open Graph, Twitter Card y Schema JSON-LD.
 * 
 * IMPORTANTE: El add_action de línea 1213 en seo-enhanced.php debe
 * eliminarse para evitar duplicados. Este archivo lo reemplaza.
 * El add_action de línea 1131 en seo-enhanced.php también se elimina
 * porque el schema ahora se genera aquí.
 * 
 * @version 4.1
 */

if (!defined('ABSPATH')) exit;

/**
 * Output SEO, Open Graph, Twitter Card y Schema JSON-LD
 * Llamada directamente desde header.php antes de wp_head()
 */
function mg_output_seo_meta() {

    if (!is_singular()) return;

    global $post;

    // =============================================================
    // TITLE
    // =============================================================
    $title = get_post_meta($post->ID, 'mg_seo_title', true);
    if (!$title) {
        $title = get_the_title($post->ID) . ' | ' . get_bloginfo('name');
    }

    // =============================================================
    // DESCRIPTION
    // =============================================================
    $desc = get_post_meta($post->ID, 'mg_seo_description', true);
    if (!$desc) {
        $desc = wp_trim_words(strip_tags(get_post_field('post_content', $post->ID)), 30);
    }

    // =============================================================
    // KEYWORDS (soporta array y string)
    // =============================================================
    $keywords     = get_post_meta($post->ID, 'mg_seo_keywords', true);
    $keywords_str = '';

    if (!empty($keywords)) {
        if (is_array($keywords)) {
            $keywords_str = implode(', ', array_filter($keywords));
        } elseif (is_string($keywords)) {
            $keywords_str = $keywords;
        }
    }

    // =============================================================
    // OG IMAGE (3 niveles de fallback)
    // =============================================================
    $og_image = '';

    $custom_og = get_post_meta($post->ID, 'mg_og_image', true);
    if (is_numeric($custom_og)) {
        $og_image = wp_get_attachment_url($custom_og);
    } elseif (filter_var($custom_og, FILTER_VALIDATE_URL)) {
        $og_image = $custom_og;
    }

    if (!$og_image && has_post_thumbnail($post->ID)) {
        $og_image = get_the_post_thumbnail_url($post->ID, 'large');
    }

    if (!$og_image) {
        // Fallback: imagen OG por defecto subida a Media
        $default_og = get_option('mg_default_og_image', '');
        $og_image   = $default_og ?: get_template_directory_uri() . '/assets/img/default-og.webp';
    }

    // =============================================================
    // OG TYPE (website para home, article para posts, website para el resto)
    // =============================================================
    $og_type = 'website';
    if (is_singular('post')) {
        $og_type = 'article';
    }

    // =============================================================
    // ROBOTS
    // =============================================================
    $noindex = get_post_meta($post->ID, 'mg_seo_noindex', true);

    // =============================================================
    // SCHEMA JSON-LD
    // Prioridad: 1) Schema manual del metabox, 2) Schema automático
    // =============================================================
    $schema_manual = get_post_meta($post->ID, 'mg_schema_json', true);

    if (!empty($schema_manual)) {
        $schema_output = $schema_manual;
    } elseif (is_front_page()) {
        // Schema especial para el Home
        $schema_output = mg_schema_home_json();
    } elseif (function_exists('mg_generate_auto_schema')) {
        $schema_auto = mg_generate_auto_schema($post->ID);
        if ($schema_auto) {
            if (function_exists('mg_clean_schema_nulls')) {
                $schema_auto = mg_clean_schema_nulls($schema_auto);
            }
            if (function_exists('mg_sanitize_schema_utf8')) {
                $schema_auto = mg_sanitize_schema_utf8($schema_auto);
            }
            $schema_output = json_encode($schema_auto, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        }
    }

    // =============================================================
    // OUTPUT
    // =============================================================

    // El <title> lo maneja el filtro pre_get_document_title (ver al final del archivo)
    // Description
    echo "<meta name='description' content='" . esc_attr($desc) . "'>\n";

    // Keywords (solo si existen y no son vacías)
    if (!empty($keywords_str)) {
        echo "<meta name='keywords' content='" . esc_attr($keywords_str) . "'>\n";
    }

    // Robots
    if ($noindex) {
        echo "<meta name='robots' content='noindex, nofollow'>\n";
    } else {
        echo "<meta name='robots' content='index, follow, max-image-preview:large'>\n";
    }

    // Open Graph
    echo "<meta property='og:title' content='" . esc_attr($title) . "'>\n";
    echo "<meta property='og:description' content='" . esc_attr($desc) . "'>\n";
    echo "<meta property='og:url' content='" . esc_url(get_permalink($post->ID)) . "'>\n";
    echo "<meta property='og:type' content='" . esc_attr($og_type) . "'>\n";
    echo "<meta property='og:image' content='" . esc_url($og_image) . "'>\n";

    // Twitter Card
    echo "<meta name='twitter:card' content='summary_large_image'>\n";
    echo "<meta name='twitter:title' content='" . esc_attr($title) . "'>\n";
    echo "<meta name='twitter:description' content='" . esc_attr($desc) . "'>\n";
    echo "<meta name='twitter:image' content='" . esc_url($og_image) . "'>\n";

    // Schema JSON-LD (único bloque, sin duplicados)
    if (!empty($schema_output)) {
        echo "<script type='application/ld+json'>\n";
        echo $schema_output;
        echo "\n</script>\n";
    }
}

/**
 * Schema del Home: MarketingAgency + WebSite + WebPage en un array JSON-LD válido
 * Basado en los datos reales del sitio, editables desde wp-admin → Ajustes
 */
function mg_schema_home_json() {
    $site_name = get_bloginfo('name');
    $site_url  = home_url('/');

    $schema = [
        [
            '@context' => 'https://schema.org',
            '@type'    => 'MarketingAgency',
            '@id'      => $site_url . '#organization',
            'name'     => $site_name,
            'url'      => $site_url,
            'logo'     => [
                '@type' => 'ImageObject',
                'url'   => get_theme_file_uri('assets/img/logo-mm.svg'),
            ],
            'description'      => get_option('mg_schema_description', 'Agencia de marketing digital en Santiago de Chile con metodología propia basada en datos e inteligencia de mercados.'),
            'slogan'           => get_option('mg_schema_slogan', 'Pensamos en grande para que tu marca llegue a lo más alto'),
            'foundingLocation' => [
                '@type' => 'Place',
                'name'  => 'Santiago, Chile',
            ],
            'address' => [
                '@type'           => 'PostalAddress',
                'streetAddress'   => get_option('mg_direccion', 'Alcántara 1791'),
                'addressLocality' => 'Las Condes',
                'addressRegion'   => 'Región Metropolitana',
                'postalCode'      => '7550000',
                'addressCountry'  => 'CL',
            ],
            'contactPoint' => [
                '@type'             => 'ContactPoint',
                'telephone'         => get_option('mg_telefono', ''),
                'email'             => get_option('mg_email', ''),
                'contactType'       => 'customer service',
                'availableLanguage' => ['Spanish'],
            ],
            'areaServed' => ['CL', 'LATAM'],
            'sameAs'     => array_filter([
                get_option('mg_instagram_url', ''),
                get_option('mg_linkedin_url', ''),
                get_option('mg_facebook_url', ''),
                get_option('mg_youtube_url', ''),
                get_option('mg_tiktok_url', ''),
            ]),
            'knowsAbout' => [
                'Marketing Digital',
                'Inteligencia de Mercados',
                'Estrategia de Marca',
                'Campañas de Medios Pagados',
                'Branding',
                'Diseño Web',
            ],
        ],
        [
            '@context'  => 'https://schema.org',
            '@type'     => 'WebSite',
            '@id'       => $site_url . '#website',
            'url'       => $site_url,
            'name'      => $site_name,
            'publisher' => ['@id' => $site_url . '#organization'],
            'inLanguage' => 'es-CL',
        ],
        [
            '@context'    => 'https://schema.org',
            '@type'       => 'WebPage',
            '@id'         => $site_url . '#webpage',
            'url'         => $site_url,
            'name'        => get_post_meta(get_option('page_on_front'), 'mg_seo_title', true) ?: ($site_name . ' | Agencia de Marketing Digital en Chile'),
            'description' => get_post_meta(get_option('page_on_front'), 'mg_seo_description', true) ?: '',
            'isPartOf'    => ['@id' => $site_url . '#website'],
            'about'       => ['@id' => $site_url . '#organization'],
            'inLanguage'  => 'es-CL',
            'dateModified' => get_the_modified_date('c', get_option('page_on_front')),
        ],
    ];

    return json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
}

/**
 * Controlar el <title> desde aquí en lugar de imprimirlo directo,
 * para evitar el duplicado que genera WordPress via wp_head().
 * 
 * Prioridad: 1) mg_seo_title del metabox, 2) título del post + nombre del sitio
 */
add_filter('pre_get_document_title', function () {
    if (!is_singular()) return '';

    global $post;

    $title = get_post_meta($post->ID, 'mg_seo_title', true);
    if (!$title) {
        $title = get_the_title($post->ID) . ' | ' . get_bloginfo('name');
    }

    return $title;
}, 10);
