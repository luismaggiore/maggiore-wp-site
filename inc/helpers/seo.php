<?php
if (!defined('ABSPATH')) exit;

/**
 * Output SEO, Open Graph, Twitter y Schema
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
    // OG IMAGE (3 niveles)
    // =========================

    $og_image = '';

    // 1️⃣ OG Image custom (guardada como ID)
    $custom_og = get_post_meta($post->ID, 'mg_og_image', true);
    if (is_numeric($custom_og)) {
        $og_image = wp_get_attachment_url($custom_og);
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
