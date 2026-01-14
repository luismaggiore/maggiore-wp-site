<?php
if (!defined('ABSPATH')) exit;

/**
 * Imprime etiquetas hreflang en <head>
 */
add_action('wp_head', 'mg_output_hreflang_tags', 5);

function mg_output_hreflang_tags() {
    if (!function_exists('pll_get_post') || !function_exists('pll_languages_list')) return;
    if (!is_singular()) return;

    global $post;
    $current_id = $post->ID;

    $languages = pll_languages_list();

    foreach ($languages as $lang) {
        $translated_id = pll_get_post($current_id, $lang);
        if (!$translated_id) continue;

        $url = get_permalink($translated_id);
        if (!$url) continue;

        echo '<link rel="alternate" hreflang="' . esc_attr($lang) . '" href="' . esc_url($url) . '" />' . "\n";
    }
}
