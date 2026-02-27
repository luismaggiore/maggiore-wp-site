<?php
if (!defined('ABSPATH')) exit;

require_once get_template_directory() . '/inc/helpers/cpt-relations.php';

/**
 * Agrega metaboxes al CPT Cliente
 */
add_action('add_meta_boxes', function () {
    add_meta_box(
        'mg_cliente_info_servicios',
        __('Información del Cliente', 'maggiore'),
        'mg_render_metabox_clientes',
        'mg_cliente',
        'normal',
        'high'
    );
});

/**
 * Renderiza el metabox de cliente
 */
function mg_render_metabox_clientes($post) {
    wp_nonce_field('mg_save_cliente', 'mg_cliente_nonce');

    // === CAMPOS EXISTENTES ===
    $descripcion     = get_post_meta($post->ID, 'mg_cliente_descripcion', true);
    $manual_servicios = get_post_meta($post->ID, 'mg_cliente_servicios', true) ?: [];
    $auto_servicios   = get_post_meta($post->ID, '_mg_servicios_auto', true) ?: [];

    // === NUEVOS CAMPOS ===
    $inicio_contrato  = get_post_meta($post->ID, 'mg_cliente_inicio_contrato', true);
    $termino_contrato = get_post_meta($post->ID, 'mg_cliente_termino_contrato', true);
    $empleados        = get_post_meta($post->ID, 'mg_cliente_num_empleados', true);
    $linkedin         = get_post_meta($post->ID, 'mg_cliente_linkedin', true);
    $no_indexar = get_post_meta($post->ID, 'mg_cliente_no_indexar', true);
    // Opciones
    $servicios_options = mg_get_servicios_options();

    // === DESCRIPCIÓN ===
    echo '<p><label for="mg_cliente_descripcion"><strong>' . __('Descripción breve del cliente', 'maggiore') . '</strong></label></p>';
    echo '<textarea name="mg_cliente_descripcion" id="mg_cliente_descripcion" class="widefat" rows="3"
        placeholder="' . esc_attr__('Ej: Empresa líder en tecnología agrícola', 'maggiore') . '">' .
        esc_textarea($descripcion) . '</textarea>';

    echo '<hr style="margin: 2em 0;">';

    // === DATOS CONTRACTUALES Y CORPORATIVOS ===
    echo '<h4>' . __('Información contractual y corporativa', 'maggiore') . '</h4>';

    echo '<p>
        <label><strong>' . __('Inicio de relación contractual', 'maggiore') . '</strong></label><br>
        <input type="date" name="mg_cliente_inicio_contrato" value="' . esc_attr($inicio_contrato) . '" class="widefat">
    </p>';

    echo '<p>
        <label><strong>' . __('Término de relación contractual', 'maggiore') . '</strong></label><br>
        <input type="date" name="mg_cliente_termino_contrato" value="' . esc_attr($termino_contrato) . '" class="widefat">
    </p>';

    echo '<p>
        <label><strong>' . __('Número de empleados', 'maggiore') . '</strong></label><br>
        <input type="number" name="mg_cliente_num_empleados" value="' . esc_attr($empleados) . '" min="1" class="widefat">
    </p>';

    echo '<p>
        <label><strong>' . __('LinkedIn de la empresa', 'maggiore') . '</strong></label><br>
        <input type="url" name="mg_cliente_linkedin"
               value="' . esc_attr($linkedin) . '"
               placeholder="https://www.linkedin.com/company/empresa/"
               class="widefat">
    </p>';

    echo '<hr style="margin: 2em 0;">';

    // === CHECKBOX NO INDEXAR ===
echo '<div style="background: #fff3cd; border-left: 4px solid #ffc107; padding: 12px; margin-bottom: 20px;">';
echo '<label style="display: flex; align-items: center; gap: 10px; cursor: pointer;">';
echo '<input type="checkbox" name="mg_cliente_no_indexar" value="1" ' . checked($no_indexar, '1', false) . '>';
echo '<strong>' . __('No indexar este cliente', 'maggiore') . '</strong>';
echo '</label>';
echo '<p style="margin: 8px 0 0 28px; color: #666; font-size: 13px;">';
echo __('Si está marcado, este cliente NO aparecerá en listados públicos (archive, home, etc.) pero SÍ podrá ser referenciado en portafolios y casos de éxito.', 'maggiore');
echo '</p>';
echo '</div>';


    // === SERVICIOS MANUALES ===
    echo '<p><strong>' . __('Servicios contratados (manual)', 'maggiore') . '</strong></p>';
    echo '<select name="mg_cliente_servicios[]" class="widefat select2" multiple>';
    foreach ($servicios_options as $id => $label) {
        $selected = in_array($id, $manual_servicios) ? 'selected' : '';
        echo "<option value='{$id}' {$selected}>{$label}</option>";
    }
    echo '</select>';

    // === SERVICIOS AUTOMÁTICOS ===
    if (!empty($auto_servicios)) {
        echo '<div class="mt-3"><strong>' .
            __('Servicios agregados automáticamente desde Casos de Éxito y Portafolios:', 'maggiore') .
            '</strong><ul>';

        foreach ($auto_servicios as $id) {
            echo '<li>' . esc_html(get_the_title($id)) . '</li>';
        }

        echo '</ul><p style="font-style:italic; color:#666;">' .
            __('Estos servicios se asocian automáticamente si el cliente aparece en otros contenidos relacionados.', 'maggiore') .
            '</p></div>';
    }
}

/**
 * Guardado de los campos del cliente
 */
add_action('save_post_mg_cliente', function ($post_id) {

    if (!isset($_POST['mg_cliente_nonce']) ||
        !wp_verify_nonce($_POST['mg_cliente_nonce'], 'mg_save_cliente')) return;

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    // === CAMPOS EXISTENTES ===
    update_post_meta(
        $post_id,
        'mg_cliente_descripcion',
        sanitize_textarea_field($_POST['mg_cliente_descripcion'] ?? '')
    );

    $manual = isset($_POST['mg_cliente_servicios'])
        ? array_map('intval', $_POST['mg_cliente_servicios'])
        : [];
    update_post_meta($post_id, 'mg_cliente_servicios', $manual);

    // === NUEVOS CAMPOS ===
    update_post_meta(
        $post_id,
        'mg_cliente_inicio_contrato',
        sanitize_text_field($_POST['mg_cliente_inicio_contrato'] ?? '')
    );

    update_post_meta(
        $post_id,
        'mg_cliente_termino_contrato',
        sanitize_text_field($_POST['mg_cliente_termino_contrato'] ?? '')
    );

    update_post_meta(
        $post_id,
        'mg_cliente_num_empleados',
        intval($_POST['mg_cliente_num_empleados'] ?? 0)
    );

    update_post_meta(
        $post_id,
        'mg_cliente_linkedin',
        esc_url_raw($_POST['mg_cliente_linkedin'] ?? '')
    );
        // Guardar checkbox no indexar
    $no_indexar = isset($_POST['mg_cliente_no_indexar']) ? '1' : '0';
    update_post_meta($post_id, 'mg_cliente_no_indexar', $no_indexar);
});
