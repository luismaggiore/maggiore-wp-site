<?php
if (!defined('ABSPATH')) exit;

/**
 * FUNCIONES HELPER PARA JERARQUÍA ORGANIZACIONAL
 * Sistema de validación de directores y estructura de reportes
 */

/**
 * Verifica si un miembro del equipo es director de algún área
 * 
 * @param int $equipo_id ID del post tipo mg_equipo
 * @return array|false Array de posts de áreas donde es director, o false si no es director
 */
function mg_is_director($equipo_id) {
    if (!$equipo_id) {
        return false;
    }
    
    $areas = get_posts([
        'post_type' => 'mg_area',
        'numberposts' => -1,
        'meta_query' => [[
            'key' => 'mg_area_director',
            'value' => $equipo_id,
            'compare' => '='
        ]]
    ]);
    
    return !empty($areas) ? $areas : false;
}

/**
 * Verifica si un miembro es director de un área específica
 * 
 * @param int $equipo_id ID del miembro del equipo
 * @param int $area_id ID del área
 * @return bool
 */
function mg_is_director_of_area($equipo_id, $area_id) {
    if (!$equipo_id || !$area_id) {
        return false;
    }
    
    $director_id = get_post_meta($area_id, 'mg_area_director', true);
    return $director_id == $equipo_id;
}

/**
 * Obtiene todos los subordinados directos de un director
 * (Personas que tienen a este director como jefe directo)
 * 
 * @param int $director_id ID del director
 * @return array Array de posts de subordinados
 */
function mg_get_subordinados($director_id) {
    if (!$director_id) {
        return [];
    }
    
    $subordinados = get_posts([
        'post_type' => 'mg_equipo',
        'numberposts' => -1,
        'meta_query' => [[
            'key' => 'mg_equipo_jefe_directo',
            'value' => $director_id,
            'compare' => '='
        ]]
    ]);
    
    return $subordinados;
}

/**
 * Obtiene la cadena de mando completa hacia arriba
 * (Del empleado hacia el CEO)
 * 
 * @param int $empleado_id ID del empleado
 * @return array Array de IDs de jefes en orden jerárquico
 */
function mg_get_cadena_mando($empleado_id) {
    if (!$empleado_id) {
        return [];
    }
    
    $cadena = [];
    $actual = $empleado_id;
    $max_depth = 10; // Prevenir loops infinitos
    $depth = 0;
    
    while ($depth < $max_depth) {
        $jefe = get_post_meta($actual, 'mg_equipo_jefe_directo', true);
        
        if (!$jefe || in_array($jefe, $cadena)) {
            // No hay más jefes o detectamos un ciclo
            break;
        }
        
        $cadena[] = $jefe;
        $actual = $jefe;
        $depth++;
    }
    
    return $cadena;
}

/**
 * Detecta ciclos jerárquicos antes de guardar
 * (Previene que A reporte a B si B ya reporta a A directa o indirectamente)
 * 
 * @param int $empleado_id ID del empleado
 * @param int $nuevo_jefe_id ID del nuevo jefe propuesto
 * @return bool True si existe un ciclo, false si es válido
 */
function mg_tiene_ciclo_jerarquico($empleado_id, $nuevo_jefe_id) {
    if (!$empleado_id || !$nuevo_jefe_id) {
        return false;
    }
    
    // Caso obvio: intentar ser su propio jefe
    if ($empleado_id === $nuevo_jefe_id) {
        return true;
    }
    
    // Verificar si el nuevo jefe reporta (directa o indirectamente) al empleado
    $cadena_del_jefe = mg_get_cadena_mando($nuevo_jefe_id);
    
    if (in_array($empleado_id, $cadena_del_jefe)) {
        // El nuevo jefe reporta al empleado, esto crearía un ciclo
        return true;
    }
    
    return false;
}

/**
 * Obtiene el número de personas bajo el mando de un director
 * (Subordinados directos + subordinados de subordinados)
 * 
 * @param int $director_id ID del director
 * @return int Número total de personas en su organización
 */
function mg_count_organizacion($director_id) {
    if (!$director_id) {
        return 0;
    }
    
    $subordinados_directos = mg_get_subordinados($director_id);
    $total = count($subordinados_directos);
    
    // Contar recursivamente los subordinados de cada subordinado
    foreach ($subordinados_directos as $subordinado) {
        $total += mg_count_organizacion($subordinado->ID);
    }
    
    return $total;
}

/**
 * Verifica si un empleado tiene permisos de manager
 * (Es director O tiene subordinados)
 * 
 * @param int $equipo_id ID del miembro del equipo
 * @return bool
 */
function mg_is_manager($equipo_id) {
    if (!$equipo_id) {
        return false;
    }
    
    // Es director?
    if (mg_is_director($equipo_id)) {
        return true;
    }
    
    // Tiene subordinados?
    $subordinados = mg_get_subordinados($equipo_id);
    return !empty($subordinados);
}

/**
 * Obtiene el organigrama completo desde un punto de inicio
 * 
 * @param int $root_id ID del nodo raíz (director o CEO)
 * @return array Estructura jerárquica del organigrama
 */
function mg_get_organigrama($root_id) {
    if (!$root_id) {
        return [];
    }
    
    $root = get_post($root_id);
    if (!$root) {
        return [];
    }
    
    $organigrama = [
        'id' => $root_id,
        'name' => $root->post_title,
        'cargo' => get_post_meta($root_id, 'mg_equipo_cargo', true),
        'email' => get_post_meta($root_id, 'mg_equipo_email', true),
        'is_director' => (bool) mg_is_director($root_id),
        'subordinados' => []
    ];
    
    $subordinados = mg_get_subordinados($root_id);
    
    foreach ($subordinados as $subordinado) {
        $organigrama['subordinados'][] = mg_get_organigrama($subordinado->ID);
    }
    
    return $organigrama;
}

/**
 * Obtiene el nivel jerárquico de un empleado
 * (0 = CEO/Director sin jefe, 1 = reporta a CEO, 2 = reporta a manager, etc.)
 * 
 * @param int $empleado_id ID del empleado
 * @return int Nivel jerárquico
 */
function mg_get_nivel_jerarquico($empleado_id) {
    if (!$empleado_id) {
        return -1;
    }
    
    $cadena = mg_get_cadena_mando($empleado_id);
    return count($cadena);
}

/**
 * Valida la coherencia de la asignación de área y jefe
 * 
 * @param int $empleado_id ID del empleado
 * @param int $area_id ID del área asignada
 * @param int $jefe_id ID del jefe directo
 * @return array Array con 'valid' (bool) y 'warnings' (array de mensajes)
 */
function mg_validate_hierarchy_assignment($empleado_id, $area_id, $jefe_id) {
    $validation = [
        'valid' => true,
        'warnings' => []
    ];
    
    // Si no hay jefe, es válido (puede ser CEO o director)
    if (!$jefe_id) {
        return $validation;
    }
    
    // El jefe debe existir
    $jefe = get_post($jefe_id);
    if (!$jefe) {
        $validation['valid'] = false;
        $validation['warnings'][] = __('El jefe seleccionado no existe.', 'maggiore');
        return $validation;
    }
    
    // Verificar ciclos
    if (mg_tiene_ciclo_jerarquico($empleado_id, $jefe_id)) {
        $validation['valid'] = false;
        $validation['warnings'][] = __('Esto crearía un ciclo jerárquico inválido.', 'maggiore');
        return $validation;
    }
    
    // Si hay área asignada, verificar coherencia
    if ($area_id) {
        $area_jefe = get_post_meta($jefe_id, 'mg_equipo_area', true);
        $es_director_de_mi_area = mg_is_director_of_area($jefe_id, $area_id);
        
        // El jefe debe estar en la misma área O ser director de esa área
        if ($area_jefe != $area_id && !$es_director_de_mi_area) {
            $validation['warnings'][] = sprintf(
                __('Advertencia: Tu jefe está en un área diferente (%s).', 'maggiore'),
                get_the_title($area_jefe)
            );
        }
    }
    
    // Si el empleado es director, advertir
    if (mg_is_director($empleado_id)) {
        $validation['warnings'][] = __('Eres director de un área. Los directores generalmente reportan al CEO o no tienen jefe directo.', 'maggiore');
    }
    
    return $validation;
}

/**
 * Obtiene TODOS los miembros de las áreas que gestiona un director
 * (No solo reportes directos, sino todo el equipo de sus áreas)
 * 
 * @param int $director_id ID del director
 * @return array Array de posts de equipo (sin duplicados)
 */
function mg_get_equipo_completo_director($director_id) {
    $areas_director = mg_is_director($director_id);
    
    if (!$areas_director) {
        return [];
    }
    
    $todos_miembros = [];
    $ids_procesados = []; // Array para rastrear IDs únicos
    
    foreach ($areas_director as $area) {
        // Obtener todos los miembros del área
        $miembros_ids = get_post_meta($area->ID, 'mg_area_miembros', true);
        
        if (is_array($miembros_ids)) {
            foreach ($miembros_ids as $miembro_id) {
                // Evitar incluir al director mismo y evitar duplicados
                if ($miembro_id != $director_id && !in_array($miembro_id, $ids_procesados)) {
                    $post = get_post($miembro_id);
                    
                    // Verificar que el post existe y es válido
                    if ($post && $post->post_type === 'mg_equipo' && $post->post_status === 'publish') {
                        $todos_miembros[] = $post;
                        $ids_procesados[] = $miembro_id;
                    }
                }
            }
        }
    }
    
    // Ordenar por nombre
    if (!empty($todos_miembros)) {
        usort($todos_miembros, function($a, $b) {
            return strcmp($a->post_title, $b->post_title);
        });
    }
    
    return $todos_miembros;
}
