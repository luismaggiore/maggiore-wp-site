<?php
/**
 * Template: Single Área
 * Respeta diseño original + Búsqueda corregida por servicios
 */

if (!defined('ABSPATH')) exit;
get_header();

the_post();
$area_id = get_the_ID();
$lang = function_exists('pll_get_post_language') ? pll_get_post_language($area_id) : false;

// ===============================
// DATOS DEL ÁREA
// ===============================
$descripcion = get_post_meta($area_id, 'mg_area_descripcion', true);
$director_id = get_post_meta($area_id, 'mg_area_director', true);
$miembros_ids = (array) get_post_meta($area_id, 'mg_area_miembros', true);

// Traducir IDs si es necesario
if ($lang && function_exists('pll_get_post')) {
    if ($director_id && pll_get_post_language($director_id) !== $lang) {
        $director_id = pll_get_post($director_id, $lang);
    }
    foreach ($miembros_ids as &$id) {
        if (pll_get_post_language($id) !== $lang) {
            $translated = pll_get_post($id, $lang);
            if ($translated) $id = $translated;
        }
    }
    unset($id);
}

// Obtener datos del director
$director_nombre = $director_id ? get_the_title($director_id) : '';
$director_foto = $director_id ? get_the_post_thumbnail_url($director_id, 'medium') : '';
$director_cargo = $director_id ? get_post_meta($director_id, 'mg_equipo_cargo', true) : '';
$director_bio = $director_id ? get_post_meta($director_id, 'mg_equipo_bio', true) : '';

// ===============================
// EQUIPO AGRUPADO POR CATEGORÍA
// ===============================
$equipo_por_categoria = [];

if (!empty($miembros_ids)) {
    $miembros_posts = get_posts([
        'post_type'   => 'mg_equipo',
        'post__in'    => $miembros_ids,
        'numberposts' => -1,
        'orderby'     => 'title',
        'order'       => 'ASC'
    ]);
    
    foreach ($miembros_posts as $miembro) {
        $categorias = get_the_terms($miembro->ID, 'mg_equipos');
        
        if (!empty($categorias) && !is_wp_error($categorias)) {
            foreach ($categorias as $categoria) {
                if (!isset($equipo_por_categoria[$categoria->term_id])) {
                    $equipo_por_categoria[$categoria->term_id] = [
                        'term' => $categoria,
                        'miembros' => []
                    ];
                }
                $equipo_por_categoria[$categoria->term_id]['miembros'][] = $miembro;
            }
        } else {
            if (!isset($equipo_por_categoria[0])) {
                $equipo_por_categoria[0] = [
                    'term' => (object)[
                        'name' => __('Equipo General', 'maggiore'),
                        'description' => ''
                    ],
                    'miembros' => []
                ];
            }
            $equipo_por_categoria[0]['miembros'][] = $miembro;
        }
    }
}

// ===============================
// SERVICIOS DEL ÁREA
// ===============================
$servicios = get_posts([
    'post_type'   => 'mg_servicio',
    'numberposts' => -1,
    'meta_query'  => [[
        'key'   => 'mg_servicio_area',
        'value' => $area_id,
    ]]
]);

// Extraer IDs de servicios
$servicios_ids = wp_list_pluck($servicios, 'ID');

// ===============================
// PROYECTOS BASADOS EN SERVICIOS
// Método correcto: obtener todos y filtrar manualmente
// ===============================
$casos_ids = [];
$portafolios_ids = [];

if (!empty($servicios_ids)) {
    
    // ========== CASOS DE ÉXITO ==========
    // Obtener TODOS los casos publicados
    $casos_args = [
        'post_type'   => 'mg_caso_exito',
        'numberposts' => -1,
        'post_status' => 'publish',
        'fields'      => 'ids'
    ];
    
    if ($lang) {
        $casos_args['lang'] = $lang;
    }
    
    $todos_casos = get_posts($casos_args);
    
    // Filtrar manualmente los que usan servicios del área
    foreach ($todos_casos as $caso_id) {
        $caso_servicios = (array) get_post_meta($caso_id, 'mg_caso_servicios', true);
        
        // Si algún servicio del caso está en los servicios del área
        if (!empty(array_intersect($caso_servicios, $servicios_ids))) {
            $casos_ids[] = $caso_id;
        }
    }
    
    // ========== PORTAFOLIOS ==========
    // Obtener TODOS los portafolios publicados
    $portafolios_args = [
        'post_type'   => 'mg_portafolio',
        'numberposts' => -1,
        'post_status' => 'publish',
        'fields'      => 'ids'
    ];
    
    if ($lang) {
        $portafolios_args['lang'] = $lang;
    }
    
    $todos_portafolios = get_posts($portafolios_args);
    
    // Filtrar manualmente los que usan servicios del área
    foreach ($todos_portafolios as $portafolio_id) {
        $portafolio_servicios = (array) get_post_meta($portafolio_id, 'mg_portafolio_servicio', true);
        
        // Si algún servicio del portafolio está en los servicios del área
        if (!empty(array_intersect($portafolio_servicios, $servicios_ids))) {
            $portafolios_ids[] = $portafolio_id;
        }
    }
}

// ========== OBTENER POSTS DESTACADOS ==========

// Casos destacados (máximo 3)
$casos_destacados = [];
if (!empty($casos_ids)) {
    $casos_destacados = get_posts([
        'post_type'   => 'mg_caso_exito',
        'post__in'    => $casos_ids,
        'numberposts' => 3,
        'orderby'     => 'date',
        'order'       => 'DESC'
    ]);
}

// Portafolios destacados (máximo 6)
$portafolios_destacados = [];
if (!empty($portafolios_ids)) {
    $portafolios_destacados = get_posts([
        'post_type'   => 'mg_portafolio',
        'post__in'    => $portafolios_ids,
        'numberposts' => 6,
        'orderby'     => 'date',
        'order'       => 'DESC'
    ]);
}

// ========== TOTAL DE PROYECTOS ==========
$total_proyectos = count($casos_ids) + count($portafolios_ids);

// ========== DEBUG MODE (descomentar para activar) ==========
/*
echo '<pre style="background: #000; color: #0f0; padding: 20px; margin: 20px; font-family: monospace;">';
echo "=== DEBUG ÁREA: " . get_the_title() . " ===\n\n";

echo "--- SERVICIOS DEL ÁREA ---\n";
echo "Total servicios: " . count($servicios_ids) . "\n";
echo "IDs servicios: " . implode(', ', $servicios_ids) . "\n\n";

echo "--- CASOS DE ÉXITO ---\n";
echo "Total casos con servicios del área: " . count($casos_ids) . "\n";
echo "IDs casos: " . implode(', ', $casos_ids) . "\n";
echo "Casos destacados mostrados: " . count($casos_destacados) . "\n\n";

echo "--- PORTAFOLIOS ---\n";
echo "Total portafolios con servicios del área: " . count($portafolios_ids) . "\n";
echo "IDs portafolios: " . implode(', ', $portafolios_ids) . "\n";
echo "Portafolios destacados mostrados: " . count($portafolios_destacados) . "\n\n";

echo "--- TOTAL ---\n";
echo "TOTAL PROYECTOS: " . $total_proyectos . "\n";
echo '</pre>';
*/
?>

<main class="container py-5" itemscope itemtype="https://schema.org/Organization">

    <!-- ===============================
         HEADER
         =============================== -->
    <header class="mb-5 p-top" style=" position: relative;">
        <h1 class="display-4 mb-3" itemprop="name">
            <?php the_title(); ?>
        </h1>
        
        <?php if ($descripcion): ?>
            <p class="lead" itemprop="description">
                <?= esc_html($descripcion); ?>
            </p>
        <?php endif; ?>

        <meta itemprop="dateModified" content="<?= esc_attr(get_the_modified_date('c')); ?>">
        <meta itemprop="memberOf" content="<?= esc_attr(get_bloginfo('name')); ?>">
    </header>

    <!-- ===============================
         IMAGEN DESTACADA
         =============================== -->
    <?php if (has_post_thumbnail()): ?>
        <section class="mb-5">
            <figure itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                <?php the_post_thumbnail('large', [
                    'class' => 'img-fluid w-100', 
                    'style' => 'border-radius: 8px; max-height: 500px; object-fit: cover;'
                ]); ?>
                <meta itemprop="url" content="<?= get_the_post_thumbnail_url($area_id, 'large'); ?>">
            </figure>
        </section>
    <?php endif; ?>

    <!-- ===============================
         BENTO GRID - INFORMACIÓN CLAVE
         =============================== -->
    <div class="row g-2 mb-5">
        
        <!-- CARD GRANDE: Director del Área -->
        <?php if ($director_id && $director_nombre): ?>
            <div class="col-lg-8">
                <div class="card-mg" style="height: 100%;">
                    <h3 class="label">
                        <?php _e('Director del Área', 'maggiore'); ?>
                    </h3>
                      
                    <div class="  row  gx-1  mt-2">           
                        <?php if ($director_foto): ?>
                            <div class="col-md-4 mb-md-0 mb-3" style="min-heigh:100%">
                                <a href="<?= esc_url(get_permalink($director_id)); ?>" 
                                   style="color: white; text-decoration: none;">
                                <img src="<?= esc_url($director_foto); ?>" 
                                     alt="<?= esc_attr($director_nombre); ?>"
                                     class="img-fluid border-mg"
                                     style="width: 100%;height:300px; object-fit: cover; object-position: 50% 30%;"></a>
                            </div>
                        <?php endif; ?>
                        
                        <div class="<?= $director_foto ? 'col-md-8' : 'col-12'; ?>  " style="min-heigh:100%" >
                            <div class="card-mg w-100 p-lg-4 align" style="height:100%">
                            <h4 class="mb-2" style="color: white; font-size: 1.5rem;">
                                <a href="<?= esc_url(get_permalink($director_id)); ?>" 
                                   style="color: white; text-decoration: none;">
                                    <?= esc_html($director_nombre); ?>
                                </a>
                            </h4>
                            
                            <?php if ($director_cargo): ?>
                                <p class="mb-3" style="color: var(--text-secondary); font-size: 1rem;">
                                    <?= esc_html($director_cargo); ?>
                                </p>
                            <?php endif; ?>
                            
                            <?php if ($director_bio): ?>
                                <p class="mb-0" style="font-size: 0.95rem; line-height: 1.6;">
                                    <?= esc_html(wp_trim_words($director_bio, 30)); ?>
                                </p>
                            <?php endif; ?>
                         <a href="<?= esc_url(get_permalink($director_id)); ?>" 
                               class="service-tag mt-3">
                                <?php _e('Ver perfil completo', 'maggiore'); ?>
                            </a>
                            </div>
                        </div>
                    </div>
             
                </div>
            </div>
        <?php endif; ?>

        <!-- CARD: Estadísticas del Área -->
        <div class="col-lg-4">
       <div class="card-mg" style="height: 100%;">
                <h3 class="label mb-4">
                    <?php _e('En Números', 'maggiore'); ?>
                </h3>
                
                <div class="d-flex flex-column gap-2">
                    <!-- Miembros -->
                    <div class="card-mg d-inline-flex gap-2 align-items-center w-100">    
                    <div class="number-data px-2" style="min-width:40px">
                            <?= count($miembros_ids); ?>
                        </div>
                        <p class="text-muted  pt-3" >
                            <?php echo _n('Miembro', 'Miembros', count($miembros_ids), 'maggiore'); ?>
                </p></div>
                    
                       <div class="card-mg d-inline-flex gap-2 align-items-center w-100">    
                    <div class="number-data  px-2" style="min-width:40px">
                            <?= count($servicios); ?>
                        </div>
                        <p class="text-muted  pt-3" >
                            <?php echo _n('Servicios', 'Servicios', count($servicios), 'maggiore'); ?>
                </p></div>           

                <div class="card-mg d-inline-flex gap-2 align-items-center w-100">    
                    <div class="number-data  px-2" style="min-width:40px">
                            <?= $total_proyectos; ?>
                        </div>
                        <p class="text-muted  pt-3" >
                            <?php _e('Proyectos Registrados', 'maggiore'); ?>
                </p></div>  
               
                </div>
            </div>
        </div>


    </div>

    <!-- ===============================
         EQUIPO DEL ÁREA (POR CATEGORÍA)
         =============================== -->
    <?php if (!empty($equipo_por_categoria)): ?>
        <section class="mb-5">
            <div class="feature-name-2 mb-2">
                <h2>
                    <?php printf(__('Equipo de %s', 'maggiore'), get_the_title()); ?>
                </h2>
            </div>
            
            <?php foreach ($equipo_por_categoria as $categoria_data): 
                $categoria = $categoria_data['term'];
                $miembros_categoria = $categoria_data['miembros'];
            ?>
                <div class="mb-2 card-mg">
                    <div >
                        <h3 class="label">
                            <?= esc_html($categoria->name); ?>
                        </h3>
                        <?php if (!empty($categoria->description)): ?>
                            <p class="text-muted mb-2" style="font-size: 0.95rem;">
                                <?= esc_html($categoria->description); ?>
                            </p>
                        <?php endif; ?>
                    </div>
                       
                    <div class="row row-cols-1 row-cols-md-2 row-cols-lg-5   g-2">
                        <?php foreach ($miembros_categoria as $post): 
                            setup_postdata($post);
                        ?>
                            <div class="col">
                                <?php get_template_part('template-parts/card', 'equipo'); ?>
                            </div>
                        <?php endforeach; 
                        wp_reset_postdata();
                        ?>
                    </div>
             </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <!-- ===============================
         SERVICIOS DEL ÁREA (TAGS)
         =============================== -->
    <?php if (!empty($servicios)): ?>
        <section class="mb-5">
            <div class="feature-name-2 mb-2">
                <h2>
                    <?php _e('Servicios que Ofrece esta Área', 'maggiore'); ?>
                </h2>
            </div>
            
            <div class="card-mg">
                <ul class="services-tags">
                    <?php foreach ($servicios as $servicio): ?>
                        <li>
                            <a class="service-tag" href="<?= esc_url(get_permalink($servicio->ID)); ?>">
                                <?= esc_html($servicio->post_title); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
    <?php endif; ?>

    <!-- ===============================
         CASOS DE ÉXITO DESTACADOS
         =============================== -->
    <?php if (!empty($casos_destacados)): ?>
        <section class="mb-5">
            <div class="feature-name-2 mb-2">
                <h2>
                    <?php _e('Casos de Éxito Destacados', 'maggiore'); ?>
                </h2>
               
            </div> <p class="text-muted mb-3">
                    <?php printf(__('Proyectos exitosos realizados por el equipo de %s', 'maggiore'), get_the_title()); ?>
                </p>
            
            <div class="row g-2">
                <?php foreach ($casos_destacados as $post): 
                    setup_postdata($post); 
                ?>
                    <div class="col-12 mt-0">
                        <?php get_template_part('template-parts/card', 'caso-exito'); ?>
                    </div>
                <?php endforeach; 
                wp_reset_postdata(); 
                ?>
            </div>
            
            <?php if (count($casos_ids) > 3): ?>
                <div class="text-center mt-4">
                    <p class="text-muted mb-0">
                        <?php printf(
                            __('El área tiene %d casos de éxito en total', 'maggiore'),
                            count($casos_ids)
                        ); ?>
                    </p>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <!-- ===============================
         PORTAFOLIO DESTACADO
         =============================== -->
    <?php if (!empty($portafolios_destacados)): ?>
        <section class="mb-5">
            <div class="feature-name-2 mb-2">
                <h2>
                    <?php _e('Portafolio Destacado', 'maggiore'); ?>
                </h2>
               
            </div>
             <p class="text-muted mb-3">
                    <?php printf(__('Trabajos realizados por el equipo de %s', 'maggiore'), get_the_title()); ?>
                </p>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-2">
                <?php foreach ($portafolios_destacados as $post): 
                    setup_postdata($post); 
                ?>
                    <div class="col">
                        <?php get_template_part('template-parts/card', 'portafolio'); ?>
                    </div>
                <?php endforeach; 
                wp_reset_postdata(); 
                ?>
            </div>
            
            <?php if (count($portafolios_ids) > 6): ?>
                <div class="text-center mt-4">
                    <p class="text-muted mb-0">
                        <?php printf(
                            __('El área tiene %d proyectos en portafolio en total', 'maggiore'),
                            count($portafolios_ids)
                        ); ?>
                    </p>
                </div>
            <?php endif; ?>
        </section>
    <?php endif; ?>

    <!-- ===============================
         NAVEGACIÓN PREV/NEXT
         =============================== -->
    <nav class="mt-5 pt-5">
        <div class="row">
            <div class="col-md-6">
                <?php
                $prev_post = get_previous_post();
                if ($prev_post): ?>
                    <a href="<?= get_permalink($prev_post); ?>">
                        <small class="tag-flecha"> <i class="bi bi-chevron-left"></i>                            
                        <?php _e(' Área anterior', 'maggiore'); ?>
                </small>
                        <p style="color: white;"><?= esc_html($prev_post->post_title); ?></p>
                    </a>
                <?php endif; ?>
            </div>
            <div class="col-md-6 text-md-end">
                <?php
                $next_post = get_next_post();
                if ($next_post): ?>
                    <a href="<?= get_permalink($next_post); ?>">
                        <small class="tag-flecha next-flecha">
                         <?php _e('Siguiente área ', 'maggiore'); ?>
                            <i class="bi bi-chevron-right"></i> </small>
                        <i></i>
                        <p style="color: white;"><?= esc_html($next_post->post_title); ?></p>
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </nav>



</main>

<?php get_footer(); ?>
