<?php
if (!defined('ABSPATH')) exit;

/**
 * Sistema de SEO Mejorado con Schema Automático Contextual
 * Genera Schema.org basado en las relaciones entre CPTs para validar expertise
 * 
 * @version 3.0 - Con todas las mejoras incorporadas
 * @author Maggiore Theme
 * 
 * CHANGELOG v3.0:
 * - Nuevo schema para Área (mg_area)
 * - Mejoras en Caso de Éxito: portafolios relacionados (hasPart), URL externa
 * - Mejoras en Cliente: servicios contratados, portafolios, fechas contractuales
 * - Mejoras en Servicio: área como provider, precio/ofertas, beneficios
 * - Mejoras en Portafolio: vínculo a caso de éxito (isPartOf), videos, galería
 * - Mejoras en Equipo: taxonomía mg_equipos como memberOf
 */

/**
 * Generar Schema.org automático según tipo de contenido
 */
function mg_generate_auto_schema($post_id) {
    $post_type = get_post_type($post_id);
    
    switch ($post_type) {
        case 'post':
            return mg_schema_blog_post($post_id);
        case 'mg_equipo':
            return mg_schema_person($post_id);
        case 'mg_caso_exito':
            return mg_schema_case_study($post_id);
        case 'mg_cliente':
            return mg_schema_organization($post_id);
        case 'mg_servicio':
            return mg_schema_service($post_id);
        case 'mg_portafolio':
            return mg_schema_creative_work($post_id);
        case 'mg_area':
            return mg_schema_area($post_id);
        default:
            return mg_schema_webpage($post_id);
    }
}

/**
 * Schema para Blog Post (con autor verificable)
 */
function mg_schema_blog_post($post_id) {
    $post = get_post($post_id);
    $autor_id = get_post_meta($post_id, 'mg_blog_autor', true);
    
    // Datos del autor
    $autor_nombre = $autor_id ? get_the_title($autor_id) : get_bloginfo('name');
    $autor_cargo = $autor_id ? get_post_meta($autor_id, 'mg_equipo_cargo', true) : '';
    $autor_foto = $autor_id ? get_the_post_thumbnail_url($autor_id, 'thumbnail') : '';
    $autor_bio = $autor_id ? get_post_meta($autor_id, 'mg_equipo_bio', true) : '';
    $autor_url = $autor_id ? get_permalink($autor_id) : home_url();
    $autor_linkedin = $autor_id ? get_post_meta($autor_id, 'mg_equipo_linkedin', true) : '';
    
    // NUEVO: Casos Y portafolios del autor
    $work_examples = [];
    
    if ($autor_id) {
        // Casos de éxito
        $casos = get_posts([
            'post_type' => 'mg_caso_exito',
            'numberposts' => 5,
            'meta_query' => [[
                'key' => '_mg_equipo_auto',
                'value' => $autor_id,
                'compare' => 'LIKE'
            ]]
        ]);
        
        foreach ($casos as $caso) {
            $caso_cliente_id = get_post_meta($caso->ID, 'mg_caso_cliente', true);
            
            $caso_item = [
                '@type' => 'Article',
                'additionalType' => 'https://schema.org/CaseStudy',
                '@id' => get_permalink($caso->ID) . '#case-study',
                'name' => $caso->post_title,
                'url' => get_permalink($caso->ID),
                'description' => get_the_excerpt($caso->ID)
            ];
            
            // Agregar cliente si existe
            if ($caso_cliente_id) {
                $cliente_linkedin = get_post_meta($caso_cliente_id, 'mg_cliente_linkedin', true);
                $caso_item['client'] = [
                    '@type' => 'Organization',
                    '@id' => get_permalink($caso_cliente_id) . '#organization',
                    'name' => get_the_title($caso_cliente_id),
                    'url' => get_permalink($caso_cliente_id)
                ];
                
                if ($cliente_linkedin) {
                    $caso_item['client']['sameAs'] = [$cliente_linkedin];
                }
            }
            
            $work_examples[] = $caso_item;
        }
        
        // Portafolios
        $portafolios_ids = get_post_meta($autor_id, '_mg_portafolios_auto', true) ?: [];
        if (!empty($portafolios_ids)) {
            $portafolios = get_posts([
                'post_type' => 'mg_portafolio',
                'post__in' => array_slice($portafolios_ids, 0, 5),
                'numberposts' => 5
            ]);
            
            foreach ($portafolios as $portafolio) {
                $portafolio_cliente_id = get_post_meta($portafolio->ID, 'mg_portafolio_cliente', true);
                
                $portafolio_item = [
                    '@type' => 'CreativeWork',
                    '@id' => get_permalink($portafolio->ID) . '#creative-work',
                    'name' => $portafolio->post_title,
                    'url' => get_permalink($portafolio->ID)
                ];
                
                // Agregar cliente si existe
                if ($portafolio_cliente_id) {
                    $cliente_linkedin = get_post_meta($portafolio_cliente_id, 'mg_cliente_linkedin', true);
                    $portafolio_item['client'] = [
                        '@type' => 'Organization',
                        '@id' => get_permalink($portafolio_cliente_id) . '#organization',
                        'name' => get_the_title($portafolio_cliente_id),
                        'url' => get_permalink($portafolio_cliente_id)
                    ];
                    
                    if ($cliente_linkedin) {
                        $portafolio_item['client']['sameAs'] = [$cliente_linkedin];
                    }
                }
                
                $work_examples[] = $portafolio_item;
            }
        }
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        '@id' => get_permalink($post_id) . '#article',
        'headline' => get_the_title($post_id),
        'description' => get_the_excerpt($post_id),
        'image' => get_the_post_thumbnail_url($post_id, 'large'),
        'datePublished' => get_the_date('c', $post_id),
        'dateModified' => get_the_modified_date('c', $post_id),
        'author' => [
            '@type' => 'Person',
            '@id' => $autor_url . '#person',
            'name' => $autor_nombre,
            'jobTitle' => $autor_cargo,
            'image' => $autor_foto,
            'description' => $autor_bio,
            'url' => $autor_url,
            'sameAs' => $autor_linkedin ? [$autor_linkedin] : [],
            'workExample' => $work_examples,
            'worksFor' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url()
            ]
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url(),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => get_theme_file_uri('assets/images/logo.png')
            ]
        ]
    ];
    
    return $schema;
}

/**
 * Schema para Miembro del Equipo (Person con credenciales)
 * Diferencia entre directores (con validación organizacional) y empleados regulares
 */
function mg_schema_person($post_id) {
    $cargo = get_post_meta($post_id, 'mg_equipo_cargo', true);
    $bio = get_post_meta($post_id, 'mg_equipo_bio', true);
    $linkedin = get_post_meta($post_id, 'mg_equipo_linkedin', true);
    $area_id = get_post_meta($post_id, 'mg_equipo_area', true);
    
    // NUEVOS CAMPOS
    $email = get_post_meta($post_id, 'mg_equipo_email', true);
    $jefe_directo = get_post_meta($post_id, 'mg_equipo_jefe_directo', true);
    
    // Especialidades del nuevo metabox
    $especialidades = get_post_meta($post_id, 'mg_equipo_especialidades', true) ?: [];
    
    // Casos Y portafolios
    $work_examples = [];
    
    // Casos de éxito
    $casos = get_posts([
        'post_type' => 'mg_caso_exito',
        'numberposts' => -1,
        'meta_query' => [[
            'key' => '_mg_equipo_auto',
            'value' => $post_id,
            'compare' => 'LIKE'
        ]]
    ]);
    
    foreach ($casos as $caso) {
        $cliente_id = get_post_meta($caso->ID, 'mg_caso_cliente', true);
        $caso_item = [
            '@type' => 'Article',
            'additionalType' => 'https://schema.org/CaseStudy',
            '@id' => get_permalink($caso->ID) . '#case-study',
            'name' => $caso->post_title,
            'url' => get_permalink($caso->ID),
            'description' => get_the_excerpt($caso->ID)
        ];
        
        if ($cliente_id) {
            $cliente_linkedin = get_post_meta($cliente_id, 'mg_cliente_linkedin', true);
            $caso_item['client'] = [
                '@type' => 'Organization',
                '@id' => get_permalink($cliente_id) . '#organization',
                'name' => get_the_title($cliente_id),
                'url' => get_permalink($cliente_id)
            ];
            
            // Agregar sameAs si tiene LinkedIn
            if ($cliente_linkedin) {
                $caso_item['client']['sameAs'] = [$cliente_linkedin];
            }
        }
        
        $work_examples[] = $caso_item;
    }
    
    // Portafolios
    $portafolios_ids = get_post_meta($post_id, '_mg_portafolios_auto', true) ?: [];
    if (!empty($portafolios_ids)) {
        $portafolios = get_posts([
            'post_type' => 'mg_portafolio',
            'post__in' => $portafolios_ids,
            'numberposts' => -1
        ]);
        
        foreach ($portafolios as $portafolio) {
            $portafolio_cliente_id = get_post_meta($portafolio->ID, 'mg_portafolio_cliente', true);
            
            $work_item = [
                '@type' => 'CreativeWork',
                '@id' => get_permalink($portafolio->ID) . '#creative-work',
                'name' => $portafolio->post_title,
                'url' => get_permalink($portafolio->ID)
            ];
            
            // Agregar cliente si existe
            if ($portafolio_cliente_id) {
                $cliente_linkedin = get_post_meta($portafolio_cliente_id, 'mg_cliente_linkedin', true);
                $work_item['client'] = [
                    '@type' => 'Organization',
                    '@id' => get_permalink($portafolio_cliente_id) . '#organization',
                    'name' => get_the_title($portafolio_cliente_id),
                    'url' => get_permalink($portafolio_cliente_id)
                ];
                
                // Agregar sameAs si tiene LinkedIn
                if ($cliente_linkedin) {
                    $work_item['client']['sameAs'] = [$cliente_linkedin];
                }
            }
            
            $work_examples[] = $work_item;
        }
    }
    
    // Artículos escritos
    $articulos = get_posts([
        'post_type' => 'post',
        'numberposts' => -1,
        'meta_query' => [[
            'key' => 'mg_blog_autor',
            'value' => $post_id
        ]]
    ]);
    
    $articles = [];
    foreach ($articulos as $articulo) {
        $articles[] = [
            '@type' => 'Article',
            '@id' => get_permalink($articulo->ID) . '#article',
            'headline' => $articulo->post_title,
            'url' => get_permalink($articulo->ID)
        ];
    }
    
    // SCHEMA BASE (todos los miembros tienen esto)
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Person',
        '@id' => get_permalink($post_id) . '#person',
        'name' => get_the_title($post_id),
        'jobTitle' => $cargo,
        'description' => $bio,
        'image' => get_the_post_thumbnail_url($post_id, 'large'),
        'url' => get_permalink($post_id),
        'sameAs' => $linkedin ? [$linkedin] : [],
        'email' => $email,
        'worksFor' => [
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url()
        ],
        'workExample' => $work_examples,
        'author' => $articles,
        'knowsAbout' => $especialidades
    ];
    
    // MEJORA v3.0: Agregar taxonomía de equipo (subárea/categoría)
    $equipos = get_the_terms($post_id, 'mg_equipos');
    
    // DETECTAR SI ES DIRECTOR
    $areas_director = mg_is_director($post_id);
    
    if ($areas_director) {
        // ===== SCHEMA ADICIONAL PARA DIRECTORES =====
        
        // 1. memberOf - Departamento(s) que lidera
        $member_of = [];
        foreach ($areas_director as $area) {
            $area_descripcion = get_post_meta($area->ID, 'mg_area_descripcion', true);
            $member_of[] = [
                '@type' => 'Organization',
                '@id' => get_permalink($area->ID) . '#department',
                'name' => $area->post_title,
                'url' => get_permalink($area->ID),
                'description' => $area_descripcion
            ];
        }
        
        // Agregar taxonomía de equipo si existe
        if ($equipos && !is_wp_error($equipos)) {
            $member_of[] = [
                '@type' => 'Organization',
                'name' => $equipos[0]->name,
                'description' => $equipos[0]->description,
                'parentOrganization' => [
                    '@type' => 'Organization',
                    'name' => get_bloginfo('name'),
                    'url' => home_url()
                ]
            ];
        }
        
        $schema['memberOf'] = $member_of;
        
        // 2. hasOccupation - Rol de director con categoría de management
        $schema['hasOccupation'] = [
            '@type' => 'Role',
            'roleName' => 'Director',
            'hasOccupationalCategory' => [
                '@type' => 'CategoryCode',
                'name' => 'Management Occupations',
                'codeValue' => '11-0000' // Standard Occupational Classification code
            ]
        ];
        
        // 3. employee - Personas que reportan directamente a este director
        $subordinados = mg_get_subordinados($post_id);
        if (!empty($subordinados)) {
            $employees = [];
            foreach ($subordinados as $sub) {
                $sub_cargo = get_post_meta($sub->ID, 'mg_equipo_cargo', true);
                $employees[] = [
                    '@type' => 'Person',
                    '@id' => get_permalink($sub->ID) . '#person',
                    'name' => $sub->post_title,
                    'jobTitle' => $sub_cargo,
                    'url' => get_permalink($sub->ID)
                ];
            }
            $schema['employee'] = $employees;
        }
        
    } else {
        // ===== SCHEMA PARA EMPLEADOS REGULARES =====
        
        // Si tiene jefe directo, agregarlo como colleague
        if ($jefe_directo) {
            $jefe_cargo = get_post_meta($jefe_directo, 'mg_equipo_cargo', true);
            $schema['colleague'] = [[
                '@type' => 'Person',
                '@id' => get_permalink($jefe_directo) . '#person',
                'name' => get_the_title($jefe_directo),
                'jobTitle' => $jefe_cargo,
                'url' => get_permalink($jefe_directo)
            ]];
        }
        
        // Si tiene área asignada, agregarla como departamento en worksFor
        if ($area_id) {
            $area_nombre = get_the_title($area_id);
            $schema['worksFor']['department'] = [
                '@type' => 'Organization',
                'name' => $area_nombre,
                'url' => get_permalink($area_id)
            ];
        }
        
        // Agregar taxonomía de equipo como memberOf
        if ($equipos && !is_wp_error($equipos)) {
            $schema['memberOf'] = [
                '@type' => 'Organization',
                'name' => $equipos[0]->name,
                'description' => $equipos[0]->description,
                'parentOrganization' => [
                    '@type' => 'Organization',
                    'name' => get_bloginfo('name'),
                    'url' => home_url()
                ]
            ];
        }
    }
    
    return $schema;
}

/**
 * Schema para Caso de Éxito (con validación de cliente y equipo)
 * v3.0: Agrega portafolios relacionados y URL externa
 */
function mg_schema_case_study($post_id) {
    $cliente_id = get_post_meta($post_id, 'mg_caso_cliente', true);
    $servicios_ids = get_post_meta($post_id, 'mg_caso_servicios', true) ?: [];
    $equipo_ids = get_post_meta($post_id, 'mg_caso_equipo', true) ?: [];
    $contexto = get_post_meta($post_id, 'mg_caso_contexto', true);
    $acciones = get_post_meta($post_id, 'mg_caso_acciones', true);
    $resultados = get_post_meta($post_id, 'mg_caso_resultados', true);
    $fecha = get_post_meta($post_id, 'mg_caso_fecha', true);
    
    // Contratador (persona real que valida)
    $contratador_nombre = get_post_meta($post_id, 'mg_caso_contratador_nombre', true);
    $contratador_cargo = get_post_meta($post_id, 'mg_caso_contratador_cargo', true);
    $contratador_linkedin = get_post_meta($post_id, 'mg_caso_contratador_linkedin', true);
    $contratador_cita = get_post_meta($post_id, 'mg_caso_contratador_cita', true);
    
    // Equipo participante
    $contributors = [];
    foreach ($equipo_ids as $miembro_id) {
        $contributors[] = [
            '@type' => 'Person',
            '@id' => get_permalink($miembro_id) . '#person',
            'name' => get_the_title($miembro_id),
            'jobTitle' => get_post_meta($miembro_id, 'mg_equipo_cargo', true),
            'url' => get_permalink($miembro_id)
        ];
    }
    
    // Servicios aplicados (MENTIONS)
    $services_mentioned = [];
    foreach ($servicios_ids as $servicio_id) {
        $services_mentioned[] = [
            '@type' => 'Service',
            '@id' => get_permalink($servicio_id) . '#service',
            'name' => get_the_title($servicio_id),
            'url' => get_permalink($servicio_id)
        ];
    }
    
    // Texto completo
    $texto_completo = '';
    if ($contexto) $texto_completo .= "Contexto: $contexto\n\n";
    if ($acciones) $texto_completo .= "Acciones: $acciones\n\n";
    if ($resultados) $texto_completo .= "Resultados: $resultados";
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Article',
        'additionalType' => 'https://schema.org/CaseStudy',
        '@id' => get_permalink($post_id) . '#case-study',
        'headline' => get_the_title($post_id),
        'description' => get_the_excerpt($post_id),
        'image' => get_the_post_thumbnail_url($post_id, 'large'),
        'datePublished' => $fecha ? $fecha . '-01' : get_the_date('c', $post_id),
        'dateModified' => get_the_modified_date('c', $post_id),
        'author' => [
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url()
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url(),
            'logo' => [
                '@type' => 'ImageObject',
                'url' => get_theme_file_uri('assets/images/logo.png')
            ]
        ],
        'about' => $cliente_id ? [
            '@type' => 'Organization',
            '@id' => get_permalink($cliente_id) . '#organization',
            'name' => get_the_title($cliente_id),
            'url' => get_permalink($cliente_id)
        ] : null,
        'contributor' => $contributors,
        'mentions' => $services_mentioned,
        'text' => $texto_completo
    ];
    
    // Agregar review si existe
    if ($contratador_nombre && $contratador_cita) {
        $schema['review'] = [
            '@type' => 'Review',
            'author' => [
                '@type' => 'Person',
                'name' => $contratador_nombre,
                'jobTitle' => $contratador_cargo,
                'sameAs' => $contratador_linkedin
            ],
            'reviewBody' => $contratador_cita
        ];
    }
    
    // MEJORA v3.0: Agregar portafolios relacionados como partes del caso
    $portafolios = get_posts([
        'post_type' => 'mg_portafolio',
        'numberposts' => -1,
        'meta_query' => [[
            'key' => 'mg_portafolio_caso_exito',
            'value' => $post_id
        ]]
    ]);
    
    if (!empty($portafolios)) {
        $has_parts = [];
        foreach ($portafolios as $port) {
            $port_cliente_id = get_post_meta($port->ID, 'mg_portafolio_cliente', true);
            
            $part_item = [
                '@type' => 'CreativeWork',
                '@id' => get_permalink($port->ID) . '#creative-work',
                'name' => $port->post_title,
                'url' => get_permalink($port->ID),
                'image' => get_the_post_thumbnail_url($port->ID, 'large')
            ];
            
            // Vincular cliente del portafolio
            if ($port_cliente_id) {
                $part_item['client'] = [
                    '@type' => 'Organization',
                    '@id' => get_permalink($port_cliente_id) . '#organization',
                    'name' => get_the_title($port_cliente_id),
                    'url' => get_permalink($port_cliente_id)
                ];
            }
            
            $has_parts[] = $part_item;
        }
        
        $schema['hasPart'] = $has_parts;
    }
    
    // MEJORA v3.0: Agregar URL externa si existe
    $url_externa = get_post_meta($post_id, 'mg_caso_url', true);
    if ($url_externa) {
        $schema['mainEntityOfPage'] = $url_externa;
    }
    
    return $schema;
}

/**
 * Schema para Cliente/Organización
 * v3.0: Agrega servicios contratados, portafolios, información contractual
 */
function mg_schema_organization($post_id) {
    $descripcion = get_post_meta($post_id, 'mg_cliente_descripcion', true);
    $industrias = get_the_terms($post_id, 'mg_industria');
    
    // NUEVOS CAMPOS
    $linkedin = get_post_meta($post_id, 'mg_cliente_linkedin', true);
    $num_empleados = get_post_meta($post_id, 'mg_cliente_num_empleados', true);
    
    // Casos de éxito (SUBJECTOF en lugar de member)
    $casos = get_posts([
        'post_type' => 'mg_caso_exito',
        'numberposts' => -1,
        'meta_query' => [[
            'key' => 'mg_caso_cliente',
            'value' => $post_id
        ]]
    ]);
    
    $subject_of = [];
    foreach ($casos as $caso) {
        $subject_of[] = [
            '@type' => 'Article',
            'additionalType' => 'https://schema.org/CaseStudy',
            '@id' => get_permalink($caso->ID) . '#case-study',
            'headline' => $caso->post_title,
            'url' => get_permalink($caso->ID)
        ];
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        '@id' => get_permalink($post_id) . '#organization',
        'name' => get_the_title($post_id),
        'description' => $descripcion,
        'logo' => get_the_post_thumbnail_url($post_id, 'medium'),
        'url' => get_permalink($post_id),
        'industry' => $industrias && !is_wp_error($industrias) ? $industrias[0]->name : null,
        'sameAs' => $linkedin ? [$linkedin] : null,
        'numberOfEmployees' => $num_empleados ? intval($num_empleados) : null,
        'subjectOf' => $subject_of
    ];
    
    // MEJORA v3.0: Agregar servicios contratados
    $servicios_ids = get_post_meta($post_id, '_mg_servicios_auto', true) ?: [];
    if (!empty($servicios_ids)) {
        $seeks = [];
        foreach ($servicios_ids as $servicio_id) {
            $servicio_bajada = get_post_meta($servicio_id, 'mg_servicio_bajada', true);
            $seeks[] = [
                '@type' => 'Service',
                '@id' => get_permalink($servicio_id) . '#service',
                'name' => get_the_title($servicio_id),
                'description' => $servicio_bajada,
                'url' => get_permalink($servicio_id)
            ];
        }
        $schema['seeks'] = $seeks;
    }
    
    // MEJORA v3.0: Agregar portafolios como trabajos realizados
    $portafolios = get_posts([
        'post_type' => 'mg_portafolio',
        'numberposts' => -1,
        'meta_query' => [[
            'key' => 'mg_portafolio_cliente',
            'value' => $post_id
        ]]
    ]);
    
    if (!empty($portafolios)) {
        $owns_creative_work = [];
        foreach ($portafolios as $port) {
            $owns_creative_work[] = [
                '@type' => 'CreativeWork',
                '@id' => get_permalink($port->ID) . '#creative-work',
                'name' => $port->post_title,
                'url' => get_permalink($port->ID),
                'creator' => [
                    '@type' => 'Organization',
                    'name' => get_bloginfo('name'),
                    'url' => home_url()
                ]
            ];
        }
        $schema['owns'] = $owns_creative_work;
    }
    
    // MEJORA v3.0: Información contractual
    $inicio = get_post_meta($post_id, 'mg_cliente_inicio_contrato', true);
    $termino = get_post_meta($post_id, 'mg_cliente_termino_contrato', true);
    
    if ($inicio) {
        $schema['foundingDate'] = $inicio;
    }
    
    // Determinar estado de la relación
    if ($termino) {
        $es_activo = strtotime($termino) > time();
        
        if (!$es_activo) {
            $schema['dissolutionDate'] = $termino;
        }
        
        // Agregar anotación en descripción
        $estado = $es_activo ? 'Cliente activo' : 'Colaboración finalizada';
        $schema['description'] = ($schema['description'] ?? '') . ' ' . $estado . '.';
    } else {
        $schema['description'] = ($schema['description'] ?? '') . ' Cliente activo.';
    }
    
    return $schema;
}

/**
 * Schema para Servicio
 * v3.0: Agrega área como provider, precio/ofertas
 */
function mg_schema_service($post_id) {
    $bajada = get_post_meta($post_id, 'mg_servicio_bajada', true);
    $categorias = get_the_terms($post_id, 'mg_categoria');

    // Patron para arrays serializados de enteros, ejemplo: i:34;
    $int_pattern = 'i:' . intval($post_id) . ';';

    // Patron para arrays serializados de strings, ejemplo: "34"
    $string_pattern = '"' . strval($post_id) . '"';

    // Casos de exito relacionados al servicio
    $casos = get_posts([
        'post_type' => 'mg_caso_exito',
        'numberposts' => -1,
        'meta_query' => [
            'relation' => 'OR',
            [
                'key' => 'mg_caso_servicios',
                'value' => $int_pattern,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'mg_caso_servicios',
                'value' => $string_pattern,
                'compare' => 'LIKE'
            ]
        ]
    ]);

    // Portafolios relacionados al servicio
    $portafolios = get_posts([
        'post_type' => 'mg_portafolio',
        'numberposts' => -1,
        'meta_query' => [
            'relation' => 'OR',
            [
                'key' => 'mg_portafolio_servicio',
                'value' => $int_pattern,
                'compare' => 'LIKE'
            ],
            [
                'key' => 'mg_portafolio_servicio',
                'value' => $string_pattern,
                'compare' => 'LIKE'
            ]
        ]
    ]);

    $subject_of = [];

    foreach ($casos as $caso) {
        $caso_cliente_id = get_post_meta($caso->ID, 'mg_caso_cliente', true);
        
        $caso_item = [
            '@type' => 'Article',
            'additionalType' => 'https://schema.org/CaseStudy',
            '@id' => get_permalink($caso->ID) . '#case-study',
            'headline' => get_the_title($caso->ID),
            'url' => get_permalink($caso->ID)
        ];
        
        // Agregar cliente si existe
        if ($caso_cliente_id) {
            $cliente_linkedin = get_post_meta($caso_cliente_id, 'mg_cliente_linkedin', true);
            $caso_item['client'] = [
                '@type' => 'Organization',
                '@id' => get_permalink($caso_cliente_id) . '#organization',
                'name' => get_the_title($caso_cliente_id),
                'url' => get_permalink($caso_cliente_id)
            ];
            
            if ($cliente_linkedin) {
                $caso_item['client']['sameAs'] = [$cliente_linkedin];
            }
        }
        
        $subject_of[] = $caso_item;
    }

    foreach ($portafolios as $portafolio) {
        $portafolio_cliente_id = get_post_meta($portafolio->ID, 'mg_portafolio_cliente', true);
        
        $portafolio_item = [
            '@type' => 'CreativeWork',
            '@id' => get_permalink($portafolio->ID) . '#portfolio',
            'name' => get_the_title($portafolio->ID),
            'url' => get_permalink($portafolio->ID)
        ];
        
        // Agregar cliente si existe
        if ($portafolio_cliente_id) {
            $cliente_linkedin = get_post_meta($portafolio_cliente_id, 'mg_cliente_linkedin', true);
            $portafolio_item['client'] = [
                '@type' => 'Organization',
                '@id' => get_permalink($portafolio_cliente_id) . '#organization',
                'name' => get_the_title($portafolio_cliente_id),
                'url' => get_permalink($portafolio_cliente_id)
            ];
            
            if ($cliente_linkedin) {
                $portafolio_item['client']['sameAs'] = [$cliente_linkedin];
            }
        }
        
        $subject_of[] = $portafolio_item;
    }

    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        '@id' => get_permalink($post_id) . '#service',
        'name' => get_the_title($post_id),
        'description' => $bajada ? $bajada : get_the_excerpt($post_id),
        'url' => get_permalink($post_id),
        'serviceType' => $categorias && !is_wp_error($categorias) ? $categorias[0]->name : null,
        'provider' => [
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url()
        ]
    ];

    if (!empty($subject_of)) {
        $schema['subjectOf'] = $subject_of;
    }
    
    // MEJORA v3.0: Agregar área como provider
    $area_id = get_post_meta($post_id, 'mg_servicio_area', true);
    if ($area_id) {
        $area_descripcion = get_post_meta($area_id, 'mg_area_descripcion', true);
        $schema['provider'] = [
            '@type' => 'Organization',
            'additionalType' => 'OrganizationalUnit',
            '@id' => get_permalink($area_id) . '#department',
            'name' => get_the_title($area_id),
            'description' => $area_descripcion,
            'url' => get_permalink($area_id),
            'parentOrganization' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url()
            ]
        ];
    }
    
    // MEJORA v3.0: Agregar información de precio si existe
    $precio = get_post_meta($post_id, 'mg_servicio_precio', true);
    if ($precio) {
        $schema['offers'] = [
            '@type' => 'Offer',
            'description' => wp_strip_all_tags($precio),
            'availability' => 'https://schema.org/InStock',
            'seller' => [
                '@type' => 'Organization',
                'name' => get_bloginfo('name'),
                'url' => home_url()
            ]
        ];
    }
    
    // MEJORA v3.0: Agregar beneficios como propiedad adicional
    $beneficios = get_post_meta($post_id, 'mg_servicio_beneficios', true);
    if ($beneficios) {
        $schema['additionalProperty'] = [
            '@type' => 'PropertyValue',
            'name' => 'Beneficios',
            'value' => wp_strip_all_tags(wp_trim_words($beneficios, 50))
        ];
    }

    return $schema;
}

/**
 * Schema para Portafolio (Creative Work)
 * v3.0: Agrega vínculo a caso de éxito, videos, galería
 */
function mg_schema_creative_work($post_id) {
    $cliente_id = get_post_meta($post_id, 'mg_portafolio_cliente', true);
    $servicios_ids = get_post_meta($post_id, 'mg_portafolio_servicio', true) ?: [];
    $equipo_ids = get_post_meta($post_id, 'mg_portafolio_equipo', true) ?: [];
    
    // Equipo participante
    $contributors = [];
    foreach ($equipo_ids as $miembro_id) {
        $contributors[] = [
            '@type' => 'Person',
            '@id' => get_permalink($miembro_id) . '#person',
            'name' => get_the_title($miembro_id),
            'url' => get_permalink($miembro_id)
        ];
    }
    
    // Servicios aplicados (MENTIONS)
    $services_mentioned = [];
    foreach ($servicios_ids as $servicio_id) {
        $services_mentioned[] = [
            '@type' => 'Service',
            '@id' => get_permalink($servicio_id) . '#service',
            'name' => get_the_title($servicio_id),
            'url' => get_permalink($servicio_id)
        ];
    }
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'CreativeWork',
        '@id' => get_permalink($post_id) . '#creative-work',
        'name' => get_the_title($post_id),
        'description' => get_the_excerpt($post_id),
        'image' => get_the_post_thumbnail_url($post_id, 'large'),
        'url' => get_permalink($post_id),
        'creator' => [
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url()
        ],
        'contributor' => $contributors,
        'client' => $cliente_id ? [
            '@type' => 'Organization',
            '@id' => get_permalink($cliente_id) . '#organization',
            'name' => get_the_title($cliente_id),
            'url' => get_permalink($cliente_id)
        ] : null,
        'mentions' => $services_mentioned
    ];
    
    // MEJORA v3.0: Vincular al caso de éxito si existe
    $caso_id = get_post_meta($post_id, 'mg_portafolio_caso_exito', true);
    if ($caso_id) {
        $caso_cliente_id = get_post_meta($caso_id, 'mg_caso_cliente', true);
        
        $schema['isPartOf'] = [
            '@type' => 'Article',
            'additionalType' => 'https://schema.org/CaseStudy',
            '@id' => get_permalink($caso_id) . '#case-study',
            'name' => get_the_title($caso_id),
            'url' => get_permalink($caso_id)
        ];
        
        // Agregar cliente del caso
        if ($caso_cliente_id) {
            $schema['isPartOf']['about'] = [
                '@type' => 'Organization',
                '@id' => get_permalink($caso_cliente_id) . '#organization',
                'name' => get_the_title($caso_cliente_id),
                'url' => get_permalink($caso_cliente_id)
            ];
        }
    }
    
  // MEJORA v3.0: Agregar videos
$videos_youtube = get_post_meta($post_id, 'mg_portafolio_videos_youtube', true);
$videos_externos = get_post_meta($post_id, 'mg_portafolio_videos_externos', true);

// Asegurar que sean arrays
$videos_youtube = is_array($videos_youtube) ? $videos_youtube : [];
$videos_externos = is_array($videos_externos) ? $videos_externos : [];

$videos_schema = [];

// Videos de YouTube
if (!empty($videos_youtube)) {
    foreach ($videos_youtube as $video) {
        if (is_array($video) && !empty($video['url']) && !empty($video['id'])) {
            $videos_schema[] = [
                '@type' => 'VideoObject',
                'embedUrl' => $video['url'],
                'name' => isset($video['titulo']) ? $video['titulo'] : get_the_title($post_id) . ' - Video',
                'thumbnailUrl' => 'https://img.youtube.com/vi/' . $video['id'] . '/maxresdefault.jpg',
                'uploadDate' => get_the_date('c', $post_id)
            ];
        }
    }
}

// Videos externos
if (!empty($videos_externos)) {
    foreach ($videos_externos as $video) {
        if (is_array($video) && !empty($video['url'])) {
            $videos_schema[] = [
                '@type' => 'VideoObject',
                'contentUrl' => $video['url'],
                'name' => isset($video['titulo']) ? $video['titulo'] : get_the_title($post_id) . ' - Video',
                'uploadDate' => get_the_date('c', $post_id)
            ];
        }
    }
}

if (!empty($videos_schema)) {
    $schema['video'] = $videos_schema;
}
    // MEJORA v3.0: Agregar galería de imágenes
    $galeria = get_post_meta($post_id, 'mg_portafolio_galeria', true);
    if (!empty($galeria)) {
        $image_objects = [];
        $attachment_ids = is_array($galeria) ? $galeria : explode(',', $galeria);
        
        foreach ($attachment_ids as $attachment_id) {
            $image_url = wp_get_attachment_url($attachment_id);
            if ($image_url) {
                $image_meta = wp_get_attachment_metadata($attachment_id);
                $image_objects[] = [
                    '@type' => 'ImageObject',
                    'url' => $image_url,
                    'contentUrl' => $image_url,
                    'width' => $image_meta['width'] ?? null,
                    'height' => $image_meta['height'] ?? null
                ];
            }
        }
        
        if (!empty($image_objects)) {
            $schema['associatedMedia'] = $image_objects;
        }
    }
    
    return $schema;
}

/**
 * Schema para Área (NUEVO v3.0)
 * Tipo: Organization > OrganizationalUnit
 */
function mg_schema_area($post_id) {
    $director_id = get_post_meta($post_id, 'mg_area_director', true);
    $miembros_ids = get_post_meta($post_id, 'mg_area_miembros', true) ?: [];
    $descripcion = get_post_meta($post_id, 'mg_area_descripcion', true);
    
    // Servicios del área
    $servicios = get_posts([
        'post_type' => 'mg_servicio',
        'numberposts' => -1,
        'meta_query' => [[
            'key' => 'mg_servicio_area',
            'value' => $post_id
        ]]
    ]);
    
    $schema = [
        '@context' => 'https://schema.org',
        '@type' => 'Organization',
        'additionalType' => 'OrganizationalUnit',
        '@id' => get_permalink($post_id) . '#department',
        'name' => get_the_title($post_id),
        'description' => $descripcion,
        'url' => get_permalink($post_id),
        'parentOrganization' => [
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url()
        ]
    ];
    
    // Agregar director como líder
    if ($director_id) {
        $director_cargo = get_post_meta($director_id, 'mg_equipo_cargo', true);
        $director_linkedin = get_post_meta($director_id, 'mg_equipo_linkedin', true);
        
        $schema['leader'] = [
            '@type' => 'Person',
            '@id' => get_permalink($director_id) . '#person',
            'name' => get_the_title($director_id),
            'jobTitle' => $director_cargo,
            'url' => get_permalink($director_id)
        ];
        
        if ($director_linkedin) {
            $schema['leader']['sameAs'] = [$director_linkedin];
        }
    }
    
    // Agregar miembros del equipo
    if (!empty($miembros_ids)) {
        $members = [];
        foreach ($miembros_ids as $miembro_id) {
            $miembro_cargo = get_post_meta($miembro_id, 'mg_equipo_cargo', true);
            $members[] = [
                '@type' => 'Person',
                '@id' => get_permalink($miembro_id) . '#person',
                'name' => get_the_title($miembro_id),
                'jobTitle' => $miembro_cargo,
                'url' => get_permalink($miembro_id)
            ];
        }
        $schema['member'] = $members;
    }
    
    // Agregar servicios que ofrece el área
    if (!empty($servicios)) {
        $provides = [];
        foreach ($servicios as $servicio) {
            $servicio_bajada = get_post_meta($servicio->ID, 'mg_servicio_bajada', true);
            $provides[] = [
                '@type' => 'Service',
                '@id' => get_permalink($servicio->ID) . '#service',
                'name' => $servicio->post_title,
                'description' => $servicio_bajada,
                'url' => get_permalink($servicio->ID)
            ];
        }
        $schema['provides'] = $provides;
    }
    
    return $schema;
}

/**
 * Schema genérico para páginas
 */
function mg_schema_webpage($post_id) {
    return [
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        '@id' => get_permalink($post_id) . '#webpage',
        'name' => get_the_title($post_id),
        'description' => get_the_excerpt($post_id),
        'url' => get_permalink($post_id),
        'publisher' => [
            '@type' => 'Organization',
            'name' => get_bloginfo('name'),
            'url' => home_url()
        ]
    ];
}

/**
 * Inyectar Schema en el <head>
 */
add_action('wp_head', function () {
    if (!is_singular()) return;
    
    global $post;
    
    // Verificar si hay schema manual
    $schema_manual = get_post_meta($post->ID, 'mg_schema_json', true);
    
    if ($schema_manual) {
        // Usar schema manual si existe
        echo "\n" . '<script type="application/ld+json">' . "\n" . $schema_manual . "\n" . '</script>' . "\n";
    } else {
        // Generar schema automático
        $schema = mg_generate_auto_schema($post->ID);
        if ($schema) {
            // Limpiar nulls del schema
            $schema = mg_clean_schema_nulls($schema);
            
            // Sanitizar UTF-8 para evitar escapes innecesarios
            $schema = mg_sanitize_schema_utf8($schema);
            
            // Generar JSON con encoding UTF-8 correcto
            $json = json_encode($schema, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            
            // Verificar que el JSON se generó correctamente
            if ($json !== false) {
                echo "\n" . '<script type="application/ld+json">' . "\n" . $json . "\n" . '</script>' . "\n";
            }
        }
    }
}, 1);

/**
 * Limpiar valores null del schema recursivamente
 */
function mg_clean_schema_nulls($array) {
    foreach ($array as $key => $value) {
        if (is_null($value)) {
            unset($array[$key]);
        } elseif (is_array($value)) {
            $array[$key] = mg_clean_schema_nulls($value);
            if (empty($array[$key])) {
                unset($array[$key]);
            }
        }
    }
    return $array;
}

/**
 * Sanitizar strings para asegurar UTF-8 correcto en el schema
 * Convierte strings con encoding incorrecto a UTF-8 válido
 */
function mg_sanitize_schema_utf8($data) {
    if (is_string($data)) {
        // Si el string ya está en UTF-8 válido, devolverlo tal cual
        if (mb_check_encoding($data, 'UTF-8')) {
            return $data;
        }
        
        // Si no está en UTF-8, intentar convertirlo
        $encoding = mb_detect_encoding($data, ['UTF-8', 'ISO-8859-1', 'Windows-1252'], true);
        if ($encoding && $encoding !== 'UTF-8') {
            return mb_convert_encoding($data, 'UTF-8', $encoding);
        }
        
        // Como último recurso, forzar UTF-8
        return mb_convert_encoding($data, 'UTF-8', 'UTF-8');
    }
    
    if (is_array($data)) {
        foreach ($data as $key => $value) {
            $data[$key] = mg_sanitize_schema_utf8($value);
        }
    }
    
    return $data;
}

/**
 * Meta tags personalizados
 */
add_action('wp_head', function () {
    if (!is_singular()) return;
    
    global $post;
    
    // Verificar noindex
    $noindex = get_post_meta($post->ID, 'mg_seo_noindex', true);
    if ($noindex) {
        echo '<meta name="robots" content="noindex, nofollow">' . "\n";
    }
    
    // Meta title
    $seo_title = get_post_meta($post->ID, 'mg_seo_title', true);
    if ($seo_title) {
        echo '<meta property="og:title" content="' . esc_attr($seo_title) . '">' . "\n";
        echo '<meta name="twitter:title" content="' . esc_attr($seo_title) . '">' . "\n";
    }
    
    // Meta description
    $seo_description = get_post_meta($post->ID, 'mg_seo_description', true);
    if ($seo_description) {
        echo '<meta name="description" content="' . esc_attr($seo_description) . '">' . "\n";
        echo '<meta property="og:description" content="' . esc_attr($seo_description) . '">' . "\n";
        echo '<meta name="twitter:description" content="' . esc_attr($seo_description) . '">' . "\n";
    }
    
    // Meta keywords
    $seo_keywords = get_post_meta($post->ID, 'mg_seo_keywords', true);
    if ($seo_keywords) {
        echo '<meta name="keywords" content="' . esc_attr($seo_keywords) . '">' . "\n";
    }
    
    // OG Image
    $og_image = get_post_meta($post->ID, 'mg_og_image', true) ?: get_the_post_thumbnail_url($post->ID, 'large');
    if ($og_image) {
        echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
        echo '<meta name="twitter:image" content="' . esc_url($og_image) . '">' . "\n";
        echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    }
    
    // OG Type
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:url" content="' . get_permalink($post->ID) . '">' . "\n";
    
}, 5);

// =============================================================================
// FUNCIONES AUXILIARES
// =============================================================================

/**
 * Verificar si un miembro es director de algún área
 */
if (!function_exists('mg_is_director')) {
    function mg_is_director($miembro_id) {
        $areas = get_posts([
            'post_type' => 'mg_area',
            'numberposts' => -1,
            'meta_query' => [[
                'key' => 'mg_area_director',
                'value' => $miembro_id
            ]]
        ]);
        
        return !empty($areas) ? $areas : false;
    }
}

/**
 * Obtener subordinados directos de un director
 */
if (!function_exists('mg_get_subordinados')) {
    function mg_get_subordinados($director_id) {
        $subordinados = get_posts([
            'post_type' => 'mg_equipo',
            'numberposts' => -1,
            'meta_query' => [[
                'key' => 'mg_equipo_jefe_directo',
                'value' => $director_id
            ]]
        ]);
        
        return $subordinados;
    }
}
