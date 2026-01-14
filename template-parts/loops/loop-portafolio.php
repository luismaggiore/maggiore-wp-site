<?php
/**
 * Loop - Portafolio
 * 
 * Uso:
 * // Con query principal
 * get_template_part('template-parts/loops/loop', 'portafolio');
 * 
 * // Con query personalizada
 * set_query_var('custom_query', $mi_query);
 * get_template_part('template-parts/loops/loop', 'portafolio');
 * 
 * // Con array de IDs
 * set_query_var('post_ids', $array_de_ids);
 * get_template_part('template-parts/loops/loop', 'portafolio');
 */

if (!defined('ABSPATH')) exit;

// Detectar qué query usar
if ($post_ids = get_query_var('post_ids')) {
    $query = new WP_Query([
        'post_type' => 'mg_portafolio',
        'post__in' => (array) $post_ids,
        'posts_per_page' => -1,
        'orderby' => 'post__in'
    ]);
} elseif ($custom_query = get_query_var('custom_query')) {
    $query = $custom_query;
} else {
    $query = $GLOBALS['wp_query'];
}

if ($query->have_posts()) :
    while ($query->have_posts()) : $query->the_post();
    echo '<div class="col-xl-4 col-lg-6 mt-0">';
        get_template_part('template-parts/card', 'portafolio');
        echo '</div>';
    endwhile;
    
    if ($post_ids || $custom_query) {
        wp_reset_postdata();
    }
else {
    echo '<p class="text-muted">' . __('No se encontraron proyectos en el portafolio.', 'maggiore') . '</p>'; }
endif;

set_query_var('post_ids', null);
set_query_var('custom_query', null);
