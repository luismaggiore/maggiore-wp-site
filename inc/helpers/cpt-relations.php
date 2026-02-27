<?php
if (!defined('ABSPATH')) exit;

/**
 * Obtener todos los clientes como array para dropdown.
 */
function mg_get_clientes_options() {
    $clientes = get_posts([
        'post_type' => 'mg_cliente',
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $options = [];
    foreach ($clientes as $cliente) {
        $options[$cliente->ID] = $cliente->post_title;
    }
    return $options;
}

/**
 * Obtener todos los servicios como array para dropdown.
 */
function mg_get_servicios_options() {
    $servicios = get_posts([
        'post_type' => 'mg_servicio',
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $options = [];
    foreach ($servicios as $servicio) {
        $options[$servicio->ID] = $servicio->post_title;
    }
    return $options;
}

/**
 * Obtener todos los casos de éxito como array para dropdown.
 */
function mg_get_casos_exito_options() {
    $casos = get_posts([
        'post_type' => 'mg_caso_exito',
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $options = [];
    foreach ($casos as $caso) {
        $options[$caso->ID] = $caso->post_title;
    }
    return $options;
}

/**
 * Obtener todos los miembros del equipo como array para dropdown múltiple.
 */
function mg_get_equipo_options() {
    $miembros = get_posts([
        'post_type' => 'mg_equipo',
        'numberposts' => -1,
        'post_status' => 'publish',
        'orderby' => 'title',
        'order' => 'ASC',
    ]);

    $options = [];
    foreach ($miembros as $persona) {
        $options[$persona->ID] = $persona->post_title;
    }
    return $options;
}

/**
 * Obtener IDs de portafolios relacionados a un cliente
 */
function mg_get_portafolios_by_cliente($cliente_id) {
    if (!$cliente_id) return [];

    $query = get_posts([
        'post_type'   => 'mg_portafolio',
        'post_status' => 'publish',
        'numberposts' => -1,
        'fields'      => 'ids',
        'meta_query'  => [
            [
                'key'   => 'mg_portafolio_cliente',
                'value' => $cliente_id,
            ]
        ]
    ]);

    return $query;
}

/**
 * Obtener IDs de portafolios relacionados a un caso de éxito
 */
function mg_get_portafolios_by_caso_exito($caso_id) {
    if (!$caso_id) return [];

    $query = get_posts([
        'post_type'   => 'mg_portafolio',
        'post_status' => 'publish',
        'numberposts' => -1,
        'fields'      => 'ids',
        'meta_query'  => [
            [
                'key'   => 'mg_portafolio_caso_exito',
                'value' => $caso_id,
            ]
        ]
    ]);

    return $query;
}

/**
 * Obtener IDs de portafolios donde participó un miembro del equipo
 */
function mg_get_portafolios_by_miembro($miembro_id) {
    if (!$miembro_id) return [];

    $query = get_posts([
        'post_type'   => 'mg_portafolio',
        'post_status' => 'publish',
        'numberposts' => -1,
        'fields'      => 'ids',
        'meta_query'  => [
            [
                'key'     => 'mg_portafolio_equipo',
                'value'   => '"' . $miembro_id . '"',
                'compare' => 'LIKE'
            ]
        ]
    ]);

    return $query;
}

function mg_get_indexable_clients_meta_query() {
    return [
        'relation' => 'OR',
        [
            'key'     => 'mg_cliente_no_indexar',
            'compare' => 'NOT EXISTS',
        ],
        [
            'key'     => 'mg_cliente_no_indexar',
            'value'   => '1',
            'compare' => '!=',
        ],
    ];
}


function mg_is_client_indexable($client_id) {
    $no_indexar = get_post_meta($client_id, 'mg_cliente_no_indexar', true);
    return $no_indexar !== '1';
}