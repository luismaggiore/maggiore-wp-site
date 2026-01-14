<?php
if (!defined('ABSPATH')) exit;

add_action('add_meta_boxes', function () {
    add_meta_box(
        'mg_servicio_detalles',
        __('Detalles del Servicio', 'maggiore'),
        'mg_servicio_metabox_callback',
        'mg_servicio',
        'normal',
        'high'
    );
});

function mg_servicio_metabox_callback($post) {

    wp_nonce_field('mg_save_servicio', 'mg_servicio_nonce');

    // Idioma actual (Polylang safe)
    $lang = function_exists('pll_get_post_language') ? pll_get_post_language($post->ID) : false;

    // =========================
    // VALORES GUARDADOS
    // =========================
    $area_id        = get_post_meta($post->ID, 'mg_servicio_area', true);
    $director_id    = get_post_meta($post->ID, 'mg_servicio_director', true);
    $bajada         = get_post_meta($post->ID, 'mg_servicio_bajada', true);
    $consiste       = get_post_meta($post->ID, 'mg_servicio_consiste', true);
    $proceso        = get_post_meta($post->ID, 'mg_servicio_proceso', true);
    $entregables    = get_post_meta($post->ID, 'mg_servicio_entregables', true);
    $para_quien     = get_post_meta($post->ID, 'mg_servicio_para_quien', true);
    $beneficios     = get_post_meta($post->ID, 'mg_servicio_beneficios', true);
    $ventajas       = get_post_meta($post->ID, 'mg_servicio_ventajas', true);
    $precio         = get_post_meta($post->ID, 'mg_servicio_precio', true);

    // =========================
    // AREAS
    // =========================
    $area_args = [
        'post_type'   => 'mg_area',
        'numberposts' => -1,
        'orderby'     => 'title',
        'order'       => 'ASC',
    ];
    if ($lang) $area_args['lang'] = $lang;
    $areas = get_posts($area_args);

    // =========================
    // EQUIPO (DIRECTOR)
    // =========================
    $equipo_args = [
        'post_type'   => 'mg_equipo',
        'numberposts' => -1,
        'orderby'     => 'title',
        'order'       => 'ASC',
    ];
    if ($lang) $equipo_args['lang'] = $lang;
    $equipo = get_posts($equipo_args);

    // =========================
    // UI
    // =========================
    ?>

    <p><strong><?php _e('Área responsable', 'maggiore'); ?></strong></p>
    <select name="mg_servicio_area" class="widefat">
        <option value=""><?php _e('— Sin asignar —', 'maggiore'); ?></option>
        <?php foreach ($areas as $area): ?>
            <option value="<?= esc_attr($area->ID); ?>" <?= selected($area_id, $area->ID, false); ?>>
                <?= esc_html($area->post_title); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <hr>

    <p><strong><?php _e('Director del servicio (opcional)', 'maggiore'); ?></strong></p>
    <select name="mg_servicio_director" class="widefat">
        <option value=""><?php _e('— Sin asignar —', 'maggiore'); ?></option>
        <?php foreach ($equipo as $persona): ?>
            <option value="<?= esc_attr($persona->ID); ?>" <?= selected($director_id, $persona->ID, false); ?>>
                <?= esc_html($persona->post_title); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <hr>
     <p><strong><?php _e('Bajada breve', 'maggiore'); ?></strong></p>
     <textarea name="mg_servicio_bajada" class="widefat" rows="2"><?= esc_textarea($bajada); ?></textarea>


    <p><strong><?php _e('¿En qué consiste el servicio?', 'maggiore'); ?></strong></p>
    <?php wp_editor($consiste, 'mg_servicio_consiste', ['textarea_rows' => 4]); ?>

    <p><strong><?php _e('Proceso', 'maggiore'); ?></strong></p>
    <?php wp_editor($proceso, 'mg_servicio_proceso', ['textarea_rows' => 4]); ?>

    <p><strong><?php _e('Entregables', 'maggiore'); ?></strong></p>
    <?php wp_editor($entregables, 'mg_servicio_entregables', ['textarea_rows' => 4]); ?>

    <p><strong><?php _e('¿Para quién es el servicio? ¿Cuándo lo necesita?', 'maggiore'); ?></strong></p>
    <?php wp_editor($para_quien, 'mg_servicio_para_quien', ['textarea_rows' => 4]); ?>

    <p><strong><?php _e('Beneficios al obtenerlo', 'maggiore'); ?></strong></p>
    <?php wp_editor($beneficios, 'mg_servicio_beneficios', ['textarea_rows' => 4]); ?>

    <p><strong><?php _e('Ventajas frente a la competencia', 'maggiore'); ?></strong></p>
        <?php wp_editor($ventajas, 'mg_servicio_ventajas', ['textarea_rows' => 4]); ?>


    <p><strong><?php _e('Precio al cliente', 'maggiore'); ?></strong></p>
    
    <textarea name="mg_servicio_precio" class="widefat" rows="3"><?= esc_textarea($precio); ?></textarea>

<?php
}

add_action('save_post_mg_servicio', function ($post_id) {

    if (!isset($_POST['mg_servicio_nonce']) ||
        !wp_verify_nonce($_POST['mg_servicio_nonce'], 'mg_save_servicio')) {
        return;
    }

    update_post_meta($post_id, 'mg_servicio_area', intval($_POST['mg_servicio_area'] ?? 0));
    update_post_meta($post_id, 'mg_servicio_director', intval($_POST['mg_servicio_director'] ?? 0));

    update_post_meta($post_id, 'mg_servicio_consiste', wp_kses_post($_POST['mg_servicio_consiste'] ?? ''));
    update_post_meta($post_id, 'mg_servicio_proceso', wp_kses_post($_POST['mg_servicio_proceso'] ?? ''));
    update_post_meta($post_id, 'mg_servicio_entregables', wp_kses_post($_POST['mg_servicio_entregables'] ?? ''));
    update_post_meta($post_id, 'mg_servicio_para_quien', wp_kses_post($_POST['mg_servicio_para_quien'] ?? ''));
    update_post_meta($post_id, 'mg_servicio_beneficios', wp_kses_post($_POST['mg_servicio_beneficios'] ?? ''));
    update_post_meta($post_id, 'mg_servicio_ventajas', wp_kses_post($_POST['mg_servicio_ventajas'] ?? ''));
    update_post_meta($post_id, 'mg_servicio_precio', sanitize_textarea_field($_POST['mg_servicio_precio'] ?? ''));
    update_post_meta($post_id, 'mg_servicio_bajada', sanitize_textarea_field($_POST['mg_servicio_bajada'] ?? ''));

});
