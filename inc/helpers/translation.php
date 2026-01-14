<?php
if (!defined('ABSPATH')) exit;

/**
 * Obtener el ID traducido de un post.
 */
if (!function_exists('mg_get_translated_post_id')) {
    function mg_get_translated_post_id($post_id) {
        if (function_exists('pll_get_post')) {
            return pll_get_post($post_id);
        }
        return $post_id;
    }
}

/**
 * Traducir múltiples IDs de post.
 */
if (!function_exists('mg_translate_post_ids')) {
    function mg_translate_post_ids($ids = []) {
        $translated = [];

        foreach ((array) $ids as $id) {
            $translated_id = mg_get_translated_post_id($id);
            if ($translated_id) {
                $translated[] = $translated_id;
            }
        }

        return $translated;
    }
}
