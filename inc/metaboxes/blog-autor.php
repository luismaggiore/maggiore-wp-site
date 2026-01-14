<?php
if (!defined('ABSPATH')) exit;

/**
 * Metabox para asignar un miembro del equipo como autor del post.
 */
function mg_metabox_blog_autor() {
    add_meta_box(
        'mg_blog_autor',
        __('Autor del artículo (miembro del equipo)', 'maggiore'),
        'mg_blog_autor_callback',
        'post',
        'side',
        'default'
    );
}
add_action('add_meta_boxes', 'mg_metabox_blog_autor');

function mg_blog_autor_callback($post) {
    $autor_id = get_post_meta($post->ID, 'mg_blog_autor', true);
    $miembros = get_posts([
        'post_type' => 'mg_equipo',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    echo '<select name="mg_blog_autor" class="widefat">';
    echo '<option value="">' . __('— Seleccionar miembro del equipo —', 'maggiore') . '</option>';
    foreach ($miembros as $miembro) {
        $selected = selected($autor_id, $miembro->ID, false);
        echo '<option value="' . esc_attr($miembro->ID) . '" ' . $selected . '>' . esc_html($miembro->post_title) . '</option>';
    }
    echo '</select>';
}

function mg_save_blog_autor($post_id) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    if (isset($_POST['mg_blog_autor'])) {
        $autor_id = intval($_POST['mg_blog_autor']);
        update_post_meta($post_id, 'mg_blog_autor', $autor_id);
    }
}
add_action('save_post_post', 'mg_save_blog_autor');
