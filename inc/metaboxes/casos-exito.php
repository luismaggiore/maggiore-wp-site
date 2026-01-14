<?php
if (!defined('ABSPATH')) exit;
require_once get_template_directory() . '/inc/helpers/cpt-relations.php';

add_action('add_meta_boxes', function () {
    add_meta_box(
        'mg_casos_relaciones',
        __('Relaciones del Caso de Éxito', 'maggiore'),
        'mg_render_metabox_casos_exito',
        'mg_caso_exito',
        'normal',
        'high'
    );
});

function mg_render_metabox_casos_exito($post) {
    wp_nonce_field('mg_save_caso_exito', 'mg_caso_exito_nonce');

    $cliente   = get_post_meta($post->ID, 'mg_caso_cliente', true);
    $servicios = get_post_meta($post->ID, 'mg_caso_servicios', true) ?: [];
    $equipo    = get_post_meta($post->ID, 'mg_caso_equipo', true) ?: [];

    $clientes     = mg_get_clientes_options();
    $serv_options = mg_get_servicios_options();
    $miembros     = mg_get_equipo_options();

    echo '<p><strong>' . __('Cliente asociado', 'maggiore') . '</strong></p>';
    echo '<select name="mg_caso_cliente" class="widefat select2">';
    echo '<option value="">' . __('Selecciona un cliente', 'maggiore') . '</option>';
    foreach ($clientes as $id => $name) {
        echo '<option value="' . esc_attr($id) . '" ' . selected($cliente, $id, false) . '>' . esc_html($name) . '</option>';
    }
    echo '</select>';

    echo '<p class="mt-3"><strong>' . __('Servicios involucrados', 'maggiore') . '</strong></p>';
    echo '<select name="mg_caso_servicios[]" class="widefat select2" multiple>';
    foreach ($serv_options as $id => $title) {
        echo '<option value="' . esc_attr($id) . '" ' . (in_array($id, $servicios) ? 'selected' : '') . '>' . esc_html($title) . '</option>';
    }
    echo '</select>';

    echo '<p class="mt-3"><strong>' . __('Miembros del equipo participantes', 'maggiore') . '</strong></p>';
    echo '<select name="mg_caso_equipo[]" class="widefat select2" multiple>';
    foreach ($miembros as $id => $title) {
        echo '<option value="' . esc_attr($id) . '" ' . (in_array($id, $id ? $equipo : []) ? 'selected' : '') . '>' . esc_html($title) . '</option>';
    }
    echo '</select>';

    echo '<hr><h3>' . __('Contenido del caso', 'maggiore') . '</h3>';

    echo '<p><label>' . __('Contexto', 'maggiore') . '</label>';
    echo '<textarea name="mg_caso_contexto" class="widefat">' . esc_textarea(get_post_meta($post->ID, 'mg_caso_contexto', true)) . '</textarea></p>';

    echo '<p><label>' . __('Acciones', 'maggiore') . '</label>';
    echo '<textarea name="mg_caso_acciones" class="widefat">' . esc_textarea(get_post_meta($post->ID, 'mg_caso_acciones', true)) . '</textarea></p>';

    echo '<p><label>' . __('Resultados', 'maggiore') . '</label>';
    echo '<textarea name="mg_caso_resultados" class="widefat">' . esc_textarea(get_post_meta($post->ID, 'mg_caso_resultados', true)) . '</textarea></p>';

    echo '<hr><h3>' . __('Landing Page', 'maggiore') . '</h3>';

    echo '<p><label>' . __('Encabezado', 'maggiore') . '</label>';
    echo '<input type="text" name="mg_caso_landing_titulo" class="widefat" value="' . esc_attr(get_post_meta($post->ID, 'mg_caso_landing_titulo', true)) . '"></p>';

    echo '<p><label>' . __('Bajada', 'maggiore') . '</label>';
    echo '<textarea name="mg_caso_landing_bajada" class="widefat">' . esc_textarea(get_post_meta($post->ID, 'mg_caso_landing_bajada', true)) . '</textarea></p>';

    echo '<p><label><input type="checkbox" name="mg_caso_aparece_en_landing" value="1" ' . checked(get_post_meta($post->ID, 'mg_caso_aparece_en_landing', true), 1, false) . '> ' . __('Mostrar en landing', 'maggiore') . '</label></p>';

    echo '<hr><h3>' . __('Cita del contratador', 'maggiore') . '</h3>';

    echo '<p><label>' . __('Nombre del contratador', 'maggiore') . '</label>';
    echo '<input type="text" name="mg_caso_contratador_nombre" class="widefat" value="' . esc_attr(get_post_meta($post->ID, 'mg_caso_contratador_nombre', true)) . '"></p>';

    echo '<p><label>' . __('Cargo del contratador', 'maggiore') . '</label>';
    echo '<input type="text" name="mg_caso_contratador_cargo" class="widefat" value="' . esc_attr(get_post_meta($post->ID, 'mg_caso_contratador_cargo', true)) . '"></p>';

    echo '<p><label>' . __('LinkedIn del contratador', 'maggiore') . '</label>';
    echo '<input type="url" name="mg_caso_contratador_linkedin" class="widefat" value="' . esc_attr(get_post_meta($post->ID, 'mg_caso_contratador_linkedin', true)) . '"></p>';

    echo '<p><label>' . __('Cita destacada', 'maggiore') . '</label>';
    echo '<textarea name="mg_caso_contratador_cita" class="widefat">' . esc_textarea(get_post_meta($post->ID, 'mg_caso_contratador_cita', true)) . '</textarea></p>';

  $img_id = get_post_meta($post->ID, 'mg_caso_contratador_img', true);
$img_url = $img_id ? wp_get_attachment_image_url($img_id, 'thumbnail') : '';

echo '<p><strong>' . __('Imagen del contratador', 'maggiore') . '</strong></p>';

echo '<div class="mg-image-field">';
echo '<img id="mg_contratador_preview" src="' . esc_url($img_url) . '" style="max-width:150px; display:' . ($img_url ? 'block' : 'none') . ';">';
echo '<input type="hidden" id="mg_caso_contratador_img" name="mg_caso_contratador_img" value="' . esc_attr($img_id) . '">';
echo '<button type="button" class="button mg-upload-image">' . __('Seleccionar imagen', 'maggiore') . '</button> ';
echo '<button type="button" class="button mg-remove-image">' . __('Quitar', 'maggiore') . '</button>';
echo '</div>';

    echo '<hr><h3>' . __('Fecha del proyecto', 'maggiore') . '</h3>';
    $fecha = get_post_meta($post->ID, 'mg_caso_fecha', true);
    echo '<input type="month" name="mg_caso_fecha" class="widefat" value="' . esc_attr($fecha) . '">';
    
    // Mostrar automáticos si los hay
    $auto_serv = get_post_meta($post->ID, '_mg_servicios_auto', true) ?: [];
    if (!empty($auto_serv)) {
        echo '<div class="mt-3"><strong>' . __('Servicios agregados automáticamente:', 'maggiore') . '</strong><ul>';
        foreach ($auto_serv as $sid) {
            echo '<li>' . esc_html(get_the_title($sid)) . '</li>';
        }
        echo '</ul></div>';
    }
}

add_action('save_post_mg_caso_exito', function ($post_id) {
    if (!isset($_POST['mg_caso_exito_nonce']) || !wp_verify_nonce($_POST['mg_caso_exito_nonce'], 'mg_save_caso_exito')) return;

    update_post_meta($post_id, 'mg_caso_cliente', intval($_POST['mg_caso_cliente'] ?? 0));
    update_post_meta($post_id, 'mg_caso_servicios', array_map('intval', $_POST['mg_caso_servicios'] ?? []));
    update_post_meta($post_id, 'mg_caso_equipo', array_map('intval', $_POST['mg_caso_equipo'] ?? []));

    update_post_meta($post_id, 'mg_caso_contexto', sanitize_textarea_field($_POST['mg_caso_contexto'] ?? ''));
    update_post_meta($post_id, 'mg_caso_acciones', sanitize_textarea_field($_POST['mg_caso_acciones'] ?? ''));
    update_post_meta($post_id, 'mg_caso_resultados', sanitize_textarea_field($_POST['mg_caso_resultados'] ?? ''));

    update_post_meta($post_id, 'mg_caso_landing_titulo', sanitize_text_field($_POST['mg_caso_landing_titulo'] ?? ''));
    update_post_meta($post_id, 'mg_caso_landing_bajada', sanitize_textarea_field($_POST['mg_caso_landing_bajada'] ?? ''));
    update_post_meta($post_id, 'mg_caso_aparece_en_landing', isset($_POST['mg_caso_aparece_en_landing']) ? 1 : 0);

    update_post_meta($post_id, 'mg_caso_contratador_nombre', sanitize_text_field($_POST['mg_caso_contratador_nombre'] ?? ''));
    update_post_meta($post_id, 'mg_caso_contratador_cargo', sanitize_text_field($_POST['mg_caso_contratador_cargo'] ?? ''));
    update_post_meta($post_id, 'mg_caso_contratador_linkedin', esc_url_raw($_POST['mg_caso_contratador_linkedin'] ?? ''));
    update_post_meta($post_id, 'mg_caso_contratador_cita', sanitize_textarea_field($_POST['mg_caso_contratador_cita'] ?? ''));
    update_post_meta($post_id, 'mg_caso_contratador_img', intval($_POST['mg_caso_contratador_img'] ?? 0));

    update_post_meta($post_id, 'mg_caso_fecha', sanitize_text_field($_POST['mg_caso_fecha'] ?? ''));
});
