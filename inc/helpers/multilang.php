<?php
if (!defined('ABSPATH')) exit;

/**
 * =====================================================
 * MULTILANGUAGE HELPERS (Polylang Free)
 * =====================================================
 */

/**
 * Devuelve el ID del post traducido al idioma actual.
 * Si no existe traducción, devuelve el ID original.
 */
function mg_translate_post_id($post_id) {
    if (!$post_id) return 0;

    if (function_exists('pll_get_post')) {
        $translated = pll_get_post($post_id, pll_current_language());
        return $translated ? $translated : $post_id;
    }

    return $post_id;
}

/**
 * Traduce un array de IDs (servicios, equipo, etc.)
 */
function mg_translate_post_ids(array $ids): array {
    $translated = [];

    foreach ($ids as $id) {
        $t = mg_translate_post_id($id);
        if ($t) $translated[] = $t;
    }

    return array_unique($translated);
}

/**
 * Devuelve el idioma actual de Polylang o 'es' por defecto
 */
function mg_current_lang(): string {
    return function_exists('pll_current_language')
        ? pll_current_language()
        : 'es';
}

/**
 * Devuelve TRUE si el post actual NO es el idioma principal
 */
function mg_is_translation($post_id): bool {
    if (!function_exists('pll_get_post_language')) return false;
    return pll_get_post_language($post_id) !== 'es';
}
