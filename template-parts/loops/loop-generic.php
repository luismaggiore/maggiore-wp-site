<?php
/**
 * Loop Genérico - Detecta automáticamente el tipo de post y usa la card correspondiente
 * 
 * Uso:
 * get_template_part('template-parts/loops/loop', 'generic');
 * 
 * O con query personalizada:
 * set_query_var('custom_query', $mi_query);
 * get_template_part('template-parts/loops/loop', 'generic');
 */

if (!defined('ABSPATH')) exit;

// Usar query personalizada si existe, si no usar la principal
$query = get_query_var('custom_query') ?: $GLOBALS['wp_query'];

if ($query->have_posts()) :
    while ($query->have_posts()) : $query->the_post();
        
        // Detectar el tipo de post y usar la card correspondiente
        $post_type = get_post_type();
        
        switch ($post_type) {
            case 'mg_servicio':
                get_template_part('template-parts/card', 'servicio');
                break;
            
            case 'mg_cliente':
                get_template_part('template-parts/card', 'cliente');
                break;
            
            case 'mg_caso_exito':
                get_template_part('template-parts/card', 'caso-exito');
                break;
            
            case 'mg_portafolio':
                get_template_part('template-parts/card', 'portafolio');
                break;
            
            case 'mg_equipo':
                get_template_part('template-parts/card', 'equipo');
                break;
            
            case 'mg_area':
                get_template_part('template-parts/card', 'area');
                break;
            
            case 'post':
                get_template_part('template-parts/card', 'articulo');
                break;
            
            default:
                get_template_part('template-parts/content');
                break;
        }
        
    endwhile;
    
    // Reset si es query personalizada
    if (get_query_var('custom_query')) {
        wp_reset_postdata();
    }
    
else ;
    get_template_part('template-parts/content', 'none');
endif;
