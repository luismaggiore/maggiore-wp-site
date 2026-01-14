<?php
/**
 * Añadir campo de color a taxonomías
 * Permite al admin asignar un color a cada término de taxonomía desde el backend
 */

if (!defined('ABSPATH')) exit;

/**
 * Añadir campos de color a las taxonomías especificadas
 */
function mg_add_taxonomy_color_fields() {
    // Lista de taxonomías donde quieres añadir el campo de color
    $taxonomies = ['mg_categoria', 'mg_industria', 'mg_equipos']; // Añade las que necesites
    
    foreach ($taxonomies as $taxonomy) {
        // Campo al crear un nuevo término
        add_action("{$taxonomy}_add_form_fields", 'mg_add_taxonomy_color_field', 10, 2);
        
        // Campo al editar un término existente
        add_action("{$taxonomy}_edit_form_fields", 'mg_edit_taxonomy_color_field', 10, 2);
        
        // Guardar el campo
        add_action("edited_{$taxonomy}", 'mg_save_taxonomy_color_field', 10, 2);
        add_action("create_{$taxonomy}", 'mg_save_taxonomy_color_field', 10, 2);
    }
}
add_action('init', 'mg_add_taxonomy_color_fields');

/**
 * Campo de color al CREAR un nuevo término
 */
function mg_add_taxonomy_color_field($taxonomy) {
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
    $color = get_term_meta($term_id, 'term_color', true);
    $color = !empty($color) ? $color : '#2271b1'; // Color por defecto
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
 * Guardar el campo de color
 */
function mg_save_taxonomy_color_field($term_id) {
    if (isset($_POST['term_color']) && !empty($_POST['term_color'])) {
        update_term_meta($term_id, 'term_color', sanitize_hex_color($_POST['term_color']));
    }
}

/**
 * Cargar el color picker de WordPress en las páginas de taxonomía
 */
add_action('admin_enqueue_scripts', function($hook) {
    if ($hook !== 'edit-tags.php' && $hook !== 'term.php') {
        return;
    }
    
    // Cargar WordPress Color Picker
    wp_enqueue_style('wp-color-picker');
    wp_enqueue_script('wp-color-picker');
    
    // Script para inicializar el color picker
    wp_add_inline_script('wp-color-picker', '
        jQuery(document).ready(function($) {
            $(".mg-color-picker").wpColorPicker();
        });
    ');
});

/**
 * Añadir columna de color en la lista de términos
 */
function mg_add_color_column($columns) {
    $columns['term_color'] = __('Color', 'maggiore');
    return $columns;
}

function mg_add_color_column_content($content, $column_name, $term_id) {
    if ($column_name === 'term_color') {
        $color = get_term_meta($term_id, 'term_color', true);
        if (!empty($color)) {
            $content = '<span style="display:inline-block; width:30px; height:30px; background-color:' . esc_attr($color) . '; border:1px solid #ddd; border-radius:3px; vertical-align:middle;"></span> ' . esc_html($color);
        } else {
            $content = '—';
        }
    }
    return $content;
}

// Aplicar las columnas a las taxonomías
$taxonomies_with_color = ['mg_categoria', 'mg_industria', 'mg_equipos'];
foreach ($taxonomies_with_color as $taxonomy) {
    add_filter("manage_edit-{$taxonomy}_columns", 'mg_add_color_column');
    add_filter("manage_{$taxonomy}_custom_column", 'mg_add_color_column_content', 10, 3);
}

/**
 * Función helper para obtener el color de un término
 * Uso: mg_get_term_color($term_id, $fallback_color)
 */
function mg_get_term_color($term_id, $fallback = '#2271b1') {
    $color = get_term_meta($term_id, 'term_color', true);
    return !empty($color) ? $color : $fallback;
}

/**
 * Función helper para obtener el color de un término por slug
 * Uso: mg_get_term_color_by_slug($term_slug, $taxonomy, $fallback_color)
 */
function mg_get_term_color_by_slug($slug, $taxonomy, $fallback = '#2271b1') {
    $term = get_term_by('slug', $slug, $taxonomy);
    if ($term && !is_wp_error($term)) {
        return mg_get_term_color($term->term_id, $fallback);
    }
    return $fallback;
}
