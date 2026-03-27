<?php
/**
 * SEO Output - inc/helpers/seo.php
 *
 * Sistema unificado para meta tags, Open Graph, Twitter Card y Schema JSON-LD.
 * Soporta:
 *  - Páginas singulares (post, page, CPTs)
 *  - Archives de CPTs con soporte multilenguaje (Polylang)
 *
 * @version 5.1 - Soporte archives CPT + Polylang
 */

if (!defined('ABSPATH')) exit;

/**
 * Output SEO, Open Graph, Twitter Card y Schema JSON-LD.
 * Llamada directamente desde header.php antes de wp_head().
 */
function mg_output_seo_meta() {

    $is_cpt_archive    = is_post_type_archive();
    $archive_post_type = $is_cpt_archive ? get_queried_object()->name : null;

    if ( ! $is_cpt_archive && ! is_singular() ) return;

    // =========================================================
    // RESOLUCIÓN DE DATOS
    // =========================================================

    if ( $is_cpt_archive && $archive_post_type ) {

        // ── Archive de CPT ────────────────────────────────────
        // Idioma actual (Polylang o 'es' como fallback)
        $lang = function_exists('pll_current_language')
            ? pll_current_language()
            : 'es';

        // Clave de opción incluye idioma: mg_archive_{cpt}_{lang}_{field}
        $prefix = 'mg_archive_' . $archive_post_type . '_' . $lang . '_';

        $title        = get_option( $prefix . 'seo_title', '' );
        $desc         = get_option( $prefix . 'seo_description', '' );
        $keywords_raw = get_option( $prefix . 'seo_keywords', [] );
        $noindex      = get_option( $prefix . 'noindex', '' );
        $schema_manual= get_option( $prefix . 'schema_json', '' );
        $custom_head  = get_option( $prefix . 'custom_head', '' );
        $og_raw       = get_option( $prefix . 'og_image', '' );
        $og_type      = 'website';

        // URL canónica del archive (usa slugs traducidos si existen)
        $canonical = get_post_type_archive_link( $archive_post_type );

        // Fallback de title
        if ( ! $title ) {
            $pto   = get_post_type_object( $archive_post_type );
            $label = $pto ? $pto->labels->name : $archive_post_type;
            $title = $label . ' | ' . get_bloginfo('name');
        }

        // Fallback de description
        if ( ! $desc ) {
            $pto  = get_post_type_object( $archive_post_type );
            $desc = ( $pto && $pto->description ) ? $pto->description : '';
        }

        // OG Image
        $og_image = '';
        if ( filter_var( $og_raw, FILTER_VALIDATE_URL ) ) {
            $og_image = $og_raw;
        }
        if ( ! $og_image ) {
            $default_og = get_option('mg_default_og_image', '');
            $og_image   = $default_og ?: get_template_directory_uri() . '/assets/img/default-og.webp';
        }

        // Schema JSON-LD
        $schema_output = '';
        if ( ! empty( $schema_manual ) ) {
            $schema_output = $schema_manual;
        } else {
            $pto         = get_post_type_object( $archive_post_type );
            $schema_auto = [
                '@context'    => 'https://schema.org',
                '@type'       => 'CollectionPage',
                '@id'         => $canonical . '#webpage',
                'name'        => $pto ? $pto->labels->name : '',
                'url'         => $canonical,
                'description' => $desc,
                'inLanguage'  => $lang === 'es' ? 'es-CL' : $lang,
                'publisher'   => [
                    '@type' => 'Organization',
                    'name'  => get_bloginfo('name'),
                    'url'   => home_url('/'),
                ],
            ];
            $schema_output = json_encode(
                $schema_auto,
                JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
            );
        }

    } else {

        // ── Singular ──────────────────────────────────────────
        global $post;

        $title = get_post_meta( $post->ID, 'mg_seo_title', true );
        if ( ! $title ) {
            $title = get_the_title( $post->ID ) . ' | ' . get_bloginfo('name');
        }

        $desc = get_post_meta( $post->ID, 'mg_seo_description', true );
        if ( ! $desc ) {
            $desc = wp_trim_words( strip_tags( get_post_field('post_content', $post->ID) ), 30 );
        }

        $keywords_raw = get_post_meta( $post->ID, 'mg_seo_keywords', true );
        $noindex      = get_post_meta( $post->ID, 'mg_seo_noindex', true );
        $schema_manual= get_post_meta( $post->ID, 'mg_schema_json', true );
        $custom_head  = get_post_meta( $post->ID, 'mg_custom_head_code', true );
        $canonical    = get_permalink( $post->ID );
        $og_type      = is_singular('post') ? 'article' : 'website';

        // OG Image (3 niveles de fallback)
        $og_image  = '';
        $custom_og = get_post_meta( $post->ID, 'mg_og_image', true );
        if ( is_numeric( $custom_og ) ) {
            $og_image = wp_get_attachment_url( $custom_og );
        } elseif ( filter_var( $custom_og, FILTER_VALIDATE_URL ) ) {
            $og_image = $custom_og;
        }
        if ( ! $og_image && has_post_thumbnail( $post->ID ) ) {
            $og_image = get_the_post_thumbnail_url( $post->ID, 'large' );
        }
        if ( ! $og_image ) {
            $default_og = get_option('mg_default_og_image', '');
            $og_image   = $default_og ?: get_template_directory_uri() . '/assets/img/default-og.webp';
        }

        // Schema JSON-LD
        $schema_output = '';
        if ( ! empty( $schema_manual ) ) {
            $schema_output = $schema_manual;
        } elseif ( is_front_page() ) {
            $schema_output = mg_schema_home_json();
        } elseif ( function_exists('mg_generate_auto_schema') ) {
            $schema_auto = mg_generate_auto_schema( $post->ID );
            if ( $schema_auto ) {
                if ( function_exists('mg_clean_schema_nulls') )   $schema_auto = mg_clean_schema_nulls( $schema_auto );
                if ( function_exists('mg_sanitize_schema_utf8') ) $schema_auto = mg_sanitize_schema_utf8( $schema_auto );
                $schema_output = json_encode(
                    $schema_auto,
                    JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT
                );
            }
        }
    }

    // =========================================================
    // KEYWORDS → string
    // =========================================================
    $keywords_str = '';
    if ( ! empty( $keywords_raw ) ) {
        if ( is_array( $keywords_raw ) ) {
            $keywords_str = implode(', ', array_filter( $keywords_raw ));
        } elseif ( is_string( $keywords_raw ) ) {
            $keywords_str = $keywords_raw;
        }
    }

    // =========================================================
    // TWITTER HANDLE
    // =========================================================
    $twitter_handle = '';
    $twitter_url    = get_option('maggiore_twitter_url', '');
    if ( ! empty( $twitter_url ) ) {
        $path     = parse_url( $twitter_url, PHP_URL_PATH );
        $username = trim( basename( rtrim( $path, '/' ) ) );
        if ( ! empty( $username ) ) {
            $twitter_handle = '@' . ltrim( $username, '@' );
        }
    }

    // =========================================================
    // OUTPUT
    // =========================================================

    echo "<meta name='description' content='" . esc_attr( $desc ) . "'>\n";

    if ( ! empty( $keywords_str ) ) {
        echo "<meta name='keywords' content='" . esc_attr( $keywords_str ) . "'>\n";
    }

    if ( $noindex ) {
        echo "<meta name='robots' content='noindex, nofollow'>\n";
    } else {
        echo "<meta name='robots' content='index, follow, max-image-preview:large'>\n";
    }

    echo "<link rel='canonical' href='" . esc_url( $canonical ) . "'>\n";

    // Open Graph
    echo "<meta property='og:title' content='"       . esc_attr( $title )    . "'>\n";
    echo "<meta property='og:description' content='" . esc_attr( $desc )     . "'>\n";
    echo "<meta property='og:url' content='"         . esc_url( $canonical ) . "'>\n";
    echo "<meta property='og:type' content='"        . esc_attr( $og_type )  . "'>\n";
    echo "<meta property='og:image' content='"       . esc_url( $og_image )  . "'>\n";

    // Twitter Card
    echo "<meta name='twitter:card' content='summary_large_image'>\n";
    if ( ! empty( $twitter_handle ) ) {
        echo "<meta name='twitter:site' content='" . esc_attr( $twitter_handle ) . "'>\n";
    }
    echo "<meta name='twitter:title' content='"       . esc_attr( $title )   . "'>\n";
    echo "<meta name='twitter:description' content='" . esc_attr( $desc )    . "'>\n";
    echo "<meta name='twitter:image' content='"       . esc_url( $og_image ) . "'>\n";

    // Schema JSON-LD
    if ( ! empty( $schema_output ) ) {
        echo "<script type='application/ld+json'>\n";
        echo $schema_output;
        echo "\n</script>\n";
    }

    // Código custom del head
    if ( ! empty( $custom_head ) ) {
        echo "\n<!-- Código Personalizado del Head -->\n";
        echo $custom_head;
        echo "\n<!-- /Código Personalizado del Head -->\n";
    }
}

/**
 * Schema del Home: MarketingAgency + WebSite + WebPage
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
            'foundingLocation' => [ '@type' => 'Place', 'name' => 'Santiago, Chile' ],
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
                'Marketing Digital', 'Inteligencia de Mercados',
                'Estrategia de Marca', 'Campañas de Medios Pagados',
                'Branding', 'Diseño Web',
            ],
        ],
        [
            '@context'   => 'https://schema.org',
            '@type'      => 'WebSite',
            '@id'        => $site_url . '#website',
            'url'        => $site_url,
            'name'       => $site_name,
            'publisher'  => ['@id' => $site_url . '#organization'],
            'inLanguage' => 'es-CL',
        ],
        [
            '@context'     => 'https://schema.org',
            '@type'        => 'WebPage',
            '@id'          => $site_url . '#webpage',
            'url'          => $site_url,
            'name'         => get_post_meta( get_option('page_on_front'), 'mg_seo_title', true )
                                ?: ( $site_name . ' | Agencia de Marketing Digital en Chile' ),
            'description'  => get_post_meta( get_option('page_on_front'), 'mg_seo_description', true ) ?: '',
            'isPartOf'     => ['@id' => $site_url . '#website'],
            'about'        => ['@id' => $site_url . '#organization'],
            'inLanguage'   => 'es-CL',
            'dateModified' => get_the_modified_date( 'c', get_option('page_on_front') ),
        ],
    ];

    return json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT );
}

/**
 * Controlar el <title> vía filtro para singulares Y archives de CPT.
 */
add_filter('pre_get_document_title', function () {

    // ── Archive de CPT ──
    if ( is_post_type_archive() ) {
        $post_type = get_queried_object()->name;
        $lang      = function_exists('pll_current_language') ? pll_current_language() : 'es';
        $title     = get_option( 'mg_archive_' . $post_type . '_' . $lang . '_seo_title', '' );

        if ( ! $title ) {
            $pto   = get_post_type_object( $post_type );
            $label = $pto ? $pto->labels->name : $post_type;
            $title = $label . ' | ' . get_bloginfo('name');
        }

        return $title;
    }

    // ── Singular ──
    if ( is_singular() ) {
        global $post;
        $title = get_post_meta( $post->ID, 'mg_seo_title', true );
        if ( ! $title ) {
            $title = get_the_title( $post->ID ) . ' | ' . get_bloginfo('name');
        }
        return $title;
    }

    return '';
}, 10);
