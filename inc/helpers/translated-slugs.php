<?php
if (!defined('ABSPATH')) exit;

/**
 * =====================================================
 * SISTEMA DE TRADUCCIÓN DE SLUGS COMPATIBLE CON POLYLANG
 * =====================================================
 * Español (es) - Inglés (en) - Português (pt)
 * 
 * Este sistema traduce automáticamente los slugs de URLs
 * para Custom Post Types y términos de taxonomías
 */

/**
 * Configuración de slugs traducidos para CPTs
 */
function mg_get_translated_cpt_slugs() {
    return [
        'mg_servicio' => [
            'es' => 'servicios',
            'en' => 'services',
            'pt' => 'servicos'
        ],
        'mg_equipo' => [
            'es' => 'equipo',
            'en' => 'team',
            'pt' => 'equipe'
        ],
        'mg_cliente' => [
            'es' => 'clientes',
            'en' => 'clients',
            'pt' => 'clientes'
        ],
        'mg_caso_exito' => [
            'es' => 'casos-de-exito',
            'en' => 'case-studies',
            'pt' => 'casos-de-sucesso'
        ],
        'mg_portafolio' => [
            'es' => 'portafolio',
            'en' => 'portfolio',
            'pt' => 'portfolio'
        ],
        'mg_area' => [
            'es' => 'area',
            'en' => 'area',
            'pt' => 'area'
        ],
    ];
}

/**
 * Configuración de slugs traducidos para términos de taxonomías
 * Agregar aquí los términos que quieras traducir manualmente
 */
function mg_get_translated_term_slugs() {
    return [
        // CATEGORÍAS DE SERVICIOS (mg_categoria)
        'diseno' => [
            'es' => 'diseno',
            'en' => 'design',
            'pt' => 'design'
        ],
        'estrategia' => [
            'es' => 'estrategia',
            'en' => 'strategy',
            'pt' => 'estrategia'
        ],
        'desarrollo' => [
            'es' => 'desarrollo',
            'en' => 'development',
            'pt' => 'desenvolvimento'
        ],
        'marketing' => [
            'es' => 'marketing',
            'en' => 'marketing',
            'pt' => 'marketing'
        ],
        'consultoria' => [
            'es' => 'consultoria',
            'en' => 'consulting',
            'pt' => 'consultoria'
        ],
        
        // INDUSTRIAS (mg_industria)
        'tecnologia' => [
            'es' => 'tecnologia',
            'en' => 'technology',
            'pt' => 'tecnologia'
        ],
        'salud' => [
            'es' => 'salud',
            'en' => 'healthcare',
            'pt' => 'saude'
        ],
        'retail' => [
            'es' => 'retail',
            'en' => 'retail',
            'pt' => 'varejo'
        ],
        'educacion' => [
            'es' => 'educacion',
            'en' => 'education',
            'pt' => 'educacao'
        ],
        'finanzas' => [
            'es' => 'finanzas',
            'en' => 'finance',
            'pt' => 'financas'
        ],
        'alimentos-bebidas' => [
            'es' => 'alimentos-bebidas',
            'en' => 'food-beverage',
            'pt' => 'alimentos-bebidas'
        ],
        
        // EQUIPOS (mg_equipos)
        'liderazgo' => [
            'es' => 'liderazgo',
            'en' => 'leadership',
            'pt' => 'lideranca'
        ],
        'creativo' => [
            'es' => 'creativo',
            'en' => 'creative',
            'pt' => 'criativo'
        ],
    ];
}

/**
 * =====================================================
 * REGISTRAR REGLAS DE REWRITE TRADUCIDAS
 * =====================================================
 */
function mg_register_polylang_translated_slugs() {
    if (!function_exists('pll_languages_list')) {
        return;
    }
    
    $languages = pll_languages_list();
    $cpt_slugs = mg_get_translated_cpt_slugs();
    
    foreach ($languages as $lang) {
        // ========== SERVICIOS ==========
        $slug = isset($cpt_slugs['mg_servicio'][$lang]) ? $cpt_slugs['mg_servicio'][$lang] : 'servicios';
        
        // Archive: /en/services
        add_rewrite_rule(
            $lang . '/' . $slug . '/?$',
            'index.php?post_type=mg_servicio&lang=' . $lang,
            'top'
        );
        
        // Single con categoría: /en/services/design/brandbook
        add_rewrite_rule(
            $lang . '/' . $slug . '/([^/]+)/([^/]+)/?$',
            'index.php?mg_servicio=$matches[2]&lang=' . $lang,
            'top'
        );
        
        // Taxonomy archive: /en/services/design
        add_rewrite_rule(
            $lang . '/' . $slug . '/([^/]+)/?$',
            'index.php?mg_categoria=$matches[1]&post_type=mg_servicio&lang=' . $lang,
            'top'
        );
        
        // ========== EQUIPO ==========
        $slug = isset($cpt_slugs['mg_equipo'][$lang]) ? $cpt_slugs['mg_equipo'][$lang] : 'equipo';
        
        add_rewrite_rule(
            $lang . '/' . $slug . '/?$',
            'index.php?post_type=mg_equipo&lang=' . $lang,
            'top'
        );
        
        add_rewrite_rule(
            $lang . '/' . $slug . '/([^/]+)/([^/]+)/?$',
            'index.php?mg_equipo=$matches[2]&lang=' . $lang,
            'top'
        );
        
        // ========== CLIENTES ==========
        $slug = isset($cpt_slugs['mg_cliente'][$lang]) ? $cpt_slugs['mg_cliente'][$lang] : 'clientes';
        
        add_rewrite_rule(
            $lang . '/' . $slug . '/?$',
            'index.php?post_type=mg_cliente&lang=' . $lang,
            'top'
        );
        
        add_rewrite_rule(
            $lang . '/' . $slug . '/([^/]+)/([^/]+)/?$',
            'index.php?mg_cliente=$matches[2]&lang=' . $lang,
            'top'
        );
        
        // ========== CASOS DE ÉXITO ==========
        $slug = isset($cpt_slugs['mg_caso_exito'][$lang]) ? $cpt_slugs['mg_caso_exito'][$lang] : 'casos-de-exito';
        
        add_rewrite_rule(
            $lang . '/' . $slug . '/?$',
            'index.php?post_type=mg_caso_exito&lang=' . $lang,
            'top'
        );
        
        add_rewrite_rule(
            $lang . '/' . $slug . '/([^/]+)/([^/]+)/?$',
            'index.php?mg_caso_exito=$matches[2]&lang=' . $lang,
            'top'
        );
        
        // ========== PORTAFOLIO ==========
        $slug = isset($cpt_slugs['mg_portafolio'][$lang]) ? $cpt_slugs['mg_portafolio'][$lang] : 'portafolio';
        
        add_rewrite_rule(
            $lang . '/' . $slug . '/?$',
            'index.php?post_type=mg_portafolio&lang=' . $lang,
            'top'
        );
        
        add_rewrite_rule(
            $lang . '/' . $slug . '/proyecto/([^/]+)/?$',
            'index.php?mg_portafolio=$matches[1]&lang=' . $lang,
            'top'
        );
    }
}
add_action('init', 'mg_register_polylang_translated_slugs', 100);

/**
 * =====================================================
 * FILTROS PARA TRADUCIR PERMALINKS
 * =====================================================
 */

/**
 * Traducir archive links (cuando usas get_post_type_archive_link())
 */
function mg_translate_archive_link($link, $post_type) {
    if (!function_exists('pll_current_language')) {
        return $link;
    }
    
    $lang = pll_current_language();
    $cpt_slugs = mg_get_translated_cpt_slugs();
    
    if (isset($cpt_slugs[$post_type])) {
        $slug_es = $cpt_slugs[$post_type]['es'];
        $slug_translated = isset($cpt_slugs[$post_type][$lang]) ? $cpt_slugs[$post_type][$lang] : $slug_es;
        $link = str_replace('/' . $slug_es . '/', '/' . $slug_translated . '/', $link);
    }
    
    return $link;
}
add_filter('post_type_archive_link', 'mg_translate_archive_link', 10, 2);

/**
 * Traducir permalinks de posts individuales
 */
function mg_translate_post_permalink($permalink, $post) {
    if (!function_exists('pll_get_post_language')) {
        return $permalink;
    }
    
    $lang = pll_get_post_language($post->ID);
    
    // Solo traducir si NO es español
    if (!$lang || $lang === 'es') {
        return $permalink;
    }
    
    $post_type = get_post_type($post);
    $cpt_slugs = mg_get_translated_cpt_slugs();
    
    // Traducir slug del CPT
    if (isset($cpt_slugs[$post_type])) {
        $slug_es = $cpt_slugs[$post_type]['es'];
        $slug_translated = isset($cpt_slugs[$post_type][$lang]) ? $cpt_slugs[$post_type][$lang] : $slug_es;
        $permalink = str_replace('/' . $slug_es . '/', '/' . $slug_translated . '/', $permalink);
    }
    
    // Traducir slugs de términos en la URL
    $term_slugs = mg_get_translated_term_slugs();
    foreach ($term_slugs as $slug_es => $translations) {
        if (isset($translations[$lang])) {
            $slug_translated = $translations[$lang];
            $permalink = str_replace('/' . $slug_es . '/', '/' . $slug_translated . '/', $permalink);
        }
    }
    
    return $permalink;
}
add_filter('post_type_link', 'mg_translate_post_permalink', 10, 2);

/**
 * Traducir term links (taxonomías)
 */
function mg_translate_term_link($termlink, $term) {
    if (!function_exists('pll_current_language')) {
        return $termlink;
    }
    
    $lang = pll_current_language();
    
    if ($lang === 'es') {
        return $termlink;
    }
    
    // Traducir slug del término
    $term_slugs = mg_get_translated_term_slugs();
    if (isset($term_slugs[$term->slug][$lang])) {
        $slug_translated = $term_slugs[$term->slug][$lang];
        $termlink = str_replace('/' . $term->slug . '/', '/' . $slug_translated . '/', $termlink);
    }
    
    // Traducir base del CPT si es necesario
    $cpt_slugs = mg_get_translated_cpt_slugs();
    foreach ($cpt_slugs as $post_type => $translations) {
        $slug_es = $translations['es'];
        $slug_translated = isset($translations[$lang]) ? $translations[$lang] : $slug_es;
        $termlink = str_replace('/' . $slug_es . '/', '/' . $slug_translated . '/', $termlink);
    }
    
    return $termlink;
}
add_filter('term_link', 'mg_translate_term_link', 10, 2);

/**
 * =====================================================
 * FIX PARA LANGUAGE SWITCHER DE POLYLANG
 * =====================================================
 * Corrige las URLs del navegador de idiomas para que usen slugs traducidos
 */

/**
 * Filtrar URLs del language switcher (banderitas)
 * Ejemplo: /clientes → 🇺🇸 → /en/clients (no /en/clientes)
 * Y también: /en/services → 🇨🇱 → /servicios (no /services)
 */
function mg_fix_polylang_language_switcher_urls($url, $lang, $slug = null) {
    // Solo aplicar si es un archive o single de nuestros CPTs
    if (!is_post_type_archive() && !is_singular()) {
        return $url;
    }
    
    $cpt_slugs = mg_get_translated_cpt_slugs();
    $term_slugs = mg_get_translated_term_slugs();
    
    // Traducir slugs de CPTs
    foreach ($cpt_slugs as $post_type => $translations) {
        // Buscar qué slug está en la URL actual
        foreach ($translations as $current_lang => $current_slug) {
            if (strpos($url, '/' . $current_slug . '/') !== false || strpos($url, '/' . $current_slug) !== false) {
                // Obtener el slug del idioma de destino
                $slug_translated = isset($translations[$lang]) ? $translations[$lang] : $translations['es'];
                
                // Solo reemplazar si son diferentes
                if ($current_slug !== $slug_translated) {
                    $url = str_replace('/' . $current_slug . '/', '/' . $slug_translated . '/', $url);
                    $url = str_replace('/' . $current_slug, '/' . $slug_translated, $url);
                }
                break;
            }
        }
    }
    
    // Traducir slugs de términos de taxonomía
    foreach ($term_slugs as $term_id => $translations) {
        // Buscar qué slug está en la URL actual
        foreach ($translations as $current_lang => $current_slug) {
            if (strpos($url, '/' . $current_slug . '/') !== false || strpos($url, '/' . $current_slug) !== false) {
                // Obtener el slug del idioma de destino
                $slug_translated = isset($translations[$lang]) ? $translations[$lang] : $translations['es'];
                
                // Solo reemplazar si son diferentes
                if ($current_slug !== $slug_translated) {
                    $url = str_replace('/' . $current_slug . '/', '/' . $slug_translated . '/', $url);
                    $url = str_replace('/' . $current_slug, '/' . $slug_translated, $url);
                }
                break;
            }
        }
    }
    
    return $url;
}
add_filter('pll_translation_url', 'mg_fix_polylang_language_switcher_urls', 10, 2);

/**
 * Filtrar el array completo de idiomas del switcher
 * Para cuando uses pll_the_languages() en tu template
 */
function mg_fix_language_switcher_items($languages) {
    // Validar que sea un array antes de procesar
    if (!is_array($languages)) {
        return $languages;
    }
    
    if (!is_post_type_archive() && !is_singular()) {
        return $languages;
    }
    
    $cpt_slugs = mg_get_translated_cpt_slugs();
    $term_slugs = mg_get_translated_term_slugs();
    
    foreach ($languages as $lang_code => &$language) {
        if (isset($language['url'])) {
            $url = $language['url'];
            
            // Traducir slugs de CPTs (bidireccional)
            foreach ($cpt_slugs as $post_type => $translations) {
                // Buscar qué slug está en la URL actual
                foreach ($translations as $current_lang => $current_slug) {
                    if (strpos($url, '/' . $current_slug . '/') !== false || strpos($url, '/' . $current_slug) !== false) {
                        // Obtener el slug del idioma de destino
                        $slug_translated = isset($translations[$lang_code]) ? $translations[$lang_code] : $translations['es'];
                        
                        // Solo reemplazar si son diferentes
                        if ($current_slug !== $slug_translated) {
                            $url = str_replace('/' . $current_slug . '/', '/' . $slug_translated . '/', $url);
                            $url = str_replace('/' . $current_slug, '/' . $slug_translated, $url);
                        }
                        break;
                    }
                }
            }
            
            // Traducir slugs de términos (bidireccional)
            foreach ($term_slugs as $term_id => $translations) {
                // Buscar qué slug está en la URL actual
                foreach ($translations as $current_lang => $current_slug) {
                    if (strpos($url, '/' . $current_slug . '/') !== false || strpos($url, '/' . $current_slug) !== false) {
                        // Obtener el slug del idioma de destino
                        $slug_translated = isset($translations[$lang_code]) ? $translations[$lang_code] : $translations['es'];
                        
                        // Solo reemplazar si son diferentes
                        if ($current_slug !== $slug_translated) {
                            $url = str_replace('/' . $current_slug . '/', '/' . $slug_translated . '/', $url);
                            $url = str_replace('/' . $current_slug, '/' . $slug_translated, $url);
                        }
                        break;
                    }
                }
            }
            
            $language['url'] = $url;
        }
    }
    
    return $languages;
}
add_filter('pll_the_languages', 'mg_fix_language_switcher_items', 10, 1);
