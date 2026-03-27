<?php
if (!defined('ABSPATH')) exit;

/**
 * Hreflang tags - inc/helpers/hreflang.php
 *
 * Soporta:
 *  - Páginas singulares (comportamiento original)
 *  - Archives de CPTs con slugs traducidos (via mg_get_translated_cpt_slugs)
 *
 * @version 2.0 - Soporte archives CPT
 */

add_action('wp_head', 'mg_output_hreflang_tags', 5);

function mg_output_hreflang_tags() {

    if ( ! function_exists('pll_languages_list') ) return;

    $languages = pll_languages_list( [ 'fields' => 'slug' ] );
    if ( empty( $languages ) ) return;

    // ── Archive de CPT ────────────────────────────────────────
    if ( is_post_type_archive() ) {
        $post_type = get_queried_object()->name;

        // Necesitamos los slugs traducidos de este CPT
        if ( ! function_exists('mg_get_translated_cpt_slugs') ) return;
        $cpt_slugs = mg_get_translated_cpt_slugs();

        if ( ! isset( $cpt_slugs[ $post_type ] ) ) return;

        $slugs    = $cpt_slugs[ $post_type ];
        $base_url = home_url('/');

        foreach ( $languages as $lang ) {
            $slug = $slugs[ $lang ] ?? $slugs['es'] ?? null;
            if ( ! $slug ) continue;

            // El idioma principal (es) no lleva prefijo de idioma en la URL
            if ( $lang === 'es' ) {
                $url = trailingslashit( $base_url . $slug );
            } else {
                $url = trailingslashit( $base_url . $lang . '/' . $slug );
            }

            // Mapear código de idioma a locale hreflang (es → es-CL, en → en, pt → pt-BR)
            $hreflang = mg_hreflang_code( $lang );

            echo '<link rel="alternate" hreflang="' . esc_attr( $hreflang ) . '" href="' . esc_url( $url ) . '" />' . "\n";
        }

        // x-default apunta al idioma principal (es)
        $slug_default = $slugs['es'] ?? null;
        if ( $slug_default ) {
            $url_default = trailingslashit( $base_url . $slug_default );
            echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $url_default ) . '" />' . "\n";
        }

        return;
    }

    // ── Singular ──────────────────────────────────────────────
    if ( ! is_singular() ) return;
    if ( ! function_exists('pll_get_post') ) return;

    global $post;
    $current_id = $post->ID;

    $has_any = false;

    foreach ( $languages as $lang ) {
        $translated_id = pll_get_post( $current_id, $lang );
        if ( ! $translated_id ) continue;

        $url = get_permalink( $translated_id );
        if ( ! $url ) continue;

        $hreflang = mg_hreflang_code( $lang );
        echo '<link rel="alternate" hreflang="' . esc_attr( $hreflang ) . '" href="' . esc_url( $url ) . '" />' . "\n";
        $has_any = true;
    }

    // x-default para singulares: apunta a la versión ES si existe
    if ( $has_any && function_exists('pll_get_post') ) {
        $es_id  = pll_get_post( $current_id, 'es' );
        $es_url = $es_id ? get_permalink( $es_id ) : get_permalink( $current_id );
        if ( $es_url ) {
            echo '<link rel="alternate" hreflang="x-default" href="' . esc_url( $es_url ) . '" />' . "\n";
        }
    }
}

/**
 * Convierte código de idioma de Polylang al formato hreflang correcto.
 * Polylang usa 'es', 'en', 'pt' — hreflang necesita el locale completo
 * cuando aplica (es-CL, pt-BR, etc.).
 *
 * Ajusta el mapeo a los locales reales de tu sitio.
 */
function mg_hreflang_code( $lang_slug ) {
    $map = [
        'es' => 'es-CL',
        'en' => 'en',
        'pt' => 'pt-BR',
    ];
    return $map[ $lang_slug ] ?? $lang_slug;
}
