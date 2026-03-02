<?php
/**
 * Añadir campo de color a taxonomías + Fix guardado de jerarquía (parent)
 * Compatible con Polylang: usa wpdb directo con prioridad 999 para correr
 * después de que Polylang termine sus sincronizaciones.
 */

if (!defined('ABSPATH')) exit;

// ============================================================
// FIX DE JERARQUÍA (PARENT) — Independiente del color
// ============================================================

/**
 * Captura el parent enviado por el formulario ANTES de que se procese
 * (Polylang puede modificar $_POST durante el ciclo de edición)
 */
add_action('load-term.php', function() {
    if (
        isset($_POST['action'])   && $_POST['action'] === 'editedtag' &&
        isset($_POST['taxonomy']) && isset($_POST['tag_ID'])           &&
        isset($_POST['parent'])
    ) {
        // Guardar el parent deseado en una variable global segura
        $GLOBALS['mg_intended_parent'] = (int) $_POST['parent'];
        $GLOBALS['mg_intended_term_id'] = (int) $_POST['tag_ID'];
        $GLOBALS['mg_intended_taxonomy'] = sanitize_key($_POST['taxonomy']);
    }
});

/**
 * Aplica el parent directamente en la tabla term_taxonomy con wpdb,
 * corriendo en prioridad 999 para ejecutarse DESPUÉS de Polylang.
 */
add_action('edited_term', function($term_id, $tt_id, $taxonomy) {
    // Solo si tenemos datos capturados y coinciden con este término
    if (
        !isset($GLOBALS['mg_intended_term_id']) ||
        (int) $GLOBALS['mg_intended_term_id'] !== (int) $term_id
    ) {
        return;
    }

    $taxonomies_gestionadas = ['mg_categoria', 'mg_industria', 'mg_equipos', 'mg_categoria_portafolio'];
    if (!in_array($taxonomy, $taxonomies_gestionadas)) return;

    $new_parent = $GLOBALS['mg_intended_parent'];

    // Verificar que el parent no sea el propio término (evitar loop)
    if ((int) $new_parent === (int) $term_id) return;

    // Verificar parent actual para no hacer update innecesario
    global $wpdb;
    $current_parent = (int) $wpdb->get_var($wpdb->prepare(
        "SELECT parent FROM {$wpdb->term_taxonomy} WHERE term_id = %d AND taxonomy = %s",
        $term_id,
        $taxonomy
    ));

    if ($current_parent === $new_parent) return;

    // Escribir directamente en la DB, bypasseando todos los hooks de Polylang
    $wpdb->update(
        $wpdb->term_taxonomy,
        ['parent' => $new_parent],
        ['term_id' => $term_id, 'taxonomy' => $taxonomy],
        ['%d'],
        ['%d', '%s']
    );

    // Limpiar caché para que WordPress refleje el cambio inmediatamente
    clean_term_cache($term_id, $taxonomy);
    delete_option("{$taxonomy}_children");

    // Limpiar variables globales
    unset($GLOBALS['mg_intended_parent'], $GLOBALS['mg_intended_term_id'], $GLOBALS['mg_intended_taxonomy']);

}, 999, 3);


// ============================================================
// CAMPOS DE COLOR EN TAXONOMÍAS
// ============================================================

function mg_add_taxonomy_color_fields() {
    $taxonomies = ['mg_categoria', 'mg_industria', 'mg_equipos', 'mg_categoria_portafolio'];

    foreach ($taxonomies as $taxonomy) {
        add_action("{$taxonomy}_add_form_fields",  'mg_add_taxonomy_color_field',  10, 2);
        add_action("{$taxonomy}_edit_form_fields", 'mg_edit_taxonomy_color_field', 10, 2);
        add_action("edited_{$taxonomy}",           'mg_save_taxonomy_color_field', 10, 2);
        add_action("create_{$taxonomy}",           'mg_save_taxonomy_color_field', 10, 2);
    }
}
add_action('init', 'mg_add_taxonomy_color_fields');

/**
 * Campo de color al CREAR un nuevo término
 */
function mg_add_taxonomy_color_field($taxonomy) {
    wp_nonce_field('mg_taxonomy_color_nonce', 'mg_taxonomy_color_nonce_field');
    ?>
    <div class="form-field term-color-wrap">
        <label for="term-color"><?php _e('Color', 'maggiore'); ?></label>
        <input type="text" name="term_color" id="term-color" value="#2271b1" class="mg-color-picker" />
        <p class="description"><?php _e('Selecciona un color para este término. Se usará en el diseño del sitio.', 'maggiore'); ?></p>
    </div>
    <?php
}

/**
 * Campo de color al EDITAR un término existente
 */
function mg_edit_taxonomy_color_field($term, $taxonomy) {
    $term_id = $term->term_id;
    $color   = get_term_meta($term_id, 'term_color', true);
    $color   = !empty($color) ? $color : '#2271b1';

    wp_nonce_field('mg_taxonomy_color_nonce', 'mg_taxonomy_color_nonce_field');
    ?>
    <tr class="form-field term-color-wrap">
        <th scope="row">
            <label for="term-color"><?php _e('Color', 'maggiore'); ?></label>
        </th>
        <td>
            <input type="text" name="term_color" id="term-color" value="<?php echo esc_attr($color); ?>" class="mg-color-picker" />
            <p class="description"><?php _e('Selecciona un color para este término. Se usará en el diseño del sitio.', 'maggiore'); ?></p>
        </td>
    </tr>
    <?php
}

/**
 * Guardar el campo de color con verificación de nonce
 */
function mg_save_taxonomy_color_field($term_id) {
    if (
        ! isset($_POST['mg_taxonomy_color_nonce_field']) ||
        ! wp_verify_nonce($_POST['mg_taxonomy_color_nonce_field'], 'mg_taxonomy_color_nonce')
    ) {
        return;
    }

    if (isset($_POST['term_color'])) {
        $color = sanitize_hex_color($_POST['term_color']);
        if ($color) {
            update_term_meta($term_id, 'term_color', $color);
        } else {
            delete_term_meta($term_id, 'term_color');
        }
    }
}

/**
 * Cargar el Color Picker de WordPress en las páginas de taxonomía
 */
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'edit-tags.php' && $hook !== 'term.php') return;

    wp_enqueue_style('wp-color-picker');

    wp_register_script('mg-color-picker-init', false, ['wp-color-picker'], false, true);
    wp_enqueue_script('mg-color-picker-init');

    wp_add_inline_script('mg-color-picker-init', '
        jQuery(document).ready(function($) {
            if ($.fn.wpColorPicker) {
                $(".mg-color-picker").wpColorPicker();
            }
        });
    ');
});


// ============================================================
// COLUMNA DE COLOR EN LISTAS DE TÉRMINOS
// ============================================================

function mg_add_color_column($columns) {
    $columns['term_color'] = __('Color', 'maggiore');
    return $columns;
}

function mg_add_color_column_content($content, $column_name, $term_id) {
    if ($column_name === 'term_color') {
        $color = get_term_meta($term_id, 'term_color', true);
        $content = !empty($color)
            ? '<span style="display:inline-block; width:30px; height:30px; background-color:' . esc_attr($color) . '; border:1px solid #ddd; border-radius:3px; vertical-align:middle;"></span> ' . esc_html($color)
            : '—';
    }
    return $content;
}

$taxonomies_with_color = ['mg_categoria', 'mg_industria', 'mg_equipos', 'mg_categoria_portafolio'];
foreach ($taxonomies_with_color as $taxonomy) {
    add_filter("manage_edit-{$taxonomy}_columns",  'mg_add_color_column');
    add_filter("manage_{$taxonomy}_custom_column", 'mg_add_color_column_content', 10, 3);
}


// ============================================================
// HELPER
// ============================================================

/**
 * Obtener el color de un término con fallback
 * Uso: mg_get_term_color($term_id)
 */
function mg_get_term_color($term_id, $fallback = '#2271b1') {
    $color = get_term_meta($term_id, 'term_color', true);
    return !empty($color) ? $color : $fallback;
}
