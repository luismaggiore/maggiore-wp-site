<?php
/**
 * Término Principal de Taxonomía (mg_equipos)
 * 
 * Permite elegir cuál equipo/clasificación aparece primero cuando
 * un miembro tiene más de uno asignado.
 * 
 * Solución transparente: filtra get_the_terms() globalmente,
 * sin tocar ningún template.
 * 
 * Para incluir: require_once get_template_directory() . '/inc/helpers/primary-term.php';
 */

if (!defined('ABSPATH')) exit;


// ============================================================
// 1. CAMPO EN EL METABOX DE mg_equipo
// ============================================================

/**
 * Agrega el selector de "Equipo Principal" al metabox existente de mg_equipo.
 * Se engancha a la acción de renderizado del metabox y añade el campo al final.
 */
add_action('add_meta_boxes', function() {
    add_meta_box(
        'mg_equipo_primary_term',
        __('Clasificación Principal', 'maggiore'),
        'mg_render_primary_term_metabox',
        'mg_equipo',
        'side',   // aparece en la columna lateral, junto a los checkboxes de taxonomía
        'default'
    );
}, 20); // prioridad 20 para que aparezca después del metabox principal

function mg_render_primary_term_metabox($post) {
    wp_nonce_field('mg_save_primary_term', 'mg_primary_term_nonce');

    // Obtener los equipos asignados actualmente a este miembro
    $assigned_terms = get_the_terms($post->ID, 'mg_equipos');
    $primary_term   = get_post_meta($post->ID, 'mg_primary_equipos_term', true);

    if (empty($assigned_terms) || is_wp_error($assigned_terms)) {
        echo '<p style="color:#888; font-size:13px;">' . __('Asigna al menos un equipo en la caja de Equipos para poder elegir el principal.', 'maggiore') . '</p>';
        return;
    }
    ?>

    <p style="font-size:13px; color:#555; margin-bottom:10px;">
        <?php _e('Elige qué clasificación aparece primera cuando este miembro pertenece a más de un equipo.', 'maggiore'); ?>
    </p>

    <select name="mg_primary_equipos_term" style="width:100%;">
        <option value=""><?php _e('— Automático (alfabético) —', 'maggiore'); ?></option>
        <?php foreach ($assigned_terms as $term): ?>
            <option value="<?= esc_attr($term->term_id); ?>" <?= selected($primary_term, $term->term_id, false); ?>>
                <?= esc_html($term->name); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <p style="font-size:12px; color:#888; margin-top:8px;">
        <?php _e('Si el miembro solo tiene un equipo asignado, este campo no tiene efecto.', 'maggiore'); ?>
    </p>

    <?php
}


// ============================================================
// 2. GUARDAR EL CAMPO
// ============================================================

add_action('save_post_mg_equipo', function($post_id) {
    // Verificaciones de seguridad estándar
    if (!isset($_POST['mg_primary_term_nonce'])) return;
    if (!wp_verify_nonce($_POST['mg_primary_term_nonce'], 'mg_save_primary_term')) return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (!current_user_can('edit_post', $post_id)) return;

    if (isset($_POST['mg_primary_equipos_term'])) {
        $value = absint($_POST['mg_primary_equipos_term']);
        if ($value) {
            update_post_meta($post_id, 'mg_primary_equipos_term', $value);
        } else {
            delete_post_meta($post_id, 'mg_primary_equipos_term');
        }
    }
});


// ============================================================
// 3. FILTRO GLOBAL: reordenar get_the_terms() automáticamente
// ============================================================

/**
 * Intercepta get_the_terms() para mg_equipos y mueve el término
 * principal al inicio del array.
 *
 * Esto afecta TODAS las llamadas a get_the_terms($id, 'mg_equipos')
 * en templates, card-equipo.php, single-mg_equipo.php, etc.
 * Sin tocar ningún archivo.
 */
add_filter('get_the_terms', function($terms, $post_id, $taxonomy) {

    // Solo actuar sobre mg_equipos
    if ($taxonomy !== 'mg_equipos') return $terms;

    // Validar que hay términos y más de uno (si solo hay uno, no hay nada que reordenar)
    if (empty($terms) || is_wp_error($terms) || count($terms) <= 1) return $terms;

    $primary_id = (int) get_post_meta($post_id, 'mg_primary_equipos_term', true);

    // Si no hay término principal definido, devolver sin cambios
    if (!$primary_id) return $terms;

    // Buscar el término principal dentro del array
    $primary_term = null;
    $rest         = [];

    foreach ($terms as $term) {
        if ((int) $term->term_id === $primary_id) {
            $primary_term = $term;
        } else {
            $rest[] = $term;
        }
    }

    // Si encontramos el término principal, lo ponemos primero
    if ($primary_term) {
        return array_merge([$primary_term], $rest);
    }

    // Si el término guardado ya no está asignado (fue eliminado), devolver sin cambios
    return $terms;

}, 10, 3);


// ============================================================
// 4. ACTUALIZAR EL SELECTOR CUANDO CAMBIAN LOS EQUIPOS ASIGNADOS
// ============================================================

/**
 * Cuando se guardan los términos de mg_equipos de un miembro,
 * verificar que el término principal guardado sigue siendo válido.
 * Si el término fue desasignado, limpiar el meta para evitar datos huérfanos.
 */
add_action('set_object_terms', function($post_id, $terms, $tt_ids, $taxonomy) {
    if ($taxonomy !== 'mg_equipos') return;

    $primary_id = (int) get_post_meta($post_id, 'mg_primary_equipos_term', true);
    if (!$primary_id) return;

    // Si el término principal ya no está entre los asignados, limpiarlo
    if (!in_array($primary_id, (array) $terms)) {
        delete_post_meta($post_id, 'mg_primary_equipos_term');
    }

}, 10, 4);
