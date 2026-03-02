<?php
if (!defined('ABSPATH')) exit;

/**
 * Helper: Clientes
 * Fuente única de verdad para tramos de ventas y tamaños de empresa.
 * Usar en metaboxes, singles, archives y template parts.
 */

/**
 * Retorna el array completo de tramos de ventas.
 * Cada tramo tiene: nombre (para front), rango (texto visible) y codigo.
 *
 * Para cambiar un nombre o rango, editarlo aquí y se propaga a todo el sitio.
 */
function mg_get_tramos(): array {
    return [
        '01' => [
            'nombre' => __('Sin ventas', 'maggiore'),
            'rango'  => '',
        ],
        '02' => [
            'nombre' => __('Micro', 'maggiore'),
            'rango'  => 'US$0 – $10k',
        ],
        '03' => [
            'nombre' => __('Micro', 'maggiore'),
            'rango'  => 'US$10k – $30k',
        ],
        '04' => [
            'nombre' => __('Micro', 'maggiore'),
            'rango'  => 'US$30k – $110k',
        ],
        '05' => [
            'nombre' => __('Pequeña', 'maggiore'),
            'rango'  => 'US$110k – $230k',
        ],
        '06' => [
            'nombre' => __('Pequeña', 'maggiore'),
            'rango'  => 'US$230k – $460k',
        ],
        '07' => [
            'nombre' => __('Pequeña', 'maggiore'),
            'rango'  => 'US$460k – $1.2M',
        ],
        '08' => [
            'nombre' => __('Mediana', 'maggiore'),
            'rango'  => 'US$1.2M – $2.3M',
        ],
        '09' => [
            'nombre' => __('Mediana', 'maggiore'),
            'rango'  => 'US$2.3M – $4.6M',
        ],
        '10' => [
            'nombre' => __('Grande', 'maggiore'),
            'rango'  => 'US$4.6M – $9.2M',
        ],
        '11' => [
            'nombre' => __('Grande', 'maggiore'),
            'rango'  => 'US$9.2M – $28M',
        ],
        '12' => [
            'nombre' => __('Grande', 'maggiore'),
            'rango'  => 'US$28M – $46M',
        ],
        '13' => [
            'nombre' => __('Gigante', 'maggiore'),
            'rango'  => '+US$46M',
        ],
    ];
}

/**
 * Retorna los datos de tamaño de un cliente dado su post ID.
 *
 * @param int $post_id
 * @return array|null  ['codigo' => '09', 'nombre' => 'Mediana', 'rango' => 'US$2.3M – $4.6M']
 *                     null si no tiene tramo asignado
 */
function mg_get_tamano_cliente(int $post_id): ?array {
    $codigo = get_post_meta($post_id, 'mg_cliente_tamano', true);

    if (!$codigo) return null;

    $tramos = mg_get_tramos();

    if (!isset($tramos[$codigo])) return null;

    return array_merge(['codigo' => $codigo], $tramos[$codigo]);
}
