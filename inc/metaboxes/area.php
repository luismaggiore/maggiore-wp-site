<?php
if (!defined('ABSPATH')) exit;

add_action('add_meta_boxes', function () {
    add_meta_box(
        'mg_area_detalles',
        __('Detalles del Área', 'maggiore'),
        'mg_area_metabox_callback',
        'mg_area',
        'normal',
        'default'
    );
});

/**
 * Cargar el color picker de WordPress en el admin
 */
add_action('admin_enqueue_scripts', function ($hook) {
    global $post;

    if (!$post) return;
    if ($post->post_type !== 'mg_area') return;

    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
});

function mg_area_metabox_callback($post) {
    $descripcion = get_post_meta($post->ID, 'mg_area_descripcion', true);
    $director_id = get_post_meta($post->ID, 'mg_area_director', true);
    $miembros = (array) get_post_meta($post->ID, 'mg_area_miembros', true);

    // NUEVO: color guardado
    $color = get_post_meta($post->ID, 'mg_area_color', true);

    $lang = function_exists('pll_get_post_language') ? pll_get_post_language($post->ID) : false;

    $args = [
        'post_type' => 'mg_equipo',
        'numberposts' => -1,
        'orderby' => 'title',
        'order' => 'ASC',
    ];

    if ($lang) {
        $args['lang'] = $lang;
    }

    $todos_equipo = get_posts($args);
    ?>

    <!-- DESCRIPCIÓN -->
    <p>
        <label for="mg_area_descripcion">
            <strong><?php _e('Descripción del área', 'maggiore'); ?></strong>
        </label>
    </p>
    <textarea name="mg_area_descripcion" id="mg_area_descripcion" rows="4" class="widefat"><?= esc_textarea($descripcion); ?></textarea>

    <hr>

    <!-- DIRECTOR -->
    <p>
        <label for="mg_area_director">
            <strong><?php _e('Director del área', 'maggiore'); ?></strong>
        </label>
    </p>
    <select name="mg_area_director" id="mg_area_director" class="widefat">
        <option value=""><?php _e('— Sin director —', 'maggiore'); ?></option>
        <?php foreach ($todos_equipo as $persona): ?>
            <option value="<?= esc_attr($persona->ID); ?>" <?= selected($director_id, $persona->ID, false); ?>>
                <?= esc_html($persona->post_title); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <hr>

    <!-- MIEMBROS DEL EQUIPO -->
    <p>
        <label for="mg_area_miembros[]">
            <strong><?php _e('Miembros del área', 'maggiore'); ?></strong>
        </label>
    </p>
    <select name="mg_area_miembros[]" id="mg_area_miembros" class="widefat select2" multiple>
        <?php foreach ($todos_equipo as $persona): ?>
            <option value="<?= esc_attr($persona->ID); ?>" <?= in_array($persona->ID, $miembros) ? 'selected' : ''; ?>>
                <?= esc_html($persona->post_title); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <hr>

    <!-- NUEVO: COLOR -->
    <p>
        <label for="mg_area_color">
            <strong><?php _e('Color del área', 'maggiore'); ?></strong>
        </label>
    </p>
    <input
        type="text"
        name="mg_area_color"
        id="mg_area_color"
        class="widefat mg-color-field"
        value="<?= esc_attr($color ?: '#000000'); ?>"
    />

    <script>
        jQuery(document).ready(function ($) {
            $('#mg_area_miembros').select2();

            // Activar el selector de color de WP
            $('.mg-color-field').wpColorPicker();
        });
    </script>

    <?php
}

add_action('save_post_mg_area', function ($post_id) {
    if (isset($_POST['mg_area_descripcion'])) {
        update_post_meta($post_id, 'mg_area_descripcion', sanitize_textarea_field($_POST['mg_area_descripcion']));
    }

    if (isset($_POST['mg_area_director'])) {
        update_post_meta($post_id, 'mg_area_director', intval($_POST['mg_area_director']));
    }

    if (isset($_POST['mg_area_miembros'])) {
        $ids = array_filter(array_map('intval', $_POST['mg_area_miembros']));
        update_post_meta($post_id, 'mg_area_miembros', $ids);
    }

    // NUEVO: guardar color
    if (isset($_POST['mg_area_color'])) {
        $color = sanitize_hex_color($_POST['mg_area_color']);
        update_post_meta($post_id, 'mg_area_color', $color);
    }
});
