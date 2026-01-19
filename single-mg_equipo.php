<?php
if (!defined('ABSPATH')) exit;
get_header();

the_post();
$miembro_id = get_the_ID();
$lang = function_exists('pll_get_post_language') ? pll_get_post_language($miembro_id) : false;

// Datos personales del miembro
$cargo = get_post_meta($miembro_id, 'mg_equipo_cargo', true);
$area_id = get_post_meta($miembro_id, 'mg_equipo_area', true);
$sub_area = get_post_meta($miembro_id, 'mg_equipo_subarea', true);
$bio = get_post_meta($miembro_id, 'mg_equipo_bio', true);
$linkedin = get_post_meta($miembro_id, 'mg_equipo_linkedin', true);
$equipos = get_the_terms($miembro_id, 'mg_equipos');
$correo = get_post_meta($miembro_id, 'mg_equipo_email', true);

// NUEVO: Detectar si es director
$areas_director = mg_is_director($miembro_id);
$es_director = !empty($areas_director);

// Ajustar área según idioma si es necesario
if ($lang && $area_id && function_exists('pll_get_post_language') && pll_get_post_language($area_id) !== $lang) {
    $area_id = pll_get_post($area_id, $lang);
}

// Ajustar áreas de director según idioma
if ($lang && $es_director) {
    foreach ($areas_director as &$area_dir) {
        if (function_exists('pll_get_post_language') && pll_get_post_language($area_dir->ID) !== $lang) {
            $translated = pll_get_post($area_dir->ID, $lang);
            if ($translated) {
                $area_dir = get_post($translated);
            }
        }
    }
    unset($area_dir);
}
?>
<?php if (!empty($equipos) && !is_wp_error($equipos)): ?>
        <?php $equipos = $equipos[0]; ?>      <?php endif; ?>
<main class="container py-5" itemscope itemtype="https://schema.org/Person">
 
    
<!-- Header del miembro -->
    <header class=" mb-2  p-top" style=";position:relative">
        <div class="row  g-2" >
        <div class=" col-lg-2 col-md-3 " >
            <div class="card-mg p-0" > 
            <?php if (has_post_thumbnail()): ?>
                <div >
                    <?php the_post_thumbnail('large', [
                        'class' => 'img-fluid',
                        'itemprop' => 'image',
                        'style' => 'width: 100%;height:300px;object-fit: cover;object-position:50% 30%'
                    ]); ?>
                </div>
            <?php endif; ?></div>
        </div>
        
        <div class="col-lg-10 col-md-9 " style="min-height:100%"  >
                        <div class="card-mg "  style="min-height:100%;align-content:center"> 

            <h1 class="display-6 mb-2" itemprop="name"><?php the_title(); ?></h1>
            
            <?php if ($cargo): ?>
                <p class="lead mb-0" itemprop="jobTitle">
                    <strong><?= esc_html($cargo); ?></strong>
                        
                </p>
                    
                                 
            <?php endif; ?>
            
            <?php 
            // === CONDICIONAL DIRECTOR VS NO DIRECTOR ===
            if ($es_director): 
                // MOSTRAR PARA DIRECTORES
            ?>
                <p class="mb-2 mt-2">
                    <span style="color: var(--text-secondary);">     <a href="<?= esc_url(get_term_link($equipos)); ?>"> 
                            <?= esc_html($equipos->name); ?><?php _e(' de', 'maggiore'); ?>
                        </a> </span><br>
                    <?php foreach ($areas_director as $area_dir): ?>
                        <a href="<?= get_permalink($area_dir->ID); ?>" 
                           class="service-tag" 
                         >
                            <?= esc_html($area_dir->post_title); ?>
                        </a>
                    <?php endforeach; ?>
                </p>
                
          
                
            <?php else: 
                // MOSTRAR PARA NO DIRECTORES
                if ($area_id): 
            ?>
                <p class="mb-2" itemprop="worksFor" itemscope itemtype="https://schema.org/Organization">
                    <span><?php _e('Área:', 'maggiore'); ?></span>
                    <a href="<?= get_permalink($area_id); ?>" itemprop="url">
                        <span itemprop="name"><?= get_the_title($area_id); ?></span>
                    </a> /
                    <a href="<?= esc_url(get_term_link($equipos)); ?>">
                        <?= esc_html($equipos->name); ?>
                    </a>  
                </p>
            <?php 
                endif; 
            endif; 
            ?>
            
            <?php if ($linkedin): ?>
                <p class="mb-3">
                    <a href="<?= esc_url($linkedin); ?>" 
                       class="btn   btn-sm btn-linkedin"
                       target="_blank" 
                       rel="noopener noreferrer"
                       itemprop="sameAs"
                       aria-label="<?= esc_attr(sprintf(__('Ver perfil de %s en LinkedIn', 'maggiore'), get_the_title())); ?>">
                        <i class="bi bi-linkedin"></i>
                        LinkedIn
                    </a>
                       <?php if ($correo): ?> 
               <a href="#"
   class="btn btn-outline-secondary btn-sm copiar-correo"
   data-correo="<?= esc_attr($correo); ?>"
   role="button"
   aria-label="<?= esc_attr(sprintf(__('Copiar correo de %s', 'maggiore'), get_the_title())); ?>">
    <i class="bi bi-envelope"></i>
    <?= esc_html($correo); ?>
</a>
                       <?php endif; ?>
                </p>
            <?php endif; ?>
        </div>
        </div>
        </div>
    </header>

                        
    <div class="row g-2 mb-5">
    <!-- Biografía -->
    <?php if ($bio ): ?>
        <div class="col-lg-8" style="min-height: 100%;">
               <div class="card-mg " style="height: 100%;">

            <h2 class="label"><?php _e('Sobre mí', 'maggiore'); ?></h2>
            
            <?php if ($bio): ?>
                <div class="lead mb-4" itemprop="description">
                    <?= wpautop(esc_html($bio)); ?>
                </div>
            <?php endif; ?>
            
                </div>
            </div>
    <?php endif; ?>
<?php
// === Áreas de Especialización ===
$especialidades = get_post_meta($miembro_id, 'mg_equipo_especialidades', true);

if (!empty($especialidades) && is_array($especialidades)): ?>
    <div class="col-lg-4"  style="min-height: 100%;">
        <div class="card-mg "  style="height: 100%;">
            <h2 class="label"><?php _e('Áreas de Especialización', 'maggiore'); ?></h2>
   
        
        <ul class="list-unstyled row g-2">
            <?php foreach ($especialidades as $especialidad): ?>
                <li class="col-auto ">
                    <div class="d-flex align-items-center px-1 border-info   border rounded" >
                        <i class="bi bi-check "></i>
                      
                        <span class="mx-2"><?= esc_html($especialidad); ?></span>
                    </div>
                </li>
            <?php endforeach; ?>
        </ul>
        </div>
    </div>
<?php endif; ?>

<?php
// === Equipo a cargo ===
// NUEVO: Para directores, mostrar TODO el equipo de sus áreas
if ($es_director) {
    $equipo_a_cargo = mg_get_equipo_completo_director($miembro_id);
} else {
    // Para no directores, mostrar solo reportes directos
    $equipo_a_cargo = get_posts([
        'post_type'   => 'mg_equipo',
        'numberposts' => -1,
        'orderby'     => 'title',
        'order'       => 'ASC',
        'meta_query'  => [[
            'key'     => 'mg_equipo_jefe_directo',
            'value'   => $miembro_id,
            'compare' => '='
        ]]
    ]);
}

// Filtrar por idioma si es necesario Y eliminar duplicados
if ($lang && !empty($equipo_a_cargo)) {
    $equipo_temp = [];
    $ids_procesados = []; // Para evitar duplicados
    
    foreach ($equipo_a_cargo as $miembro) {
        $miembro_actual_id = $miembro->ID;
        
        // Ajustar al idioma correcto
        if (function_exists('pll_get_post_language') && pll_get_post_language($miembro_actual_id) !== $lang) {
            $traducido = pll_get_post($miembro_actual_id, $lang);
            if ($traducido) {
                $miembro_actual_id = $traducido;
            }
        }
        
        // Solo agregar si no se ha procesado este ID
        if (!in_array($miembro_actual_id, $ids_procesados)) {
            $ids_procesados[] = $miembro_actual_id;
            $equipo_temp[] = get_post($miembro_actual_id);
        }
    }
    
    $equipo_a_cargo = $equipo_temp;
} else if (!empty($equipo_a_cargo)) {
    // Sin idiomas, también filtrar duplicados por si acaso
    $equipo_temp = [];
    $ids_procesados = [];
    
    foreach ($equipo_a_cargo as $miembro) {
        if (!in_array($miembro->ID, $ids_procesados)) {
            $ids_procesados[] = $miembro->ID;
            $equipo_temp[] = $miembro;
        }
    }
    
    $equipo_a_cargo = $equipo_temp;
}

if (!empty($equipo_a_cargo)): ?>
    <div class="col-lg-12">
        <div class="card-mg ">
            <h2 class="label">
                <?php 
                if ($es_director) {
                    _e('Equipo completo de las áreas que supervisa', 'maggiore'); 
                } else {
                    _e('Equipo a cargo', 'maggiore'); 
                }
                ?>
            </h2>
        
        <div class="row g-2">
            <?php foreach ($equipo_a_cargo as $miembro): 
                $cargo_miembro = get_post_meta($miembro->ID, 'mg_equipo_cargo', true);
                $area_miembro_id = get_post_meta($miembro->ID, 'mg_equipo_area', true);
                
                // Ajustar área según idioma
                if ($lang && $area_miembro_id && function_exists('pll_get_post_language') && pll_get_post_language($area_miembro_id) !== $lang) {
                    $area_miembro_id = pll_get_post($area_miembro_id, $lang);
                }
            ?>
                <div class="col-auto">
                     <a href="<?= get_permalink($miembro->ID); ?>" 
                           class="person-card"
                         >
                              <?php if (has_post_thumbnail($miembro->ID)): ?>
                                   <?= get_the_post_thumbnail($miembro->ID, 'thumbnail', [
                                                'class' => 'rounded-circle me-3',
                                                'style' => 'width: 50px; height: 50px; object-fit: cover;'
                                            ]); ?>    
                            <?php endif; ?>
                            <div style="line-height: 1.3;">
                                <div style="color: white; font-weight: 500;margin-right:20px">
                                   <?= esc_html($miembro->post_title); ?>
                                </div>
                                <?php if ($cargo_miembro): ?>
                                    <div style="color: var(--text-secondary); font-size: 0.875rem;">
                                        <?= esc_html($cargo_miembro); ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </a>


                  
                </div>
            <?php endforeach; ?>
        </div></div>
                                </div>
<?php endif; ?>
</div>

    <?php
    // === Casos de Éxito ===
    $casos_ids = get_posts([
        'post_type'   => 'mg_caso_exito',
        'numberposts' => -1,
        'fields'      => 'ids',
        'orderby'     => 'date',
        'order'       => 'DESC',
        'meta_query'  => [[
            'key'     => '_mg_equipo_auto',
            'value'   => $miembro_id,
            'compare' => 'LIKE',
        ]]
    ]);

    if ($lang && !empty($casos_ids)) {
        foreach ($casos_ids as &$id) {
            if (function_exists('pll_get_post_language') && pll_get_post_language($id) !== $lang) {
                $translated = pll_get_post($id, $lang);
                if ($translated) $id = $translated;
            }
        }
        unset($id); // Limpiar la referencia
        $casos_ids = array_unique($casos_ids); // Eliminar duplicados
    }
    ?>

    <?php if (!empty($casos_ids)): ?>
        <section class="mb-5">
            <div class="feature-name-2 mb-2">
            <h2 >
             <?php _e('Casos de Éxito', 'maggiore'); ?>        
                </h2>
            </div>
        
            
            <div class="row g-2">
                <?php foreach ($casos_ids as $caso_id): 
                    global $post;
                    $post = get_post($caso_id);
                    setup_postdata($post);
                ?>
                    <div class="col-12">
                        <?php get_template_part('template-parts/card', 'caso-exito'); ?>
                    </div>
                <?php 
                endforeach; 
                wp_reset_postdata(); 
                ?>
            </div>
        </section>

    <?php endif; ?>

    <?php
    // === Proyectos de Portafolio ===
    $portafolios_ids = (array) get_post_meta($miembro_id, '_mg_portafolios_auto', true);
    
    if (!empty($portafolios_ids)) {
        $portafolios = get_posts([
            'post_type'   => 'mg_portafolio',
            'post__in'    => $portafolios_ids,
            'numberposts' => -1,
            'orderby'     => 'date',
            'order'       => 'DESC'
        ]);

        if ($lang && !empty($portafolios)) {
            foreach ($portafolios as &$p) {
                if (function_exists('pll_get_post_language') && pll_get_post_language($p->ID) !== $lang) {
                    $translated = pll_get_post($p->ID, $lang);
                    if ($translated) {
                        $p = get_post($translated);
                    }
                }
            }
            unset($p); // Limpiar la referencia
            
            // Eliminar duplicados basados en ID
            $ids_unicos = [];
            $portafolios_unicos = [];
            foreach ($portafolios as $portfolio) {
                if (!in_array($portfolio->ID, $ids_unicos)) {
                    $ids_unicos[] = $portfolio->ID;
                    $portafolios_unicos[] = $portfolio;
                }
            }
            $portafolios = $portafolios_unicos;
        }
    } else {
        $portafolios = [];
    }
    ?>

    <?php if (!empty($portafolios)): ?>
        <section class="mb-5" >
          
                         <div class="feature-name-2 mb-3">
            <h2 >
                <?php _e('Portafolio', 'maggiore'); ?>
            </h2>
            </div>

<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3 g-2">
                    <?php 
                    global $post;
                    foreach ($portafolios as $post): 
                        setup_postdata($post); 
                    ?>
                    <div class="col mt-0">
                        <?php get_template_part('template-parts/card', 'portafolio'); ?>
                    </div>
                    <?php 
                    wp_reset_postdata(); 
                endforeach; 
                    ?>
            </div>
        </section>

    <?php endif; ?>

    <?php
    // === Artículos de Blog ===
    $blog_posts = get_posts([
        'post_type'   => 'post',
        'numberposts' => -1,
        'orderby'     => 'date',
        'order'       => 'DESC',
        'meta_query'  => [[
            'key'     => 'mg_blog_autor',
            'value'   => $miembro_id,
            'compare' => '='
        ]]
    ]);

    if ($lang && !empty($blog_posts)) {
        foreach ($blog_posts as &$bp) {
            if (function_exists('pll_get_post_language') && pll_get_post_language($bp->ID) !== $lang) {
                $translated = pll_get_post($bp->ID, $lang);
                if ($translated) {
                    $bp = get_post($translated);
                }
            }
        }
        unset($bp); // Limpiar la referencia
        
        // Eliminar duplicados basados en ID
        $ids_unicos = [];
        $blog_posts_unicos = [];
        foreach ($blog_posts as $blog_post) {
            if (!in_array($blog_post->ID, $ids_unicos)) {
                $ids_unicos[] = $blog_post->ID;
                $blog_posts_unicos[] = $blog_post;
            }
        }
        $blog_posts = $blog_posts_unicos;
    }
    ?>

    <?php if (!empty($blog_posts)): ?>
        <section class="mb-5">
             <div class="feature-name-2 mb-2">
             <h2>
                <?php _e('Artículos escritos', 'maggiore'); ?>
            </h2>
             </div>
<div class="row row-cols-1 row-cols-lg-2 row-cols-xl-3 g-2">
                    <?php 
                    global $post;
                    foreach ($blog_posts as $post): 
                        setup_postdata($post); 
                    ?>
                    <div class="col">
                        <?php get_template_part('template-parts/card', 'articulo'); ?>
                    </div>
                    <?php 
                    wp_reset_postdata(); 
                endforeach; 
                    ?>
            </div>
        </section>
    <?php endif; ?>

<?php
// Al final del archivo, reemplaza toda la sección <nav>
set_query_var('prev_label', __('Miembro anterior', 'maggiore'));
set_query_var('next_label', __('Siguiente miembro', 'maggiore'));
set_query_var('show_thumbnail', false);
get_template_part('template-parts/navigation', 'single');
?>
</main>

<?php get_footer(); ?>
