<?php
if (!defined('ABSPATH')) exit;

/**
 * =====================================================
 * AUTO RELATIONS (Language-safe)
 * =====================================================
 */

add_action('save_post', 'mg_handle_auto_relations', 20, 2);

function mg_handle_auto_relations($post_id, $post) {

    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;
    if (wp_is_post_revision($post_id)) return;

    if (!in_array($post->post_type, [
        'mg_portafolio',
        'mg_caso_exito'
    ])) return;

    // 🔒 NO cruzar idiomas
    if (function_exists('pll_get_post_language')) {
        $lang = pll_get_post_language($post_id);
        if (!$lang) return;
    }

    if ($post->post_type === 'mg_portafolio') {
        mg_sync_from_portafolio($post_id);
    }

    if ($post->post_type === 'mg_caso_exito') {
        mg_sync_from_caso_exito($post_id);
    }
}

/**
 * =====================================================
 * PORTAFOLIO → CLIENTE / CASO / EQUIPO
 * =====================================================
 */
function mg_sync_from_portafolio($portafolio_id) {

    $cliente   = get_post_meta($portafolio_id, 'mg_portafolio_cliente', true);
    $servicios = (array) get_post_meta($portafolio_id, 'mg_portafolio_servicio', true);
    $equipo    = (array) get_post_meta($portafolio_id, 'mg_portafolio_equipo', true);
    $caso      = get_post_meta($portafolio_id, 'mg_portafolio_caso_exito', true);

    // 1️⃣ Servicios → Cliente (AUTO)
    if ($cliente && $servicios) {
        $auto = (array) get_post_meta($cliente, '_mg_servicios_auto', true);
        $merged = array_unique(array_merge($auto, $servicios));
        update_post_meta($cliente, '_mg_servicios_auto', $merged);
    }

    // 2️⃣ Equipo → Caso de Éxito (AUTO)
    if ($caso && $equipo) {
        $auto = (array) get_post_meta($caso, '_mg_equipo_auto', true);
        $merged = array_unique(array_merge($auto, $equipo));
        update_post_meta($caso, '_mg_equipo_auto', $merged);
    }

    // 3️⃣ Portafolio → Equipo (AUTO)
    foreach ($equipo as $miembro) {
        $auto = (array) get_post_meta($miembro, '_mg_portafolios_auto', true);
        if (!in_array($portafolio_id, $auto)) {
            $auto[] = $portafolio_id;
            update_post_meta($miembro, '_mg_portafolios_auto', $auto);
        }
    }
}

/**
 * =====================================================
 * CASO DE ÉXITO → CLIENTE / EQUIPO
 * =====================================================
 */
function mg_sync_from_caso_exito($caso_id) {

    $cliente   = get_post_meta($caso_id, 'mg_caso_cliente', true);
    $servicios = (array) get_post_meta($caso_id, 'mg_caso_servicios', true);
    $equipo    = (array) get_post_meta($caso_id, 'mg_caso_equipo', true);

    // 1️⃣ Servicios → Cliente (AUTO)
    if ($cliente && $servicios) {
        $auto = (array) get_post_meta($cliente, '_mg_servicios_auto', true);
        $merged = array_unique(array_merge($auto, $servicios));
        update_post_meta($cliente, '_mg_servicios_auto', $merged);
    }

    // 2️⃣ Equipo → Caso (AUTO)
    if ($equipo) {
        $auto = (array) get_post_meta($caso_id, '_mg_equipo_auto', true);
        $merged = array_unique(array_merge($auto, $equipo));
        update_post_meta($caso_id, '_mg_equipo_auto', $merged);
    }
}
/**
 * =====================================================
 * AUTO AREA RELATIONS (Language-safe)
 * =====================================================
 *
 * Reglas:
 * - Área ↔ Miembros es bidireccional
 * - Director NO es bidireccional
 * - Servicios NO entran aquí
 * - Nunca cruzar idiomas
 */

add_action('save_post_mg_area', 'mg_sync_area_to_members', 20, 2);
add_action('save_post_mg_equipo', 'mg_sync_member_to_area', 20, 2);

/**
 * =====================================================
 * UTIL: verificar mismo idioma
 * =====================================================
 */
function mg_same_language($post_id_1, $post_id_2) {
    if (!function_exists('pll_get_post_language')) return true;
    return pll_get_post_language($post_id_1) === pll_get_post_language($post_id_2);
}

/**
 * =====================================================
 * 1️⃣ ÁREA → MIEMBROS
 * =====================================================
 * Al guardar un área:
 * - Si un miembro está en la lista → su campo mg_equipo_area debe apuntar a esta área
 * - Si fue removido → se limpia su campo mg_equipo_area
 */
function mg_sync_area_to_members($area_id, $post) {

    if ($post->post_type !== 'mg_area') return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $miembros_area = (array) get_post_meta($area_id, 'mg_area_miembros', true);

    // Obtener SOLO miembros del mismo idioma
    $lang = function_exists('pll_get_post_language') ? pll_get_post_language($area_id) : false;

    $args = [
        'post_type'   => 'mg_equipo',
        'numberposts' => -1,
        'post_status' => 'publish',
    ];

    if ($lang) {
        $args['lang'] = $lang;
    }

    $todos_miembros = get_posts($args);

    foreach ($todos_miembros as $persona) {
        $miembro_id = $persona->ID;

        // Seguridad idioma
        if (!mg_same_language($area_id, $miembro_id)) continue;

        $area_actual = get_post_meta($miembro_id, 'mg_equipo_area', true);
        $esta_en_area = in_array($miembro_id, $miembros_area);

        // ➕ Agregar relación
        if ($esta_en_area && $area_actual != $area_id) {
            update_post_meta($miembro_id, 'mg_equipo_area', $area_id);
        }

        // ➖ Quitar relación
        if (!$esta_en_area && $area_actual == $area_id) {
            delete_post_meta($miembro_id, 'mg_equipo_area');
        }
    }
}

/**
 * =====================================================
 * 2️⃣ MIEMBRO → ÁREA
 * =====================================================
 * Al guardar un miembro:
 * - Se agrega a la lista de miembros del área seleccionada
 * - Se elimina de cualquier otra área del mismo idioma
 */
function mg_sync_member_to_area($miembro_id, $post) {

    if ($post->post_type !== 'mg_equipo') return;
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

    $area_id = get_post_meta($miembro_id, 'mg_equipo_area', true);
    if (!$area_id) return;

    // Seguridad idioma
    if (!mg_same_language($miembro_id, $area_id)) return;

    // 1️⃣ Asegurar que esté en su área
    $miembros = (array) get_post_meta($area_id, 'mg_area_miembros', true);

    if (!in_array($miembro_id, $miembros)) {
        $miembros[] = $miembro_id;
        update_post_meta($area_id, 'mg_area_miembros', array_unique($miembros));
    }

    // 2️⃣ Eliminarlo de otras áreas (mismo idioma)
    $lang = function_exists('pll_get_post_language') ? pll_get_post_language($miembro_id) : false;

    $args = [
        'post_type'   => 'mg_area',
        'numberposts' => -1,
        'post_status' => 'publish',
        'exclude'     => [$area_id],
    ];

    if ($lang) {
        $args['lang'] = $lang;
    }

    $otras_areas = get_posts($args);

    foreach ($otras_areas as $otra_area) {
        if (!mg_same_language($miembro_id, $otra_area->ID)) continue;

        $lista = (array) get_post_meta($otra_area->ID, 'mg_area_miembros', true);

        if (in_array($miembro_id, $lista)) {
            $nueva_lista = array_diff($lista, [$miembro_id]);
            update_post_meta($otra_area->ID, 'mg_area_miembros', $nueva_lista);
        }
    }
}